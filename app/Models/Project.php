<?php
declare(strict_types=1);

namespace App\Models;

class Project extends BaseModel
{
    protected string $table = 'projects';
    protected array $fillable = [
        'title', 'slug', 'client_name', 'location', 'scale', 'category', 'supplied_items', 'thumbnail', 'gallery', 'description', 'content', 'completion_year', 'is_featured', 'sort_order', 'status',
    ];

    public function featured(int $limit = 6): array
    {
        return $this->db->fetchAll(
            "SELECT * FROM projects WHERE status = 1 ORDER BY sort_order ASC, id DESC LIMIT :limit",
            ['limit' => $limit]
        );
    }

    public function findBySlug(string $slug): ?array
    {
        return $this->db->fetch(
            "SELECT * FROM projects WHERE slug = :slug AND status = 1 LIMIT 1",
            ['slug' => $slug]
        );
    }

    public function paginate(string $keyword = '', int $page = 1, int $perPage = 10): array
    {
        $where = ['1=1'];
        $params = [];

        if ($keyword !== '') {
            $where[] = '(title LIKE :kw OR client_name LIKE :kw OR location LIKE :kw OR category LIKE :kw)';
            $params['kw'] = "%{$keyword}%";
        }

        $whereClause = implode(' AND ', $where);
        $total = (int) $this->db->fetchColumn("SELECT COUNT(*) FROM `{$this->table}` WHERE {$whereClause}", $params);

        $pages = max(1, (int) ceil($total / $perPage));
        $page = max(1, min($page, $pages));
        $offset = ($page - 1) * $perPage;

        $items = $this->db->fetchAll(
            "SELECT * FROM `{$this->table}` WHERE {$whereClause} ORDER BY sort_order ASC, id DESC LIMIT {$perPage} OFFSET {$offset}",
            $params
        );

        return [
            'items' => $items,
            'total' => $total,
            'page'  => $page,
            'pages' => $pages,
        ];
    }
}
