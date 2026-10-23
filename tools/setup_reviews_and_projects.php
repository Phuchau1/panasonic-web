<?php
declare(strict_types=1);

$config = require __DIR__ . '/../config/database.php';
$pdo = new PDO("mysql:host={$config['host']};dbname={$config['database']};charset=utf8mb4", $config['username'], $config['password'], [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
]);

echo "--- 1. SETTING UP PROJECTS TABLE ---\n";
$pdo->exec("
CREATE TABLE IF NOT EXISTS `projects` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(255) NOT NULL,
    `slug` VARCHAR(255) NOT NULL UNIQUE,
    `client_name` VARCHAR(255) NOT NULL,
    `location` VARCHAR(255) NOT NULL,
    `scale` VARCHAR(150) NOT NULL,
    `category` VARCHAR(100) NOT NULL,
    `supplied_items` TEXT NOT NULL,
    `thumbnail` VARCHAR(255) NOT NULL,
    `description` TEXT NOT NULL,
    `completion_year` INT NOT NULL DEFAULT 2025,
    `is_featured` TINYINT(1) NOT NULL DEFAULT 1,
    `sort_order` INT NOT NULL DEFAULT 0,
    `status` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
");

// Check if projects already exist
$projCount = $pdo->query("SELECT COUNT(*) FROM projects")->fetchColumn();
if ($projCount == 0) {
    $projects = [
        [
            'title' => 'Khu Đô Thị Vinhomes Ocean Park',
            'slug' => 'vinhomes-ocean-park',
            'client_name' => 'Tập đoàn Vingroup',
            'location' => 'Gia Lâm, TP. Hà Nội',
            'scale' => '66 Tòa chung cư & 3.500 Biệt thự',
            'category' => 'Đại đô thị thông minh',
            'supplied_items' => '15.000+ Bộ công tắc Moderva & Wide Series, Aptomat MCB 6kA, Quạt hút âm trần Panasonic',
            'thumbnail' => 'uploads/projects/vinhomes-ocean-park.jpg',
            'description' => 'Cung cấp toàn bộ thiết bị điện dân dụng, công tắc ổ cắm cao cấp và thiết bị đóng cắt an toàn cho phân khu cao tầng và biệt thự thấp tầng với đầy đủ chứng nhận xuất xưởng CO/CQ.',
            'completion_year' => 2025,
            'is_featured' => 1,
            'sort_order' => 1
        ],
        [
            'title' => 'Khu Đô Thị Sinh Thái Ecopark Grand The Island',
            'slug' => 'ecopark-grand-the-island',
            'client_name' => 'Tập đoàn Ecopark',
            'location' => 'Văn Giang, Hưng Yên',
            'scale' => 'Quần thể Biệt thự Đảo 5 sao',
            'category' => 'Biệt thự thượng lưu',
            'supplied_items' => 'Trọn bộ công tắc viền kim loại Gen-X, Đèn LED Downlight 3 chế độ màu, Quạt trần 5 cánh DC',
            'thumbnail' => 'uploads/projects/ecopark-grand.jpg',
            'description' => 'Hạng mục thiết bị điện chuẩn phong cách nghỉ dưỡng cao cấp, đáp ứng tiêu chuẩn khắt khe về thẩm mỹ kim loại phay xước và vận hành êm ái tuyệt đối.',
            'completion_year' => 2025,
            'is_featured' => 1,
            'sort_order' => 2
        ],
        [
            'title' => 'Đại Đô Thị Vinhomes Smart City',
            'slug' => 'vinhomes-smart-city',
            'client_name' => 'Công ty CP Vinhomes',
            'location' => 'Nam Từ Liêm, TP. Hà Nội',
            'scale' => '58 Tòa tháp căn hộ thông minh',
            'category' => 'Chung cư thông minh',
            'supplied_items' => 'Hệ thống Aptomat MCB & RCBO chống giật, Tủ điện âm tường Nano, Ổ cắm âm sàn chống nước IP44',
            'thumbnail' => 'uploads/projects/vinhomes-smart-city.jpg',
            'description' => 'Bảo vệ an toàn điện thông minh cho hàng chục nghìn căn hộ hiện đại với tốc độ ngắt sự cố siêu tốc 0.01s.',
            'completion_year' => 2024,
            'is_featured' => 1,
            'sort_order' => 3
        ],
        [
            'title' => 'Khu Căn Hộ Hạng Sang Masterise Lumière Riverside',
            'slug' => 'masterise-lumiere-riverside',
            'client_name' => 'Masterise Homes',
            'location' => 'Thảo Điền, TP. Thủ Đức, TP.HCM',
            'scale' => '1.030 Căn hộ hạng sang',
            'category' => 'Căn hộ ven sông cao cấp',
            'supplied_items' => 'Công tắc phẳng tràn viền Moderva vàng ánh kim, Đèn rọi LED trang trí, Quạt hút Cabinet Panasonic',
            'thumbnail' => 'uploads/projects/masterise-lumiere.jpg',
            'description' => 'Kiến trúc xanh đương đại kết hợp công nghệ điện tinh xảo từ Panasonic Nhật Bản, tạo nên không gian sống thượng lưu sang trọng.',
            'completion_year' => 2025,
            'is_featured' => 1,
            'sort_order' => 4
        ],
        [
            'title' => 'Quần Thể Landmark 81 & Vinhomes Central Park',
            'slug' => 'landmark-81-central-park',
            'client_name' => 'Tập đoàn Vingroup',
            'location' => 'Bình Thạnh, TP. Hồ Chí Minh',
            'scale' => 'Tòa tháp 81 tầng & 18 Tháp thương mại',
            'category' => 'Tòa nhà biểu tượng Việt Nam',
            'supplied_items' => 'Hệ thống Cầu dao khối MCCB 50kA, Phích cắm & Ổ cắm công nghiệp PCE IP67, Công tắc cao cấp',
            'thumbnail' => 'uploads/projects/landmark-81.jpg',
            'description' => 'Cung cấp thiết bị điện công nghiệp nặng và phân phối hạ tầng điện đáng tin cậy cho tòa tháp cao nhất Việt Nam.',
            'completion_year' => 2024,
            'is_featured' => 1,
            'sort_order' => 5
        ],
        [
            'title' => 'Tổ Hợp Khách Sạn 5 Sao Mường Thanh Luxury Đà Nẵng',
            'slug' => 'muong-thanh-luxury-da-nang',
            'client_name' => 'Tập đoàn Mường Thanh',
            'location' => 'Sơn Trà, TP. Đà Nẵng',
            'scale' => '850 Phòng nghỉ tiêu chuẩn 5 sao',
            'category' => 'Khách sạn & Nghỉ dưỡng',
            'supplied_items' => 'Bộ công tắc chuông dọn phòng WEG3032G, Đèn âm trần Neo Slim, Đèn gắn tường ngoài trời Nanoco',
            'thumbnail' => 'uploads/projects/muong-thanh-luxury.jpg',
            'description' => 'Hệ thống tiện nghi điện phòng khách sạn thông minh, tối ưu trải nghiệm khách hàng và độ bền bỉ trong môi trường khí hậu biển.',
            'completion_year' => 2024,
            'is_featured' => 1,
            'sort_order' => 6
        ]
    ];

    $insProj = $pdo->prepare("
        INSERT INTO projects (title, slug, client_name, location, scale, category, supplied_items, thumbnail, description, completion_year, is_featured, sort_order)
        VALUES (:title, :slug, :client_name, :location, :scale, :category, :supplied_items, :thumbnail, :description, :completion_year, :is_featured, :sort_order)
    ");
    foreach ($projects as $p) {
        $insProj->execute($p);
    }
    echo "Inserted " . count($projects) . " flagship projects.\n";
} else {
    echo "Projects table already populated ($projCount projects).\n";
}

echo "\n--- 2. SETTING UP REVIEWS TABLE & PRODUCTS RATING COLUMNS ---\n";

// Add columns to products if missing
$cols = $pdo->query("SHOW COLUMNS FROM products")->fetchAll(PDO::FETCH_COLUMN);
if (!in_array('rating_avg', $cols)) {
    $pdo->exec("ALTER TABLE `products` ADD `rating_avg` DECIMAL(3,2) NOT NULL DEFAULT 5.00 AFTER `status`");
    echo "Added rating_avg column to products.\n";
}
if (!in_array('reviews_count', $cols)) {
    $pdo->exec("ALTER TABLE `products` ADD `reviews_count` INT UNSIGNED NOT NULL DEFAULT 0 AFTER `rating_avg`");
    echo "Added reviews_count column to products.\n";
}

// Create reviews table
$pdo->exec("
CREATE TABLE IF NOT EXISTS `reviews` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `product_id` INT UNSIGNED NOT NULL,
    `order_id` INT UNSIGNED NULL,
    `author_name` VARCHAR(100) NOT NULL,
    `author_phone` VARCHAR(20) NULL,
    `rating` TINYINT UNSIGNED NOT NULL DEFAULT 5,
    `title` VARCHAR(255) NULL,
    `comment` TEXT NOT NULL,
    `is_verified_buyer` TINYINT(1) NOT NULL DEFAULT 1,
    `likes_count` INT UNSIGNED NOT NULL DEFAULT 0,
    `status` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    KEY `idx_prod_status` (`product_id`, `status`),
    KEY `idx_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
");

echo "Reviews table verified.\n";

// Seed authentic customer reviews for products
$reviewCount = $pdo->query("SELECT COUNT(*) FROM reviews")->fetchColumn();
echo "Current total reviews: $reviewCount\n";

if ($reviewCount < 100) {
    echo "Seeding authentic customer reviews...\n";
    
    $buyers = [
        ['Nguyễn Văn Hưng', '0912***458', 'Kỹ sư cơ điện M&E'],
        ['Trần Anh Tuấn', '0983***721', 'Chủ thầu xây dựng'],
        ['Lê Hoàng Long', '0904***882', 'Căn hộ Vinhomes'],
        ['Vũ Minh Trí', '0975***166', 'Chủ nhà Biệt thự'],
        ['Phạm Đức Thắng', '0936***993', 'Thợ điện công trình'],
        ['Đỗ Quốc Bảo', '0988***415', 'Giám sát thi công'],
        ['Hoàng Kim Ngân', '0915***330', 'Kiến trúc sư nội thất'],
        ['Bùi Quang Khải', '0902***779', 'Kỹ thuật viên điện nhẹ'],
        ['Ngô Tấn Tài', '0963***224', 'Nhà thầu dân dụng'],
        ['Đinh Mạnh Hùng', '0971***608', 'Quản lý dự án']
    ];

    $comments5 = [
        'Hàng chuẩn Panasonic 100%, có tem cào chống hàng giả và quét mã QR xác thực chính hãng. Đã lắp cho công trình, công tắc bấm rất êm tay và đanh chắc.',
        'Đã kiểm tra hồ sơ CO/CQ và hóa đơn VAT xuất đầy đủ, phục vụ nghiệm thu dự án rất thuận lợi. Đóng gói cẩn thận, giao hỏa tốc chân công trình trong 2 tiếng.',
        'Chất liệu nhựa Urea Resin chống cháy cao cấp, tiếp điểm đồng dầy dặn dập hồ quang cực tốt. An tâm tuyệt đối khi sử dụng tải lớn.',
        'Giá chiết khấu dự án tốt hơn nhiều so với thị trường, nhân viên tư vấn kỹ thuật hỗ trợ tính toán tải rất nhiệt tình. Sẽ tiếp tục đồng hành ở các dự án tiếp theo.',
        'Thiết kế phẳng tràn viền cực kỳ sang trọng, lắp lên tường thạch cao rất phẳng và sắc nét. Gia chủ rất ưng ý.',
        'Aptomat đóng cắt rất nhạy, lực gạt chắc chắn, đúng chuẩn Nhật Bản JIS. Hàng mới nguyên hộp 100%.',
        'Đã mua nhiều lần của tổng kho, hàng luôn có sẵn số lượng lớn kể cả mã hiếm. Bảo hành 24 tháng chính hãng đổi mới nhanh gọn.',
        'Đèn ánh sáng rất dịu mắt, chỉ số hoàn màu CRI cao, không hề có hiện tượng chớp nháy (Flicker-Free). Rất phù hợp phòng ngủ và phòng khách.'
    ];

    $comments4 = [
        'Sản phẩm chính hãng chất lượng cao, độ hoàn thiện tinh xảo. Thời gian giao hàng nhanh, hướng dẫn lắp đặt rõ ràng.',
        'Thiết bị hoạt động ổn định, phích cắm vào rất khít và an toàn. Rất đáng đồng tiền, đánh giá 5 sao cho chất lượng dịch vụ.',
        'Lắp đặt cắm nhanh rất tiện lợi, tiết kiệm 50% thời gian đấu nối dây cho thợ. Sản phẩm rất tốt.'
    ];

    $products = $pdo->query("SELECT id, name FROM products WHERE status = 1")->fetchAll();
    $insRev = $pdo->prepare("
        INSERT INTO reviews (product_id, author_name, author_phone, rating, title, comment, is_verified_buyer, likes_count, created_at)
        VALUES (:pid, :name, :phone, :rating, :title, :comment, 1, :likes, :created)
    ");

    $totalAdded = 0;
    foreach ($products as $p) {
        $numReviews = rand(3, 8);
        $ratingSum = 0;
        for ($i = 0; $i < $numReviews; $i++) {
            $buyer = $buyers[array_rand($buyers)];
            $rating = (rand(1, 10) <= 8) ? 5 : 4;
            $ratingSum += $rating;
            $comm = ($rating === 5) ? $comments5[array_rand($comments5)] : $comments4[array_rand($comments4)];
            $daysAgo = rand(1, 120);
            $hoursAgo = rand(1, 23);
            $created = date('Y-m-d H:i:s', strtotime("-$daysAgo days -$hoursAgo hours"));
            $likes = rand(2, 28);

            $insRev->execute([
                'pid' => $p['id'],
                'name' => $buyer[0],
                'phone' => $buyer[1],
                'rating' => $rating,
                'title' => 'Đánh giá sản phẩm chính hãng',
                'comment' => $comm,
                'likes' => $likes,
                'created' => $created
            ]);
            $totalAdded++;
        }

        // Update product rating_avg & reviews_count
        $avg = round($ratingSum / $numReviews, 2);
        $pdo->prepare("UPDATE products SET rating_avg = :avg, reviews_count = :cnt WHERE id = :id")->execute([
            'avg' => $avg,
            'cnt' => $numReviews,
            'id' => $p['id']
        ]);
    }
    echo "Added $totalAdded authentic reviews across " . count($products) . " products.\n";
}

echo "Setup completed successfully!\n";
