<?php
declare(strict_types=1);

namespace App\Models;

class Contact extends BaseModel
{
    protected string $table = 'contacts';
    protected array $fillable = [
        'name', 'phone', 'email', 'address', 'message', 'status', 'note', 'ip_address'
    ];

    public const STATUSES = [
        'new'       => 'Mới tiếp nhận',
        'contacted' => 'Đã liên hệ tư vấn',
        'closed'    => 'Đã xử lý xong',
    ];

    public function paginate(string $status = '', string $search = '', int $page = 1, int $perPage = 20): array
    {
        $where  = '1=1';
        $params = [];

        if (isset(self::STATUSES[$status])) {
            $where .= ' AND status = :status';
            $params['status'] = $status;
        }

        if ($search !== '') {
            $where .= ' AND (name LIKE :q OR phone LIKE :q OR email LIKE :q OR message LIKE :q)';
            $params['q'] = '%' . $search . '%';
        }

        $total = $this->count($where, $params);
        $pages = max(1, (int) ceil($total / $perPage));
        $page  = min(max(1, $page), $pages);

        $items = $this->db->fetchAll(
            "SELECT * FROM {$this->table} WHERE {$where} ORDER BY created_at DESC, id DESC LIMIT :limit OFFSET :offset",
            $params + ['limit' => $perPage, 'offset' => ($page - 1) * $perPage]
        );
        return compact('items', 'total', 'page', 'pages');
    }

    public function updateStatus(int $id, string $status, string $note = ''): bool
    {
        if (!isset(self::STATUSES[$status])) {
            return false;
        }
        $data = ['status' => $status];
        if ($note !== '') {
            $data['note'] = $note;
        }
        return $this->update($id, $data);
    }
}
