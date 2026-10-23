<?php
declare(strict_types=1);

namespace App\Models;

class ProductSeries extends BaseModel
{
    protected string $table = 'product_series';
    protected array $fillable = ['name', 'slug', 'tagline', 'color', 'catalog_url', 'sort_order', 'status'];

    public function active(): array
    {
        return $this->db->fetchAll(
            "SELECT s.*, COUNT(p.id) AS product_count
             FROM product_series s
             LEFT JOIN products p ON p.series_id = s.id AND p.status = 1
             WHERE s.status = 1
             GROUP BY s.id
             ORDER BY s.sort_order ASC"
        );
    }

    public function findActiveBySlug(string $slug): ?array
    {
        return $this->db->fetch(
            'SELECT * FROM product_series WHERE slug = :slug AND status = 1 LIMIT 1',
            ['slug' => $slug]
        );
    }
}
