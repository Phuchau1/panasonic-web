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
    ];

    public const STATUSES = [
        'pending'   => 'Chờ xác nhận',
        'confirmed' => 'Đã xác nhận',
        'shipping'  => 'Đang giao hàng',
        'completed' => 'Hoàn thành',
        'cancelled' => 'Đã huỷ',
    ];

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
