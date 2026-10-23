<?php
declare(strict_types=1);

namespace App\Models;

class Category extends BaseModel
{
    protected string $table = 'categories';
    protected array $fillable = ['name', 'slug', 'icon', 'description', 'sort_order', 'status'];

    /**
     * Danh mục đang hoạt động kèm số lượng sản phẩm.
     */
    public function active(): array
    {
        return $this->db->fetchAll(
            "SELECT c.*, COUNT(p.id) AS product_count
             FROM categories c
             LEFT JOIN products p ON p.category_id = c.id AND p.status = 1
             WHERE c.status = 1
             GROUP BY c.id
             ORDER BY c.sort_order ASC, c.name ASC"
        );
    }

    public function findActiveBySlug(string $slug): ?array
    {
        return $this->db->fetch(
            'SELECT * FROM categories WHERE slug = :slug AND status = 1 LIMIT 1',
            ['slug' => $slug]
        );
    }
}
