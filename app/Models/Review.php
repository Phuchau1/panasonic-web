<?php
declare(strict_types=1);

namespace App\Models;

class Review extends BaseModel
{
    protected string $table = 'reviews';
    protected array $fillable = [
        'product_id', 'order_id', 'author_name', 'author_phone', 'rating', 'title', 'comment', 'admin_reply', 'admin_reply_at', 'is_verified_buyer', 'likes_count', 'status',
    ];

    /**
     * Lấy danh sách đánh giá của sản phẩm (chỉ lấy review status = 1)
     */
    public function getByProduct(int $productId, int $limit = 20): array
    {
        return $this->db->fetchAll(
            "SELECT * FROM reviews WHERE product_id = :pid AND status = 1 ORDER BY created_at DESC, id DESC LIMIT :limit",
            ['pid' => $productId, 'limit' => $limit]
        );
    }

    /**
     * Thống kê sao của sản phẩm (Trung bình sao, tổng đánh giá, số lượng từng mức 1-5 sao)
     */
    public function getStats(int $productId): array
    {
        $rows = $this->db->fetchAll(
            "SELECT rating, COUNT(*) as count FROM reviews WHERE product_id = :pid AND status = 1 GROUP BY rating",
            ['pid' => $productId]
        );

        $counts = [5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0];
        $total = 0;
        $sum = 0;

        foreach ($rows as $r) {
            $star = (int) $r['rating'];
            $cnt = (int) $r['count'];
            if (isset($counts[$star])) {
                $counts[$star] = $cnt;
            }
            $total += $cnt;
            $sum += $star * $cnt;
        }

        $avg = $total > 0 ? round($sum / $total, 1) : 5.0;

        return [
            'total'   => $total,
            'avg'     => $avg,
            'counts'  => $counts,
            'percent' => array_map(fn($c) => $total > 0 ? round(($c / $total) * 100) : 0, $counts)
        ];
    }

    /**
     * Thêm đánh giá mới và tự động cập nhật thống kê sản phẩm
     */
    public function addReview(array $data): int
    {
        $id = $this->create([
            'product_id'        => (int) $data['product_id'],
            'author_name'       => trim($data['author_name']),
            'author_phone'      => trim($data['author_phone'] ?? ''),
            'rating'            => max(1, min(5, (int) ($data['rating'] ?? 5))),
            'title'             => trim($data['title'] ?? 'Đánh giá sản phẩm'),
            'comment'           => trim($data['comment']),
            'is_verified_buyer' => 1,
            'likes_count'       => 0,
            'status'            => 1,
        ]);

        // Cập nhật rating_avg và reviews_count vào bảng products
        $stats = $this->getStats((int) $data['product_id']);
        $this->db->query(
            "UPDATE products SET rating_avg = :avg, reviews_count = :cnt WHERE id = :pid",
            ['avg' => $stats['avg'], 'cnt' => $stats['total'], 'pid' => (int) $data['product_id']]
        );

        return $id;
    }

    /**
     * Phân trang danh sách đánh giá cho Admin
     */
    public function adminPaginate(string $keyword = '', ?int $rating = null, int $page = 1, int $perPage = 15): array
    {
        $where = ['1=1'];
        $params = [];

        if ($keyword !== '') {
            $where[] = '(r.author_name LIKE :kw OR r.comment LIKE :kw OR p.name LIKE :kw OR p.model_code LIKE :kw)';
            $params['kw'] = "%{$keyword}%";
        }

        if ($rating !== null && $rating > 0) {
            $where[] = 'r.rating = :rating';
            $params['rating'] = $rating;
        }

        $whereClause = implode(' AND ', $where);
        $total = (int) $this->db->fetchColumn(
            "SELECT COUNT(*) FROM reviews r LEFT JOIN products p ON r.product_id = p.id WHERE {$whereClause}",
            $params
        );

        $pages = max(1, (int) ceil($total / $perPage));
        $page = max(1, min($page, $pages));
        $offset = ($page - 1) * $perPage;

        $items = $this->db->fetchAll(
            "SELECT r.*, p.name as product_name, p.model_code, p.slug as product_slug, p.thumbnail as product_thumbnail
             FROM reviews r
             LEFT JOIN products p ON r.product_id = p.id
             WHERE {$whereClause}
             ORDER BY r.id DESC
             LIMIT {$perPage} OFFSET {$offset}",
            $params
        );

        return [
            'items' => $items,
            'total' => $total,
            'page'  => $page,
            'pages' => $pages,
        ];
    }

    /**
     * Quản trị viên phản hồi/trả lời đánh giá
     */
    public function replyReview(int $id, string $reply): bool
    {
        $reply = trim($reply);
        return (bool) $this->db->query(
            "UPDATE reviews SET admin_reply = :reply, admin_reply_at = :now WHERE id = :id",
            [
                'reply' => $reply !== '' ? $reply : null,
                'now'   => $reply !== '' ? date('Y-m-d H:i:s') : null,
                'id'    => $id
            ]
        );
    }
}
