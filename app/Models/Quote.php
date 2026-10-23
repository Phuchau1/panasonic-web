<?php
declare(strict_types=1);

namespace App\Models;

class Quote extends BaseModel
{
    protected string $table = 'quotes_requests';
    protected array $fillable = [
        'customer_name', 'company', 'customer_type', 'phone', 'email', 'project_name',
        'address', 'file_bom_path', 'file_original', 'note', 'status', 'ip_address',
    ];

    public const STATUSES = [
        'new'        => 'Mới tiếp nhận',
        'processing' => 'Đang bóc tách',
        'quoted'     => 'Đã gửi báo giá',
        'closed'     => 'Đã đóng',
    ];

    public const CUSTOMER_TYPES = [
        'contractor' => 'Nhà thầu M&E / Thợ điện',
        'dealer'     => 'Đại lý / Cửa hàng điện',
        'investor'   => 'Chủ đầu tư / Ban QLDA',
        'retail'     => 'Khách lẻ / Hộ gia đình',
    ];

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
            "SELECT * FROM quotes_requests WHERE {$where} ORDER BY created_at DESC, id DESC LIMIT :limit OFFSET :offset",
            $params + ['limit' => $perPage, 'offset' => ($page - 1) * $perPage]
        );
        return compact('items', 'total', 'page', 'pages');
    }

    public function updateStatus(int $id, string $status): bool
    {
        if (!isset(self::STATUSES[$status])) {
            return false;
        }
        return $this->update($id, ['status' => $status]);
    }

    /**
     * Chống spam: đếm số yêu cầu từ một IP trong N phút gần nhất.
     */
    public function recentCountByIp(string $ip, int $minutes = 10): int
    {
        return (int) $this->db->fetchColumn(
            'SELECT COUNT(*) FROM quotes_requests WHERE ip_address = :ip AND created_at >= (NOW() - INTERVAL :m MINUTE)',
            ['ip' => $ip, 'm' => $minutes]
        );
    }
}
