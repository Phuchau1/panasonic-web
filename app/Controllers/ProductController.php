<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductSeries;
use Core\Controller;

class ProductController extends Controller
{
    private Product $products;

    public function __construct()
    {
        $this->products = new Product();
    }

    /** /san-pham?category=&series=&ampe[]=&ka[]=&poles[]=&price_min=&price_max=&q=&sort= */
    public function index(): void
    {
        $this->listing([], 'Tất cả sản phẩm Panasonic');
    }

    /** /danh-muc/{slug} */
    public function category(string $slug): void
    {
        $category = (new Category())->findActiveBySlug($slug);
        if (!$category) {
            $this->abort(404, 'Danh mục không tồn tại.');
        }

        $hasFilters = !empty($_GET['q'])
            || !empty($_GET['ampe'])
            || !empty($_GET['ka'])
            || !empty($_GET['poles'])
            || !empty($_GET['price_min'])
            || !empty($_GET['price_max'])
            || (!empty($_GET['sort']) && $_GET['sort'] !== 'newest')
            || !empty($_GET['series'])
            || (!empty($_GET['page']) && (int) $_GET['page'] > 1);

        if (!$hasFilters) {
            $sections = $this->buildCategorySections((int) $category['id'], $slug);
            if (!empty($sections)) {
                $this->render('products/category_overview', [
                    'title'           => $category['name'] . ' Panasonic Chính Hãng | Bảng Giá & Chiết Khấu Mới Nhất',
                    'metaDescription' => $category['description'] ?: "Bảng giá và các dòng sản phẩm {$category['name']} Panasonic chính hãng bảo hành 24 tháng.",
                    'category'        => $category,
                    'sections'        => $sections,
                ]);
                return;
            }
        }

        $this->listing(['category' => $slug], $category['name'], $category);
    }

    /** /panasonic-{slug} – ví dụ /panasonic-moderva */
    public function series(string $slug): void
    {
        $series = (new ProductSeries())->findActiveBySlug($slug);
        if (!$series) {
            $this->abort(404, 'Dòng sản phẩm không tồn tại.');
        }
        $this->listing(['series' => $slug], 'Panasonic ' . $series['name'], null, $series);
    }

    /** /san-pham/{slug} */
    public function detail(string $slug): void
    {
        $product = $this->products->findBySlug($slug);
        if (!$product) {
            $this->abort(404, 'Sản phẩm không tồn tại hoặc đã ngừng kinh doanh.');
        }

        // Chỉ tăng lượt xem 1 lần / phiên
        $viewed = $_SESSION['viewed'] ?? [];
        if (!in_array($product['id'], $viewed, true)) {
            $this->products->incrementViews((int) $product['id']);
            $_SESSION['viewed'] = array_slice(array_merge($viewed, [$product['id']]), -50);
        }

        $reviewsModel = new \App\Models\Review();
        $reviews = $reviewsModel->getByProduct((int) $product['id'], 25);
        $reviewStats = $reviewsModel->getStats((int) $product['id']);

        $this->render('products/detail', [
            'title'           => $product['name'] . ' – ' . $product['model_code'],
            'metaDescription' => $product['short_desc'] ?: $product['name'],
            'product'         => $product,
            'related'         => $this->products->related($product, 4),
            'reviews'         => $reviews,
            'reviewStats'     => $reviewStats,
        ]);
    }

    /** POST /api/danh-gia - Lưu đánh giá thật của khách hàng vào database */
    public function addReview(): void
    {
        $productId = $this->inputInt('product_id');
        $name = trim($this->input('author_name'));
        $phone = trim($this->input('author_phone'));
        $rating = max(1, min(5, $this->inputInt('rating', 5)));
        $comment = trim($this->input('comment'));

        if (!$productId || empty($name) || empty($comment)) {
            $this->json([
                'success' => false,
                'message' => 'Vui lòng điền đầy đủ họ tên và nội dung đánh giá sản phẩm.'
            ], 422);
        }

        $reviewsModel = new \App\Models\Review();
        $id = $reviewsModel->addReview([
            'product_id'   => $productId,
            'author_name'  => $name,
            'author_phone' => $phone,
            'rating'       => $rating,
            'comment'      => $comment,
            'title'        => 'Đánh giá sản phẩm',
        ]);

        $this->json([
            'success' => true,
            'message' => 'Cảm ơn bạn! Đánh giá đã được lưu vào hệ thống thành công.',
            'review'  => [
                'id'          => $id,
                'author_name' => htmlspecialchars($name, ENT_QUOTES, 'UTF-8'),
                'rating'      => $rating,
                'comment'     => htmlspecialchars($comment, ENT_QUOTES, 'UTF-8'),
                'created_at'  => date('d/m/Y')
            ]
        ]);
    }

    /** /api/tim-kiem?q=WEV – JSON cho Live Search */
    public function search(): void
    {
        $q = $this->input('q');
        $results = $this->products->liveSearch($q, 8);

        $this->json([
            'success' => true,
            'query'   => $q,
            'total'   => count($results),
            'items'   => $results,
            'all_url' => url('/san-pham') . '?q=' . rawurlencode($q),
        ]);
    }

    private function listing(array $fixed, string $heading, ?array $category = null, ?array $series = null): void
    {
        $filters = [
            'category'  => $fixed['category'] ?? $this->input('category'),
            'series'    => $fixed['series'] ?? $this->input('series'),
            'ampe'      => $_GET['ampe'] ?? null,
            'ka'        => $_GET['ka'] ?? null,
            'poles'     => $_GET['poles'] ?? null,
            'price_min' => $this->input('price_min'),
            'price_max' => $this->input('price_max'),
            'q'         => $this->input('q'),
            'sort'      => $this->input('sort', 'newest'),
            'limit'     => $_GET['limit'] ?? null,
        ];

        // Chuẩn hoá giá trị mảng từ query string
        foreach (['ampe', 'ka', 'poles'] as $key) {
            $filters[$key] = array_values(array_filter((array) ($filters[$key] ?? []), 'is_numeric'));
        }

        if ($category === null && !empty($filters['category'])) {
            $category = (new Category())->findActiveBySlug($filters['category']);
        }
        if ($series === null && !empty($filters['series'])) {
            $series = (new ProductSeries())->findActiveBySlug($filters['series']);
        }

        if ($filters['q'] !== '') {
            $heading = 'Kết quả tìm kiếm: “' . $filters['q'] . '”';
        } elseif ($category && $series) {
            $heading = $category['name'] . ' – Dòng ' . $series['name'];
        } elseif ($category) {
            $heading = $category['name'] . ' Panasonic';
        } elseif ($series) {
            $heading = 'Thiết bị điện Panasonic dòng ' . $series['name'];
        } else {
            $heading = 'Tất cả sản phẩm Panasonic';
        }

        $perPage = 12;
        if ($filters['limit'] === 'all') {
            $perPage = 1000;
        } elseif (in_array((int)$filters['limit'], [12, 24, 48, 96], true)) {
            $perPage = (int)$filters['limit'];
        }

        $page   = max(1, $this->inputInt('page', 1));
        $result = $this->products->filter($filters, $page, $perPage);

        $this->render('products/list', [
            'title'           => $heading . ' | ' . config('name'),
            'metaDescription' => $category['description'] ?? ($series['tagline'] ?? config('tagline')),
            'heading'         => $heading,
            'category'        => $category,
            'series'          => $series,
            'filters'         => $filters,
            'fixed'           => $fixed,
            'result'          => $result,
            'categories'      => (new Category())->active(),
            'seriesList'      => (new ProductSeries())->active(),
            'facets'          => $this->products->facets($filters['category'] ?: null),
        ]);
    }

    private function buildCategorySections(int $catId, string $slug): array
    {
        $db = \Core\Database::getInstance();
        $sections = [];

        switch ($slug) {
            case 'cong-tac-o-cam':
                $seriesDefs = [
                    ['id' => 'series-gen-x', 'label' => 'Gen-X', 'series_id' => 3, 'img' => 'uploads/series/gen-x.png', 'title' => 'PANASONIC DÒNG GEN-X', 'bullets' => ['Đỉnh cao thiết kế công tắc mỏng nhẹ với bề mặt nhôm xước phay siêu mỏng chỉ 2mm.', 'Cơ chế bấm nhả chuyển động nhẹ nhàng, tiếp điểm hợp kim bạc nguyên chất siêu bền bỉ.'], 'url' => url('/panasonic-gen-x')],
                    ['id' => 'series-minerva', 'label' => 'Minerva', 'series_id' => 6, 'img' => 'uploads/products/bo-1-o-cam-mang-cat5e-minerva-wmt421myh-vn-xam-anh-kim.jpg', 'title' => 'PANASONIC DÒNG MINERVA', 'bullets' => ['Thiết kế mặt phẳng tràn viền nguyên khối hiện đại, góc cạnh vuông vắn tinh tế.', 'Đa dạng 3 gam màu thời thượng: Trắng tuyết, Xám ánh kim và Vàng ánh kim cao cấp.'], 'url' => url('/panasonic-minerva')],
                    ['id' => 'series-moderva', 'label' => 'Moderva', 'series_id' => 4, 'img' => 'uploads/products/bo-o-cam-vuong-dong-moderva-panasonic-mau-trang-1.jpg', 'title' => 'PANASONIC DÒNG MODERVA', 'bullets' => ['Dòng công tắc mặt vuông chuẩn phong cách Châu Âu, phím bấm êm ái chống bám vân tay.', 'Lựa chọn hoàn hảo cho các căn hộ cao cấp, biệt thự sang trọng và khách sạn hiện đại.'], 'url' => url('/panasonic-moderva')],
                    ['id' => 'series-refina', 'label' => 'Refina', 'series_id' => 7, 'img' => 'uploads/products/bo-cong-tac-o-cam-panasonic-refina-mau-xam-anh-kim-1.jpg', 'title' => 'PANASONIC DÒNG REFINA', 'bullets' => ['Đường nét bo tròn thanh thoát, viền mạ kim loại ánh kim độc đáo và sang trọng.', 'Tiếp điểm chất lượng cao, chống phóng điện hồ quang an toàn tuyệt đối.'], 'url' => url('/panasonic-refina')],
                    ['id' => 'series-wide', 'label' => 'Wide', 'series_id' => 1, 'img' => 'uploads/products/bo-cong-tac-o-cam-wide-panasonic-mau-trang-1.jpg', 'title' => 'PANASONIC DÒNG WIDE', 'bullets' => ['Dòng công tắc ổ cắm thông dụng và được yêu thích nhất của Panasonic tại Việt Nam.', 'Thiết kế phím bấm lớn dễ thao tác, độ bền vượt trội lên đến 40.000 lần bật tắt.'], 'url' => url('/panasonic-wide')],
                    ['id' => 'series-halumie', 'label' => 'Halumie', 'series_id' => 2, 'img' => 'uploads/products/bo-cong-tac-o-cam-halumie-acrylic-panasonic-1.jpg', 'title' => 'PANASONIC DÒNG HALUMIE', 'bullets' => ['Kiểu dáng bo cong trang nhã, phím bấm êm ái, thân thiện và tiết kiệm chi phí công trình.', 'Tích hợp ổ cắm sạc USB thông minh tiện lợi cho phòng khách, phòng ngủ hiện đại.'], 'url' => url('/panasonic-halumie')],
                ];
                foreach ($seriesDefs as $def) {
                    $rows = $db->fetchAll(
                        "SELECT p.*, c.name AS category_name, s.name AS series_name FROM products p JOIN categories c ON p.category_id = c.id LEFT JOIN product_series s ON p.series_id = s.id WHERE p.category_id = 1 AND p.series_id = :sid AND p.status = 1 ORDER BY p.is_featured DESC, p.id DESC LIMIT 6",
                        ['sid' => $def['series_id']]
                    );
                    if (!empty($rows)) {
                        $sections[] = [
                            'id' => $def['id'],
                            'label' => $def['label'],
                            'icon_img' => $def['img'],
                            'title' => $def['title'],
                            'bullets' => $def['bullets'],
                            'more_url' => $def['url'],
                            'products' => array_map([$this->products, 'decode'], $rows),
                        ];
                    }
                }
                break;

            case 'thiet-bi-dong-cat':
                $groups = [
                    ['id' => 'sec-mcb', 'label' => 'Aptomat MCB', 'where' => "(p.name LIKE '%MCB%' OR p.model_code LIKE 'BBD%')", 'img' => 'uploads/products/4cc41e9a1f832e156ca5f0a6c7dd0bd4.jpeg', 'title' => 'APTOMAT TÉP MCB PANASONIC (1P, 2P, 3P, 4P)', 'bullets' => ['Bảo vệ chống quá tải và ngắn mạch tiêu chuẩn IEC/JIS Nhật Bản, dòng cắt 6kA - 10kA.', 'Tiếp điểm hợp kim bạc dập hồ quang siêu tốc, độ nhạy cao ngắt mạch an toàn.']],
                    ['id' => 'sec-mccb', 'label' => 'Cầu Dao MCCB', 'where' => "(p.name LIKE '%MCCB%' OR p.model_code LIKE 'BBSF%')", 'img' => 'uploads/products/mccb-panasonic-4p-800a-50ka-bbsf47800cmcv.jpg', 'title' => 'CẦU DAO KHỐI MCCB PANASONIC (BBSF 10A - 800A)', 'bullets' => ['Dòng cắt ngắn mạch cực đại 20kA - 50kA, ứng dụng phân phối điện công trình và nhà xưởng.', 'Khung vỏ đúc nguyên khối chống cháy chịu nhiệt cao, vận hành bền bỉ liên tục.']],
                    ['id' => 'sec-rcbo', 'label' => 'Chống Giật RCBO', 'where' => "(p.name LIKE '%RCBO%' OR p.name LIKE '%chống giật%' OR p.model_code LIKE 'BJS%')", 'img' => 'uploads/products/cau-dao-tu-dong-panasonic-bbc31502yhvs_2023-09-12-124914.png', 'title' => 'APTOMAT CHỐNG RÒ CHỐNG GIẬT RCBO & ELB', 'bullets' => ['Tự động ngắt điện siêu tốc trong vòng 0.03 giây khi xảy ra sự cố dòng rò từ 15mA - 30mA.', 'Bảo vệ an toàn tối đa cho tính mạng người sử dụng và phòng chống cháy nổ do chập điện.']],
                    ['id' => 'sec-tudien', 'label' => 'Tủ Điện Âm Tường', 'where' => "(p.name LIKE '%Tủ điện%' OR p.model_code LIKE 'BQD%')", 'img' => 'uploads/products/NDB313-232188483456.jpg', 'title' => 'TỦ ĐIỆN ÂM TƯỜNG PANASONIC (BQDX / BQDF)', 'bullets' => ['Mặt kim loại mạ ánh kim sang trọng (trắng, vàng, xám khói) chống ố vàng và trầy xước.', 'Sức chứa linh hoạt từ 8 đến 40 đường aptomat, đáy tủ thép sơn tĩnh điện chống rỉ sét.']],
                ];
                foreach ($groups as $g) {
                    $rows = $db->fetchAll(
                        "SELECT p.*, c.name AS category_name, s.name AS series_name FROM products p JOIN categories c ON p.category_id = c.id LEFT JOIN product_series s ON p.series_id = s.id WHERE p.category_id = {$catId} AND {$g['where']} AND p.status = 1 ORDER BY p.is_featured DESC, p.id DESC LIMIT 6"
                    );
                    if (!empty($rows)) {
                        $sections[] = [
                            'id' => $g['id'],
                            'label' => $g['label'],
                            'icon_img' => $g['img'],
                            'title' => $g['title'],
                            'bullets' => $g['bullets'],
                            'more_url' => url('/san-pham?category=' . $slug),
                            'products' => array_map([$this->products, 'decode'], $rows),
                        ];
                    }
                }
                break;

            case 'den-led-chieu-sang':
                $groups = [
                    ['id' => 'sec-neoslim', 'label' => 'Neo Slim 2G', 'where' => "(p.name LIKE '%Neo Slim%')", 'img' => 'uploads/products/c490c3a8n-led-downlight-9w-vuc3b4ng-panasonic.jpg', 'title' => 'ĐÈN DOWNLIGHT ÂM TRẦN NEO SLIM 2G', 'bullets' => ['Thân đèn siêu mỏng chỉ 8mm, thích ứng linh hoạt với mọi kết cấu trần thạch cao hạ sát sàn.', 'Dải công suất 6W, 9W, 12W, 15W, 18W với góc chiếu rộng 120 độ tản sáng đều.']],
                    ['id' => 'sec-dn', 'label' => 'DN Series', 'where' => "(p.name LIKE '%DN 3G%' OR p.name LIKE '%DN 2G%' OR p.model_code LIKE 'NNV%')", 'img' => 'uploads/products/den-am-tran-6w-panasonic-nnv70040we1a-trung-tinh-3.jpg', 'title' => 'ĐÈN DOWNLIGHT ÂM TRẦN DN SERIES (DN 2G & DN 3G)', 'bullets' => ['Thân nhôm tản nhiệt nguyên khối giúp kéo dài tuổi thọ chip LED lên đến 25.000 giờ chiếu sáng.', 'Tùy chọn 3 màu ánh sáng tự nhiên, độ hoàn màu cao tái hiện màu sắc sống động.']],
                    ['id' => 'sec-color-rim', 'label' => 'Color Rim', 'where' => "(p.name LIKE '%Color Rim%')", 'img' => 'uploads/products/den-downlight-panasonic-9w-color-rim-doi-mau-3-mau-vien-vang.jpg', 'title' => 'ĐÈN DOWNLIGHT COLOR RIM VIỀN KIM LOẠI CAO CẤP', 'bullets' => ['Vành viền mạ bạc và mạ vàng ánh kim đẳng cấp, tạo điểm nhấn nghệ thuật cho không gian sống.', 'Chế độ chuyển màu linh hoạt (Vàng - Trung tính - Trắng) chỉ bằng một thao tác bật tắt.']],
                    ['id' => 'sec-denban', 'label' => 'Đèn Bàn Chống Cận', 'where' => "(p.name LIKE '%Đèn Bàn%')", 'img' => 'uploads/products/dp_130_n-b-n-h-c-panasonic-nnp61923191.gif', 'title' => 'ĐÈN BÀN HỌC SINH LED CHỐNG CẬN THỊ PANASONIC', 'bullets' => ['Đạt tiêu chuẩn an toàn sinh học RG0 không ánh sáng xanh gây hại, ánh sáng liên tục không nhấp nháy.', 'Cảm ứng điều chỉnh độ sáng đa cấp độ, bảo vệ thị lực tuyệt đối cho học sinh.']],
                    ['id' => 'sec-ledday', 'label' => 'LED Dây & Nguồn', 'where' => "(p.name LIKE '%LED dây%' OR p.model_code LIKE 'NFV%')", 'img' => 'uploads/products/dp_137_b-ngu-n-led-d-y-400w-panasonic-nfv90001we1a.gif', 'title' => 'ĐÈN LED DÂY & BỘ ĐỔI NGUỒN CHÍNH HÃNG', 'bullets' => ['Dải sáng đồng đều không ngắt quãng, ứng dụng trang trí khe hắt trần và vách tủ sang trọng.', 'Bộ đổi nguồn Panasonic 400W chuyển dòng ổn định, bảo vệ quá dòng an toàn.']],
                    ['id' => 'sec-gantuong', 'label' => 'Đèn Gắn Tường', 'where' => "(p.name LIKE '%ngoài trời%' OR p.name LIKE '%Peacock%')", 'img' => 'uploads/products/dp_153_n-led-g-n-t-ng-6w-panasonic-hhgbw060688.gif', 'title' => 'ĐÈN GẮN TƯỜNG NGOÀI TRỜI CHỐNG NƯỚC IP65', 'bullets' => ['Cấp bảo vệ chống nước và bụi bẩn IP65, độ bền thách thức mọi điều kiện thời tiết.', 'Tạo điểm nhấn ánh sáng tinh tế cho mặt tiền, sân vườn và lối đi ban công.']],
                ];
                foreach ($groups as $g) {
                    $rows = $db->fetchAll(
                        "SELECT p.*, c.name AS category_name, s.name AS series_name FROM products p JOIN categories c ON p.category_id = c.id LEFT JOIN product_series s ON p.series_id = s.id WHERE p.category_id = {$catId} AND {$g['where']} AND p.status = 1 ORDER BY p.is_featured DESC, p.id DESC LIMIT 6"
                    );
                    if (!empty($rows)) {
                        $sections[] = [
                            'id' => $g['id'],
                            'label' => $g['label'],
                            'icon_img' => $g['img'],
                            'title' => $g['title'],
                            'bullets' => $g['bullets'],
                            'more_url' => url('/san-pham?category=' . $slug),
                            'products' => array_map([$this->products, 'decode'], $rows),
                        ];
                    }
                }
                break;

            case 'quat-tran-quat-hut':
                $groups = [
                    ['id' => 'sec-quattran', 'label' => 'Quạt Trần 5 Cánh', 'where' => "(p.name LIKE '%Quạt trần%' OR p.model_code LIKE '60%')", 'img' => 'uploads/products/dp_161_qu-t-tr-n-5-c-nh-f-60gfn.jpg', 'title' => 'QUẠT TRẦN 5 CÁNH PANASONIC CAO CẤP', 'bullets' => ['Động cơ DC thế hệ mới tiết kiệm 50% điện năng, vận hành êm ái với 9 cấp độ gió.', 'Trang bị hệ thống an toàn 3 cấp độc quyền: Dây an toàn, Khóa cánh an toàn và Công tắc tự ngắt.']],
                    ['id' => 'sec-quatchangio', 'label' => 'Quạt Chắn Gió', 'where' => "(p.name LIKE '%chắn gió%' OR p.model_code LIKE 'FY%')", 'img' => 'uploads/products/quat-chan-gio-panasonic-fy-3509u1.jpg', 'title' => 'QUẠT CHẮN GIÓ PANASONIC (AIR CURTAIN)', 'bullets' => ['Tạo màn chắn không khí vô hình ngăn bụi bẩn, mùi hôi và tránh thất thoát nhiệt điều hòa.', 'Hiệu suất lưu lượng gió mạnh mẽ, độ ồn thấp thích hợp cho sảnh trung tâm và cửa hàng.']],
                    ['id' => 'sec-quathut', 'label' => 'Quạt Hút Thông Gió', 'where' => "(p.name LIKE '%Quạt hút%' OR p.model_code LIKE 'FV%')", 'img' => 'uploads/products/dp_178_qu-t-h-t-s-d-ng-cho-nh-t-m-fv-15egd2.png', 'title' => 'QUẠT HÚT THÔNG GIÓ & SƯỞI NHÀ TẮM PANASONIC', 'bullets' => ['Động cơ bạc đạn kín bôi trơn vĩnh cửu, độ bền trên 10 năm hoạt động liên tục.', 'Khử mùi và hút ẩm siêu nhanh, mang lại không gian sống luôn thông thoáng trong lành.']],
                ];
                foreach ($groups as $g) {
                    $rows = $db->fetchAll(
                        "SELECT p.*, c.name AS category_name, s.name AS series_name FROM products p JOIN categories c ON p.category_id = c.id LEFT JOIN product_series s ON p.series_id = s.id WHERE p.category_id = {$catId} AND {$g['where']} AND p.status = 1 ORDER BY p.is_featured DESC, p.id DESC LIMIT 6"
                    );
                    if (!empty($rows)) {
                        $sections[] = [
                            'id' => $g['id'],
                            'label' => $g['label'],
                            'icon_img' => $g['img'],
                            'title' => $g['title'],
                            'bullets' => $g['bullets'],
                            'more_url' => url('/san-pham?category=' . $slug),
                            'products' => array_map([$this->products, 'decode'], $rows),
                        ];
                    }
                }
                break;

            case 'o-cam-am-san-cong-nghiep':
                $groups = [
                    ['id' => 'sec-pce-phich', 'label' => 'Phích Cắm Di Động', 'where' => "p.name LIKE '%Phích cắm%'", 'img' => 'uploads/products/PCE-F115-6.jpg', 'title' => 'PHÍCH CẮM DI ĐỘNG CÔNG NGHIỆP PCE (IP44 / IP67)', 'bullets' => ['Vật liệu Polyamide 6 cách điện tuyệt đối, chịu va đập mạnh và chịu nhiệt độ cao.', 'Thiết kế kín nước chuẩn IP44 / IP67 chống bụi bẩn và tia nước bắn từ mọi hướng.']],
                    ['id' => 'sec-pce-noi', 'label' => 'Ổ Cắm Nổi PCE', 'where' => "p.name LIKE '%Ổ cắm nổi%'", 'img' => 'uploads/products/PCE-F115-6.jpg', 'title' => 'Ổ CẮM NỔI CÔNG NGHIỆP PCE (WALL SOCKET)', 'bullets' => ['Cố định trên tường, khả năng dẫn dòng ổn định 16A - 32A chịu tải công nghiệp.', 'Khóa an toàn giữ phích cắm chắc chắn không bị tuột khi vận hành thiết bị nặng.']],
                    ['id' => 'sec-pce-noiday', 'label' => 'Ổ Cắm Nối PCE', 'where' => "p.name LIKE '%Ổ cắm nối%'", 'img' => 'uploads/products/PCE-F115-6.jpg', 'title' => 'Ổ CẮM NỐI DI ĐỘNG PCE (CONNECTOR)', 'bullets' => ['Thiết kế tay cầm tiện lợi kết nối đường dây cấp nguồn di động tại công trường.', 'Độ kín nước cao đảm bảo an toàn cả trong môi trường ẩm ướt hoặc ngoài trời.']],
                    ['id' => 'sec-pce-am', 'label' => 'Ổ Cắm Âm PCE', 'where' => "p.name LIKE '%Ổ cắm âm%'", 'img' => 'uploads/products/315-6125705181.jpg', 'title' => 'Ổ CẮM ÂM CÔNG NGHIỆP PCE (FLANGED SOCKET)', 'bullets' => ['Dạng thẳng và dạng nghiêng lắp âm mặt tủ điện phân phối nhà xưởng.', 'Chân tiếp điểm đồng thau nguyên khối truyền tải dòng điện cao lên tới 63A - 125A.']],
                ];
                foreach ($groups as $g) {
                    $rows = $db->fetchAll(
                        "SELECT p.*, c.name AS category_name, s.name AS series_name FROM products p JOIN categories c ON p.category_id = c.id LEFT JOIN product_series s ON p.series_id = s.id WHERE p.category_id = {$catId} AND {$g['where']} AND p.status = 1 ORDER BY p.is_featured DESC, p.id DESC LIMIT 6"
                    );
                    if (!empty($rows)) {
                        $sections[] = [
                            'id' => $g['id'],
                            'label' => $g['label'],
                            'icon_img' => $g['img'],
                            'title' => $g['title'],
                            'bullets' => $g['bullets'],
                            'more_url' => url('/san-pham?category=' . $slug),
                            'products' => array_map([$this->products, 'decode'], $rows),
                        ];
                    }
                }
                break;

            case 'may-bom-panasonic':
                $rows = $db->fetchAll(
                    "SELECT p.*, c.name AS category_name, s.name AS series_name FROM products p JOIN categories c ON p.category_id = c.id LEFT JOIN product_series s ON p.series_id = s.id WHERE p.category_id = {$catId} AND p.status = 1 ORDER BY p.id ASC"
                );
                if (!empty($rows)) {
                    $sections[] = [
                        'id' => 'sec-bom',
                        'label' => 'Máy Bơm Nước',
                        'icon_img' => $rows[0]['thumbnail'] ?? 'uploads/products/may-bom-nuoc-day-cao-panasonic-gp-201jxk-200w.jpg',
                        'title' => 'MÁY BƠM NƯỚC PANASONIC CHÍNH HÃNG (ĐẨY CAO & TĂNG ÁP)',
                        'bullets' => ['Động cơ quấn dây đồng 100% bền bỉ, buồng bơm bằng đồng thau chống rỉ sét.', 'Trang bị rơ-le nhiệt tự ngắt bảo vệ máy khi quá nhiệt hoặc mất nguồn nước.'],
                        'more_url' => url('/san-pham?category=' . $slug),
                        'products' => array_map([$this->products, 'decode'], $rows),
                    ];
                }
                break;

            case 'may-nuoc-nong-panasonic':
                $rows = $db->fetchAll(
                    "SELECT p.*, c.name AS category_name, s.name AS series_name FROM products p JOIN categories c ON p.category_id = c.id LEFT JOIN product_series s ON p.series_id = s.id WHERE p.category_id = {$catId} AND p.status = 1 ORDER BY p.id ASC"
                );
                if (!empty($rows)) {
                    $sections[] = [
                        'id' => 'sec-nuoc-nong',
                        'label' => 'Bình Nước Nóng',
                        'icon_img' => $rows[0]['thumbnail'] ?? '',
                        'title' => 'BÌNH NÓNG LẠNH GIÁN TIẾP PANASONIC (15L, 20L, 30L)',
                        'bullets' => ['Ruột bình phủ men titan chống ăn mòn cặn bám, giữ nhiệt vượt trội lên đến 72 giờ.', 'Hệ thống an toàn kép chống giật ELCB và cảm biến chống bỏng tự động.'],
                        'more_url' => url('/san-pham?category=' . $slug),
                        'products' => array_map([$this->products, 'decode'], $rows),
                    ];
                }
                break;

            case 'may-loc-khong-khi':
                $rows = $db->fetchAll(
                    "SELECT p.*, c.name AS category_name, s.name AS series_name FROM products p JOIN categories c ON p.category_id = c.id LEFT JOIN product_series s ON p.series_id = s.id WHERE p.category_id = {$catId} AND p.status = 1 ORDER BY p.id ASC"
                );
                if (!empty($rows)) {
                    $sections[] = [
                        'id' => 'sec-loc-khi',
                        'label' => 'Máy Lọc Khí',
                        'icon_img' => $rows[0]['thumbnail'] ?? '',
                        'title' => 'MÁY LỌC KHÔNG KHÍ & KHỬ MÙI NANOE™ X PANASONIC',
                        'bullets' => ['Công nghệ phát hạt Nanoe™ X ức chế 99.9% virus, vi khuẩn, nấm mốc và khử mùi hôi khó chịu.', 'Màng lọc HEPA cao cấp giữ lại 99.97% các hạt bụi mịn siêu nhỏ PM2.5.'],
                        'more_url' => url('/san-pham?category=' . $slug),
                        'products' => array_map([$this->products, 'decode'], $rows),
                    ];
                }
                break;

            case 'day-cap-dien-cadivi':
                $groups = [
                    [
                        'id' => 'sec-vcm',
                        'label' => 'Dây Đơn Mềm VCm',
                        'where' => "p.name LIKE '%VCm%'",
                        'img' => 'uploads/products/cadivi/cadivi-vcm_do.jpg',
                        'title' => 'DÂY ĐIỆN ĐƠN MỀM CADIVI VCM (1.0 - 10 MM²)',
                        'more_q' => 'vcm',
                        'bullets' => [
                            'Ruột đồng tinh chất 99.99% nhiều sợi mềm dẻo, dễ luồn ống uốn cong khi thi công điện âm tường.',
                            'Vỏ bọc PVC cao cấp cách điện an toàn, chống cháy lan, đầy đủ các gam màu đỏ, vàng, xanh, tiếp địa theo TCVN.'
                        ]
                    ],
                    [
                        'id' => 'sec-vcmd',
                        'label' => 'Dây Đôi Mềm VCmd & VCV',
                        'where' => "(p.name LIKE '%VCmd%' OR p.name LIKE '%VCV%')",
                        'img' => 'uploads/products/cadivi/cadivi-vcmd_trang.jpg',
                        'title' => 'DÂY ĐÔI MỀM DẸP VCMD & DÂY ĐÔI TRÒN VCV',
                        'more_q' => 'vcmd',
                        'bullets' => [
                            'Chuyên dùng cấp nguồn điện cho các thiết bị sinh hoạt gia đình, đèn chiếu sáng, ổ cắm kéo dài công trường.',
                            'Hai lõi đồng mềm dẫn điện tốt, lớp vỏ bảo vệ cách điện kép dẻo dai chịu uốn gấp và va đập tốt.'
                        ]
                    ],
                    [
                        'id' => 'sec-cv',
                        'label' => 'Cáp Điện Lực CV',
                        'where' => "(p.name LIKE '%Cáp điện lực 1 lõi%CV%' OR p.model_code LIKE 'CV-%')",
                        'img' => 'uploads/products/cadivi/cadivi-cv_don.jpg',
                        'title' => 'CÁP ĐIỆN LỰC HẠ THẾ CADIVI CV (0.6/1KV)',
                        'more_q' => 'cv',
                        'bullets' => [
                            'Ruột đồng bện tròn ép chặt, cấp điện áp làm việc 0.6/1kV chuyên dùng làm trục cấp nguồn cho tủ điện tầng và toà nhà.',
                            'Tiết diện từ 4.0mm² đến 35mm², độ sụt áp thấp, khả năng truyền tải dòng công suất cao ổn định liên tục.'
                        ]
                    ],
                    [
                        'id' => 'sec-cxv',
                        'label' => 'Cáp Ngầm Hạ Thế CXV',
                        'where' => "(p.name LIKE '%CXV%' OR p.model_code LIKE 'CXV-%')",
                        'img' => 'uploads/products/cadivi/cadivi-cxv_4.jpg',
                        'title' => 'CÁP ĐIỆN LỰC & CÁP NGẦM CADIVI CXV (XLPE)',
                        'more_q' => 'cxv',
                        'bullets' => [
                            'Lớp cách điện XLPE chịu nhiệt làm việc tới 90°C và chịu ngắn mạch tới 250°C, vượt trội hoàn toàn so với nhựa PVC.',
                            'Ứng dụng chôn ngầm trực tiếp dưới đất, luồn ống cống ngầm hoặc máng cáp trục chính cho nhà xưởng và công trình dự án.'
                        ]
                    ],
                ];
                foreach ($groups as $g) {
                    $rows = $db->fetchAll(
                        "SELECT p.*, c.name AS category_name, s.name AS series_name FROM products p JOIN categories c ON p.category_id = c.id LEFT JOIN product_series s ON p.series_id = s.id WHERE p.category_id = {$catId} AND {$g['where']} AND p.status = 1 ORDER BY p.is_featured DESC, p.id ASC LIMIT 6"
                    );
                    if (!empty($rows)) {
                        $sections[] = [
                            'id' => $g['id'],
                            'label' => $g['label'],
                            'icon_img' => $g['img'],
                            'title' => $g['title'],
                            'bullets' => $g['bullets'],
                            'more_url' => url('/san-pham?category=' . $slug . (!empty($g['more_q']) ? '&q=' . $g['more_q'] : '')),
                            'products' => array_map([$this->products, 'decode'], $rows),
                        ];
                    }
                }
                break;
        }

        return $sections;
    }
}
