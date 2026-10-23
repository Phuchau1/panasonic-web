<?php
declare(strict_types=1);

namespace App\Models;

class Product extends BaseModel
{
    protected string $table = 'products';
    protected array $fillable = [
        'category_id', 'series_id', 'name', 'slug', 'sku', 'model_code', 'short_desc', 'description',
        'price', 'wholesale_price', 'wholesale_min_qty', 'stock', 'unit', 'thumbnail', 'gallery', 'specs',
        'rated_current', 'breaking_capacity', 'poles', 'pdf_catalog_url', 'is_featured', 'status', 'search_index',
    ];

    /** Các kiểu sắp xếp được phép (whitelist -> an toàn khi chèn ORDER BY) */
    private const SORTS = [
        'newest'     => 'p.created_at DESC, p.id DESC',
        'price_asc'  => 'p.price ASC',
        'price_desc' => 'p.price DESC',
        'name'       => 'p.name ASC',
        'popular'    => 'p.views DESC, p.is_featured DESC',
    ];

    private const BASE_SELECT = "SELECT p.*, c.name AS category_name, c.slug AS category_slug,
                                        s.name AS series_name, s.slug AS series_slug
                                 FROM products p
                                 INNER JOIN categories c ON c.id = p.category_id
                                 LEFT JOIN product_series s ON s.id = p.series_id";

    /**
     * Lọc đa tiêu chí.
     *
     * @param array{category?:string, series?:string, ampe?:array|string, ka?:array|string,
     *              poles?:array|string, price_min?:int|string, price_max?:int|string, q?:string, sort?:string} $f
     * @return array{items:array, total:int, page:int, pages:int, per_page:int}
     */
    public function filter(array $f, int $page = 1, int $perPage = 12): array
    {
        $where  = ['p.status = 1', 'c.status = 1'];
        $params = [];

        if (!empty($f['category'])) {
            $where[] = 'c.slug = :category';
            $params['category'] = (string) $f['category'];
        }
        if (!empty($f['series'])) {
            $where[] = 's.slug = :series';
            $params['series'] = (string) $f['series'];
        }

        $this->whereIn($where, $params, 'p.rated_current', 'ampe', $f['ampe'] ?? null, 'float');
        $this->whereIn($where, $params, 'p.breaking_capacity', 'ka', $f['ka'] ?? null, 'float');
        $this->whereIn($where, $params, 'p.poles', 'poles', $f['poles'] ?? null, 'int');

        if (isset($f['price_min']) && is_numeric($f['price_min'])) {
            $where[] = 'p.price >= :price_min';
            $params['price_min'] = (int) $f['price_min'];
        }
        if (isset($f['price_max']) && is_numeric($f['price_max']) && (int) $f['price_max'] > 0) {
            $where[] = 'p.price <= :price_max';
            $params['price_max'] = (int) $f['price_max'];
        }

        if (!empty($f['q'])) {
            $tokens = self::extractSearchTokens((string) $f['q']);
            if (!empty($tokens)) {
                $searchConditions = [];
                $qIdx = 0;
                foreach ($tokens as $t) {
                    $qIdx++;
                    $searchConditions[] = "(p.search_index LIKE :tok{$qIdx} OR p.name LIKE :tok_name{$qIdx} OR p.model_code LIKE :tok_code{$qIdx})";
                    $params["tok{$qIdx}"] = '%' . $this->escapeLike($t) . '%';
                    $params["tok_name{$qIdx}"] = '%' . $this->escapeLike($t) . '%';
                    $params["tok_code{$qIdx}"] = '%' . $this->escapeLike($t) . '%';
                }

                $noSpace = preg_replace('/[\s\-]+/', '', strip_vn_accents((string) $f['q']));
                if (preg_match('/^[a-z]+[0-9]+[a-z0-9]*$/i', $noSpace) && strlen($noSpace) >= 4) {
                    $params["tok_ns"] = '%' . $this->escapeLike($noSpace) . '%';
                    $params["tok_nsc"] = '%' . $this->escapeLike($noSpace) . '%';
                    $where[] = "((" . implode(' AND ', $searchConditions) . ") OR (p.search_index LIKE :tok_ns OR p.model_code LIKE :tok_nsc))";
                } else {
                    $where[] = '(' . implode(' AND ', $searchConditions) . ')';
                }
            }
        }

        $whereSql = implode(' AND ', $where);
        $orderSql = self::SORTS[$f['sort'] ?? 'newest'] ?? self::SORTS['newest'];

        $total = (int) $this->db->fetchColumn(
            "SELECT COUNT(*) FROM products p
             INNER JOIN categories c ON c.id = p.category_id
             LEFT JOIN product_series s ON s.id = p.series_id
             WHERE {$whereSql}",
            $params
        );

        $pages = max(1, (int) ceil($total / $perPage));
        $page  = min(max(1, $page), $pages);

        $items = $this->db->fetchAll(
            self::BASE_SELECT . " WHERE {$whereSql} ORDER BY {$orderSql} LIMIT :limit OFFSET :offset",
            $params + ['limit' => $perPage, 'offset' => ($page - 1) * $perPage]
        );

        return [
            'items'    => array_map([$this, 'decode'], $items),
            'total'    => $total,
            'page'     => $page,
            'pages'    => $pages,
            'per_page' => $perPage,
        ];
    }

    /**
     * Bóc tách từ khóa tìm kiếm thông minh:
     * - Bỏ các từ chung chung / gõ nhầm Telex (phaamrt, san pham, thiet bi, panasonic)
     * - Chuẩn hóa dấu tiếng Việt
     * - Tách token độc lập
     */
    public static function extractSearchTokens(string $keyword): array
    {
        $clean = mb_strtolower(trim($keyword));
        
        // Nếu người dùng chỉ gõ từ chung chung hoặc lỗi gõ phím ("phaamrt", "san pham", "panasonic", "thiet bi")
        if (preg_match('/^(phaamrt|phaamr|san pham|sản phẩm|thiet bi|thiết bị|panasonic|pana|do dien|đồ điện)$/iu', $clean)) {
            return []; // Không lọc từ khóa hạn chế -> hiển thị danh sách sản phẩm
        }

        $filtered = preg_replace('/(phaamrt|phaamr|san pham|sản phẩm)/iu', ' ', $clean);
        $filtered = preg_replace('/(^|\s)panasonic($|\s)/iu', ' ', $filtered);
        $filtered = preg_replace('/(^|\s)thiet bi($|\s)/iu', ' ', $filtered);

        if (trim($filtered) !== '') {
            $clean = $filtered;
        }

        $clean = str_replace([',', ';', ':', '!', '?', '(', ')', '[', ']', '"', "'", '“', '”', '`'], ' ', $clean);
        $unacc = strip_vn_accents($clean);
        $rawTokens = preg_split('/[\s]+/', trim($unacc), -1, PREG_SPLIT_NO_EMPTY);

        $stopWords = ['la', 'va', 'cua', 'cho', 'trong', 'cac', 'nhung', 'nhap', 'tim', 'kiem', 'hang'];
        $tokens = [];
        foreach ($rawTokens as $t) {
            if (mb_strlen($t) > 1 && !in_array($t, $stopWords, true)) {
                $tokens[] = $t;
            } elseif (mb_strlen($t) === 1 && ctype_digit($t)) {
                $tokens[] = $t;
            }
        }

        return array_values(array_unique($tokens));
    }

    /**
     * Live search thông minh đa năng theo mã Panasonic (WEV, BBD, Moderva, F-60...)
     * hoặc từ khóa tiếng Việt không dấu / có dấu, công suất, ampe, chức năng.
     */
    public function liveSearch(string $keyword, int $limit = 8): array
    {
        $keyword = mb_substr(trim($keyword), 0, 80);
        if (mb_strlen($keyword) < 1) {
            return [];
        }

        $tokens = self::extractSearchTokens($keyword);
        $escaped = $this->escapeLike(strip_vn_accents($keyword));
        $normalized = $this->escapeLike(strtoupper(preg_replace('/[\s\-]+/', '', $keyword)));

        $where = ['p.status = 1', 'c.status = 1'];
        $params = [];

        if (!empty($tokens)) {
            $searchConditions = [];
            $i = 0;
            foreach ($tokens as $t) {
                $i++;
                $searchConditions[] = "(p.search_index LIKE :t{$i} OR p.name LIKE :tn{$i} OR p.model_code LIKE :tc{$i})";
                $params["t{$i}"] = '%' . $this->escapeLike($t) . '%';
                $params["tn{$i}"] = '%' . $this->escapeLike($t) . '%';
                $params["tc{$i}"] = '%' . $this->escapeLike($t) . '%';
            }

            $noSpace = preg_replace('/[\s\-]+/', '', strip_vn_accents($keyword));
            if (preg_match('/^[a-z]+[0-9]+[a-z0-9]*$/i', $noSpace) && strlen($noSpace) >= 4) {
                $params['tok_ns'] = '%' . $this->escapeLike($noSpace) . '%';
                $params['tok_nsc'] = '%' . $this->escapeLike($noSpace) . '%';
                $where[] = "((" . implode(' AND ', $searchConditions) . ") OR (p.search_index LIKE :tok_ns OR p.model_code LIKE :tok_nsc))";
            } else {
                $where[] = '(' . implode(' AND ', $searchConditions) . ')';
            }
        }

        $whereSql = implode(' AND ', $where);

        $params['start1'] = $this->escapeLike($keyword) . '%';
        $params['norm1']  = $normalized . '%';
        $params['word1']  = '%' . $this->escapeLike($keyword) . '%';
        $params['wordVn'] = '%' . $escaped . '%';
        $params['limit']  = $limit;

        $rows = $this->db->fetchAll(
            "SELECT p.id, p.name, p.slug, p.model_code, p.price, p.wholesale_price, p.thumbnail, p.stock,
                    c.name AS category_name, s.name AS series_name,
                    CASE
                        WHEN p.model_code LIKE :start1 THEN 0
                        WHEN REPLACE(p.model_code, '-', '') LIKE :norm1 THEN 1
                        WHEN p.name LIKE :word1 THEN 2
                        WHEN p.name LIKE :wordVn THEN 3
                        ELSE 4
                    END AS relevance
             FROM products p
             INNER JOIN categories c ON c.id = p.category_id
             LEFT JOIN product_series s ON s.id = p.series_id
             WHERE {$whereSql}
             ORDER BY relevance ASC, p.is_featured DESC, p.id DESC
             LIMIT :limit",
            $params
        );

        return array_map(static fn(array $r) => [
            'id'         => (int) $r['id'],
            'name'       => $r['name'],
            'model_code' => $r['model_code'],
            'category'   => $r['category_name'],
            'series'     => $r['series_name'],
            'price'      => (int) $r['price'],
            'price_text' => money($r['price']),
            'in_stock'   => (int) $r['stock'] > 0,
            'thumbnail'  => media($r['thumbnail']),
            'url'        => url('/san-pham/' . $r['slug']),
        ], $rows);
    }

    public function findBySlug(string $slug): ?array
    {
        $row = $this->db->fetch(
            self::BASE_SELECT . ' WHERE p.slug = :slug AND p.status = 1 LIMIT 1',
            ['slug' => $slug]
        );
        return $row ? $this->decode($row) : null;
    }

    public function findWithRelations(int $id): ?array
    {
        $row = $this->db->fetch(self::BASE_SELECT . ' WHERE p.id = :id LIMIT 1', ['id' => $id]);
        return $row ? $this->decode($row) : null;
    }

    /**
     * @param int[] $ids
     */
    public function findMany(array $ids): array
    {
        $ids = array_values(array_unique(array_filter(array_map('intval', $ids))));
        if (!$ids) {
            return [];
        }
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $rows = $this->db->fetchAll(
            self::BASE_SELECT . " WHERE p.id IN ({$placeholders}) AND p.status = 1",
            $ids
        );
        $mapped = [];
        foreach ($rows as $row) {
            $mapped[(int) $row['id']] = $this->decode($row);
        }
        return $mapped;
    }

    public function featured(int $limit = 8): array
    {
        $rows = $this->db->fetchAll(
            self::BASE_SELECT . ' WHERE p.status = 1 AND p.is_featured = 1 ORDER BY p.id DESC LIMIT :limit',
            ['limit' => $limit]
        );
        return array_map([$this, 'decode'], $rows);
    }

    /**
     * Lấy danh sách sản phẩm theo danh mục hoặc từ khóa lọc chuyên biệt
     */
    public function getSection(string $type, int $limit = 5): array
    {
        $where = 'p.status = 1';
        $params = ['limit' => $limit];

        switch ($type) {
            case 'downlight':
                $where .= ' AND p.category_id = 3 AND (p.name LIKE :kw1 OR p.name LIKE :kw2)';
                $params['kw1'] = '%downlight%';
                $params['kw2'] = '%âm trần%';
                break;
            case 'lighting_other':
                $where .= ' AND p.category_id = 3 AND (p.name LIKE :kw1 OR p.name LIKE :kw2 OR p.name LIKE :kw3)';
                $params['kw1'] = '%đèn bàn%';
                $params['kw2'] = '%panel%';
                $params['kw3'] = '%gắn tường%';
                break;
            case 'filament':
                $where .= ' AND p.category_id = 3 AND (p.name LIKE :kw1 OR p.name LIKE :kw2)';
                $params['kw1'] = '%filament%';
                $params['kw2'] = '%peacock%';
                break;
            case 'switches':
                $where .= ' AND p.category_id = 1';
                break;
            case 'breakers':
                $where .= ' AND p.category_id = 2';
                break;
            case 'fans':
                $where .= ' AND p.category_id = 4';
                break;
            case 'industrial':
                $where .= ' AND p.category_id = 5';
                break;
            case 'pumps':
                $where .= ' AND p.category_id IN (6, 7, 8)';
                break;
            case 'cadivi':
                $where .= ' AND p.category_id = 9';
                break;
            default:
                $where .= ' AND p.category_id = ' . (int) $type;
                break;
        }

        $rows = $this->db->fetchAll(
            self::BASE_SELECT . " WHERE {$where} ORDER BY p.is_featured DESC, p.id DESC LIMIT :limit",
            $params
        );
        return array_map([$this, 'decode'], $rows);
    }

    public function related(array $product, int $limit = 4): array
    {
        $rows = $this->db->fetchAll(
            self::BASE_SELECT . ' WHERE p.status = 1 AND p.id <> :id
              AND (p.category_id = :cat OR (p.series_id IS NOT NULL AND p.series_id = :series))
              ORDER BY (p.series_id <=> :series2) DESC, p.is_featured DESC, p.id DESC
              LIMIT :limit',
            [
                'id'      => (int) $product['id'],
                'cat'     => (int) $product['category_id'],
                'series'  => (int) ($product['series_id'] ?? 0),
                'series2' => (int) ($product['series_id'] ?? 0),
                'limit'   => $limit,
            ]
        );
        return array_map([$this, 'decode'], $rows);
    }

    /**
     * MCB/RCBO dùng cho bảng tra cứu theo tải ampe.
     */
    public function breakers(): array
    {
        $rows = $this->db->fetchAll(
            self::BASE_SELECT . ' WHERE p.status = 1 AND p.rated_current IS NOT NULL AND p.breaking_capacity IS NOT NULL
              ORDER BY p.rated_current ASC, p.poles ASC'
        );
        return array_map([$this, 'decode'], $rows);
    }

    /**
     * Giá trị khả dụng cho bộ lọc kỹ thuật (kèm số lượng) – có thể giới hạn theo danh mục.
     */
    public function facets(?string $categorySlug = null): array
    {
        $join   = 'INNER JOIN categories c ON c.id = p.category_id';
        $where  = 'p.status = 1';
        $params = [];
        if ($categorySlug) {
            $where .= ' AND c.slug = :cat';
            $params['cat'] = $categorySlug;
        }

        $facet = fn(string $col) => $this->db->fetchAll(
            "SELECT p.{$col} AS value, COUNT(*) AS total FROM products p {$join}
             WHERE {$where} AND p.{$col} IS NOT NULL GROUP BY p.{$col} ORDER BY p.{$col} ASC",
            $params
        );

        $range = $this->db->fetch(
            "SELECT COALESCE(MIN(p.price),0) AS min_price, COALESCE(MAX(p.price),0) AS max_price
             FROM products p {$join} WHERE {$where}",
            $params
        );

        return [
            'ampe'  => $facet('rated_current'),
            'ka'    => $facet('breaking_capacity'),
            'poles' => $facet('poles'),
            'price' => $range,
        ];
    }

    public function incrementViews(int $id): void
    {
        $this->db->query('UPDATE products SET views = views + 1 WHERE id = :id', ['id' => $id]);
    }

    /**
     * Danh sách tài liệu PDF (gom theo file) cho trang Tải Catalogue.
     */
    public function catalogs(): array
    {
        return $this->db->fetchAll(
            "SELECT p.pdf_catalog_url AS url, c.name AS category_name, c.icon,
                    COUNT(*) AS product_count, GROUP_CONCAT(DISTINCT p.model_code ORDER BY p.model_code SEPARATOR ', ') AS models
             FROM products p
             INNER JOIN categories c ON c.id = p.category_id
             WHERE p.status = 1 AND p.pdf_catalog_url IS NOT NULL AND p.pdf_catalog_url <> ''
             GROUP BY p.pdf_catalog_url, c.name, c.icon
             ORDER BY c.name"
        );
    }

    public function adminList(string $keyword = '', int $categoryId = 0, int $seriesId = 0, int $page = 1, int $perPage = 20): array
    {
        $whereList = ['1=1'];
        $params = [];
        if ($keyword !== '') {
            $whereList[] = '(p.name LIKE :k1 OR p.model_code LIKE :k2 OR p.sku LIKE :k3)';
            $like  = '%' . $this->escapeLike($keyword) . '%';
            $params['k1'] = $like;
            $params['k2'] = $like;
            $params['k3'] = $like;
        }
        if ($categoryId > 0) {
            $whereList[] = 'p.category_id = :cat_id';
            $params['cat_id'] = $categoryId;
        }
        if ($seriesId > 0) {
            $whereList[] = 'p.series_id = :series_id';
            $params['series_id'] = $seriesId;
        }
        $where = implode(' AND ', $whereList);
        $total = (int) $this->db->fetchColumn(
            "SELECT COUNT(*) FROM products p WHERE {$where}",
            $params
        );
        $pages = max(1, (int) ceil($total / $perPage));
        $page  = min(max(1, $page), $pages);

        $items = $this->db->fetchAll(
            self::BASE_SELECT . " WHERE {$where} ORDER BY p.id DESC LIMIT :limit OFFSET :offset",
            $params + ['limit' => $perPage, 'offset' => ($page - 1) * $perPage]
        );
        return ['items' => $items, 'total' => $total, 'page' => $page, 'pages' => $pages];
    }

    public function slugExists(string $slug, int $exceptId = 0): bool
    {
        return (bool) $this->db->fetchColumn(
            'SELECT COUNT(*) FROM products WHERE slug = :slug AND id <> :id',
            ['slug' => $slug, 'id' => $exceptId]
        );
    }

    public function skuExists(string $sku, int $exceptId = 0): bool
    {
        return (bool) $this->db->fetchColumn(
            'SELECT COUNT(*) FROM products WHERE sku = :sku AND id <> :id',
            ['sku' => $sku, 'id' => $exceptId]
        );
    }

    /**
     * Giải mã các cột JSON và ép kiểu số.
     */
    public function decode(array $row): array
    {
        $row['specs']   = $this->jsonArray($row['specs'] ?? null);
        $row['gallery'] = $this->jsonArray($row['gallery'] ?? null);
        if (!$row['gallery'] && !empty($row['thumbnail'])) {
            $row['gallery'] = [$row['thumbnail']];
        }
        $row['price']             = (int) $row['price'];
        $row['wholesale_price']   = (int) $row['wholesale_price'];
        $row['wholesale_min_qty'] = max(1, (int) ($row['wholesale_min_qty'] ?? 10));
        $row['stock']             = (int) $row['stock'];
        $row['discount_percent']  = ($row['price'] > 0 && $row['wholesale_price'] > 0)
            ? (int) round(100 - $row['wholesale_price'] / $row['price'] * 100)
            : 0;
        return $row;
    }

    private function jsonArray(mixed $value): array
    {
        if (is_array($value)) {
            return $value;
        }
        if (!is_string($value) || $value === '') {
            return [];
        }
        $decoded = json_decode($value, true);
        return is_array($decoded) ? $decoded : [];
    }

    /**
     * Thêm điều kiện IN an toàn cho giá trị đơn hoặc mảng (ampe[]=16&ampe[]=20).
     */
    private function whereIn(array &$where, array &$params, string $column, string $key, mixed $values, string $type): void
    {
        if ($values === null || $values === '' || $values === []) {
            return;
        }
        $values = array_slice((array) $values, 0, 20);
        $clean  = [];
        foreach ($values as $v) {
            if (is_numeric($v)) {
                $clean[] = $type === 'int' ? (int) $v : (string) (float) $v;
            }
        }
        if (!$clean) {
            return;
        }
        $holders = [];
        foreach (array_values($clean) as $i => $v) {
            $name = "{$key}_{$i}";
            $holders[] = ':' . $name;
            $params[$name] = $v;
        }
        $where[] = "{$column} IN (" . implode(',', $holders) . ')';
    }

    private function escapeLike(string $value): string
    {
        return addcslashes($value, '\\%_');
    }
}
