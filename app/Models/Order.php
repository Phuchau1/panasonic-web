<?php
declare(strict_types=1);

namespace App\Models;

use Core\Database;

class Order extends BaseModel
{
    protected string $table = 'orders';
    protected array $fillable = [
        'user_id', 'order_code', 'customer_name', 'phone', 'email', 'company', 'tax_code', 'address',
        'province', 'district', 'ward', 'note',
        'order_type', 'subtotal', 'discount', 'total', 'payment_method', 'payment_status', 'status',
        'shipping_carrier', 'tracking_code', 'shipping_fee', 'shipping_note', 'shipped_at', 'delivered_at',
    ];

    public const STATUSES = [
        'pending'   => 'Chờ xác nhận',
        'confirmed' => 'Đã xác nhận',
        'shipping'  => 'Đang giao hàng',
        'completed' => 'Hoàn thành',
        'cancelled' => 'Đã huỷ',
    ];

    public const CARRIERS = [
        'ghtk' => [
            'name'         => 'Giao Hàng Tiết Kiệm (GHTK)',
            'short_name'   => 'GHTK',
            'color'        => '#059669',
            'bg'           => '#ecfdf5',
            'hotline'      => '1900 6092',
            'tracking_url' => 'https://khachhang.giaohangtietkiem.vn/khach-hang/tra-cuu-van-don?order_code={code}',
            'prefix'       => 'GHTK',
        ],
        'ghn' => [
            'name'         => 'Giao Hàng Nhanh (GHN Express)',
            'short_name'   => 'GHN',
            'color'        => '#ea580c',
            'bg'           => '#fff7ed',
            'hotline'      => '1900 636677',
            'tracking_url' => 'https://donhang.ghn.vn/?order_code={code}',
            'prefix'       => 'GHN',
        ],
        'viettel_post' => [
            'name'         => 'Viettel Post',
            'short_name'   => 'Viettel Post',
            'color'        => '#dc2626',
            'bg'           => '#fef2f2',
            'hotline'      => '1900 8095',
            'tracking_url' => 'https://viettelpost.com.vn/tra-cuu-hanh-trinh-don-hang?code={code}',
            'prefix'       => 'VTP',
        ],
        'vnpost' => [
            'name'         => 'Bưu Điện Việt Nam (VNPost)',
            'short_name'   => 'VNPost',
            'color'        => '#d97706',
            'bg'           => '#fffbeb',
            'hotline'      => '1900 545481',
            'tracking_url' => 'http://www.vnpost.vn/vi-vn/dinh-vi/buu-pham?key={code}',
            'prefix'       => 'VNPOST',
        ],
        'ahamove' => [
            'name'         => 'Ahamove / Giao Hỏa Tốc 2H (Nội thành)',
            'short_name'   => 'Ahamove Hỏa Tốc',
            'color'        => '#c2410c',
            'bg'           => '#fff7ed',
            'hotline'      => '1900 545411',
            'tracking_url' => '',
            'prefix'       => 'AHA',
        ],
        'panasonic_truck' => [
            'name'         => 'Xe Tải Phân Phối Kho Panasonic (Công trình)',
            'short_name'   => 'Xe Tải Panasonic',
            'color'        => '#004098',
            'bg'           => '#eff6ff',
            'hotline'      => '0798 284 321',
            'tracking_url' => '',
            'prefix'       => 'PNS-TRUCK',
        ],
    ];

    public static function getTrackingUrl(?string $carrierKey, ?string $trackingCode): ?string
    {
        if (empty($carrierKey) || empty($trackingCode)) {
            return null;
        }
        $carrier = self::CARRIERS[$carrierKey] ?? null;
        if (!$carrier || empty($carrier['tracking_url'])) {
            return null;
        }
        return str_replace('{code}', urlencode($trackingCode), $carrier['tracking_url']);
    }

    public static function getCarrierInfo(?string $carrierKey): ?array
    {
        if (empty($carrierKey)) {
            return null;
        }
        return self::CARRIERS[$carrierKey] ?? null;
    }

    public const FLOW_LEVELS = [
        'pending'   => 1,
        'confirmed' => 2,
        'shipping'  => 3,
        'completed' => 4,
        'cancelled' => 99,
    ];

    /**
     * Danh sách các trạng thái được phép TIẾN từ trạng thái hiện tại (chỉ được tiến, không được lùi).
     */
    public static function allowedNextStatuses(string $currentStatus): array
    {
        return match ($currentStatus) {
            'pending'   => [
                'confirmed' => 'Đã xác nhận',
                'shipping'  => 'Đang giao hàng',
                'completed' => 'Hoàn thành',
                'cancelled' => 'Đã huỷ',
            ],
            'confirmed' => [
                'shipping'  => 'Đang giao hàng',
                'completed' => 'Hoàn thành',
                'cancelled' => 'Đã huỷ',
            ],
            'shipping'  => [
                'completed' => 'Hoàn thành',
                'cancelled' => 'Đã huỷ',
            ],
            'completed' => [], // Đã hoàn thành: Khóa quy trình, không được phép chuyển lùi
            'cancelled' => [], // Đã huỷ: Khóa quy trình, không được phép chuyển lùi
            default     => [],
        };
    }

    /**
     * Kiểm tra chuyển trạng thái có hợp lệ theo chiều TIẾN hay không.
     */
    public static function canTransition(string $currentStatus, string $newStatus): bool
    {
        if ($currentStatus === $newStatus) {
            return true;
        }
        $allowed = self::allowedNextStatuses($currentStatus);
        return array_key_exists($newStatus, $allowed);
    }

    /**
     * Tạo đơn hàng + chi tiết + trừ tồn kho trong một transaction.
     *
     * @param array $customer Thông tin khách hàng
     * @param array $summary  Kết quả từ CartController::summarize()
     */
    public function place(array $customer, array $summary): string
    {
        return $this->db->transaction(function (Database $db) use ($customer, $summary): string {
            $code = $this->generateCode();

            $orderId = $this->create($customer + [
                'order_code' => $code,
                'order_type' => $summary['has_wholesale'] ? 'project' : 'retail',
                'subtotal'   => $summary['subtotal'],
                'discount'   => $summary['discount'],
                'total'      => $summary['total'],
                'status'     => 'pending',
            ]);

            foreach ($summary['lines'] as $line) {
                // Khoá dòng & kiểm tra tồn kho tránh bán vượt (race condition)
                $stock = $db->fetchColumn('SELECT stock FROM products WHERE id = :id FOR UPDATE', ['id' => $line['id']]);
                if ($stock === false || (int) $stock < $line['qty']) {
                    throw new \DomainException("Sản phẩm {$line['model_code']} không đủ tồn kho.");
                }

                $db->query(
                    'INSERT INTO order_items (order_id, product_id, product_name, model_code, price_type, unit_price, quantity, line_total)
                     VALUES (:order_id, :product_id, :name, :model, :ptype, :price, :qty, :total)',
                    [
                        'order_id'   => $orderId,
                        'product_id' => $line['id'],
                        'name'       => $line['name'],
                        'model'      => $line['model_code'],
                        'ptype'      => $line['price_type'],
                        'price'      => $line['unit_price'],
                        'qty'        => $line['qty'],
                        'total'      => $line['line_total'],
                    ]
                );

                $db->query('UPDATE products SET stock = stock - :qty WHERE id = :id', [
                    'qty' => $line['qty'],
                    'id'  => $line['id'],
                ]);
            }

            return $code;
        });
    }

    public function findByCode(string $code): ?array
    {
        return $this->db->fetch('SELECT * FROM orders WHERE order_code = :code LIMIT 1', ['code' => $code]);
    }

    public function findByTrackingOrCode(string $query): ?array
    {
        $clean = trim($query);
        if ($clean === '') {
            return null;
        }
        return $this->db->fetch(
            "SELECT * FROM `{$this->table}` WHERE `order_code` = :q OR `tracking_code` = :q OR `phone` = :q ORDER BY `id` DESC LIMIT 1",
            ['q' => $clean]
        );
    }

    public function items(int $orderId): array
    {
        return $this->db->fetchAll('SELECT oi.*, p.slug as product_slug FROM order_items oi LEFT JOIN products p ON oi.product_id = p.id WHERE oi.order_id = :id ORDER BY oi.id', ['id' => $orderId]);
    }

    public function paginate(string $status = '', int $page = 1, int $perPage = 20): array
    {
        $where  = '1=1';
        $params = [];
        if (isset(self::STATUSES[$status])) {
            $where = 'status = :status';
            $params['status'] = $status;
        }
        $total = $this->count($where, $params);
        $pages = max(1, (int) ceil($total / $perPage));
        $page  = min(max(1, $page), $pages);
        $items = $this->db->fetchAll(
            "SELECT * FROM orders WHERE {$where} ORDER BY id DESC LIMIT :limit OFFSET :offset",
            $params + ['limit' => $perPage, 'offset' => ($page - 1) * $perPage]
        );
        return compact('items', 'total', 'page', 'pages');
    }

    public function revenue(): int
    {
        return (int) $this->db->fetchColumn("SELECT COALESCE(SUM(total),0) FROM orders WHERE status <> 'cancelled'");
    }

    public function updatePaymentStatus(string $code, string $paymentStatus): bool
    {
        $this->db->query(
            "UPDATE `{$this->table}` SET `payment_status` = :pstatus, `status` = CASE WHEN `status` = 'pending' THEN 'confirmed' ELSE `status` END WHERE `order_code` = :code",
            ['pstatus' => $paymentStatus, 'code' => $code]
        );
        return true;
    }

    private function generateCode(): string
    {
        do {
            $code = 'PN' . date('ymd') . strtoupper(substr(bin2hex(random_bytes(3)), 0, 5));
        } while ($this->findByCode($code));
        return $code;
    }
}
