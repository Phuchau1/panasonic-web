<?php
declare(strict_types=1);

namespace App\Services;

use App\Models\Product;

/**
 * Giỏ hàng lưu trong session: [product_id => qty].
 * Quy tắc giá: số lượng >= wholesale_min_qty của sản phẩm -> áp dụng giá công trình (wholesale_price).
 */
final class Cart
{
    private const KEY = 'cart';
    public const MAX_QTY = 9999;

    public function items(): array
    {
        if (!isset($_SESSION[self::KEY])) {
            // Khôi phục từ cookie nếu có
            if (!empty($_COOKIE['panas_cart'])) {
                $decoded = json_decode((string)$_COOKIE['panas_cart'], true);
                if (is_array($decoded)) {
                    $_SESSION[self::KEY] = $decoded;
                }
            }
            // Khôi phục từ database nếu đã đăng nhập
            if (empty($_SESSION[self::KEY]) && !empty($_SESSION['user']['id'])) {
                $u = (new \App\Models\User())->find((int)$_SESSION['user']['id']);
                if (!empty($u['cart_data'])) {
                    $dbCart = json_decode((string)$u['cart_data'], true);
                    if (is_array($dbCart)) {
                        $_SESSION[self::KEY] = $dbCart;
                    }
                }
            }
        }
        return $_SESSION[self::KEY] ?? [];
    }

    private function persist(): void
    {
        $items = $_SESSION[self::KEY] ?? [];
        $json = json_encode($items);

        // 1. Lưu vĩnh viễn vào Cookie (30 ngày)
        $expire = time() + (30 * 24 * 3600);
        $base = base_path() ?: '/';
        setcookie('panas_cart', $json, $expire, $base, '', false, false);

        // 2. Lưu vào Database nếu user đã đăng nhập
        if (!empty($_SESSION['user']['id'])) {
            try {
                (new \App\Models\User())->update((int)$_SESSION['user']['id'], [
                    'cart_data' => $json
                ]);
            } catch (\Throwable $e) {
                // Ignore DB error
            }
        }
    }

    public function add(int $productId, int $qty): void
    {
        $items = $this->items();
        $current = (int) ($items[$productId] ?? 0);
        $_SESSION[self::KEY][$productId] = min(self::MAX_QTY, $current + max(1, $qty));
        $this->persist();
    }

    public function set(int $productId, int $qty): void
    {
        if ($qty <= 0) {
            $this->remove($productId);
            return;
        }
        $this->items();
        $_SESSION[self::KEY][$productId] = min(self::MAX_QTY, $qty);
        $this->persist();
    }

    public function remove(int $productId): void
    {
        $this->items();
        unset($_SESSION[self::KEY][$productId]);
        $this->persist();
    }

    public function clear(): void
    {
        unset($_SESSION[self::KEY]);
        $base = base_path() ?: '/';
        setcookie('panas_cart', '', time() - 3600, $base);
        if (!empty($_SESSION['user']['id'])) {
            try {
                (new \App\Models\User())->update((int)$_SESSION['user']['id'], ['cart_data' => null]);
            } catch (\Throwable $e) {}
        }
    }

    public function count(): int
    {
        return array_sum(array_map('intval', $this->items()));
    }

    /**
     * Tính toán chi tiết giỏ hàng từ dữ liệu DB (không tin giá từ client).
     */
    public function summarize(): array
    {
        $items    = $this->items();
        $products = (new Product())->findMany(array_keys($items));

        $lines = [];
        $subtotal = $total = 0;
        $hasWholesale = false;

        foreach ($items as $id => $qty) {
            $id = (int) $id;
            if (!isset($products[$id])) {
                // Sản phẩm đã ngừng kinh doanh -> loại khỏi giỏ
                $this->remove($id);
                continue;
            }
            $p   = $products[$id];
            $qty = max(1, (int) $qty);

            $isWholesale = $p['wholesale_price'] > 0 && $qty >= $p['wholesale_min_qty'];
            $unit        = $isWholesale ? $p['wholesale_price'] : $p['price'];
            $lineTotal   = $unit * $qty;
            $hasWholesale = $hasWholesale || $isWholesale;

            $subtotal += $p['price'] * $qty;
            $total    += $lineTotal;

            $lines[] = [
                'id'                => $id,
                'name'              => $p['name'],
                'slug'              => $p['slug'],
                'model_code'        => $p['model_code'],
                'thumbnail'         => $p['thumbnail'],
                'unit'              => $p['unit'],
                'stock'             => $p['stock'],
                'qty'               => $qty,
                'retail_price'      => $p['price'],
                'wholesale_price'   => $p['wholesale_price'],
                'wholesale_min_qty' => $p['wholesale_min_qty'],
                'unit_price'        => $unit,
                'price_type'        => $isWholesale ? 'wholesale' : 'retail',
                'line_total'        => $lineTotal,
                'need_more'         => $isWholesale ? 0 : max(0, $p['wholesale_min_qty'] - $qty),
            ];
        }

        return [
            'lines'         => $lines,
            'count'         => array_sum(array_column($lines, 'qty')),
            'subtotal'      => $subtotal,
            'discount'      => $subtotal - $total,
            'total'         => $total,
            'has_wholesale' => $hasWholesale,
        ];
    }
}
