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

    public function __construct()
    {
        parent::__construct();
        $this->ensureTable();
    }

    public function ensureTable(): void
    {
        static $ensured = false;
        if ($ensured) {
            return;
        }
        $ensured = true;

        try {
            $this->db->query("
                CREATE TABLE IF NOT EXISTS `{$this->table}` (
                  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                  `name` varchar(150) NOT NULL,
                  `phone` varchar(30) NOT NULL,
                  `email` varchar(150) DEFAULT NULL,
                  `address` varchar(255) DEFAULT NULL,
                  `message` text NOT NULL,
                  `status` enum('new','contacted','closed') NOT NULL DEFAULT 'new',
                  `note` text DEFAULT NULL,
                  `ip_address` varchar(45) DEFAULT NULL,
                  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                  PRIMARY KEY (`id`),
                  KEY `idx_contacts_status` (`status`),
                  KEY `idx_contacts_phone` (`phone`),
                  KEY `idx_contacts_created` (`created_at`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
            ");
        } catch (\Throwable $e) {
            error_log('[Contact ensureTable] ' . $e->getMessage());
        }
    }

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
