<?php
define('ROOT_PATH', dirname(__DIR__));
require ROOT_PATH . '/core/Database.php';

set_time_limit(600);
$pdo = Core\Database::getInstance()->pdo();

// 1. Create uploads directory for cadivi
$imgDir = ROOT_PATH . '/public/uploads/products/cadivi';
if (!is_dir($imgDir)) {
    mkdir($imgDir, 0755, true);
}

// 2. Download and map real product images
$imgSources = [
    'vcm_xanh'    => 'https://codienhaiau.com/wp-content/uploads/2023/01/day-dien-don-cadivi-vcm-xanh.jpg',
    'vcm_do'      => 'https://codienhaiau.com/wp-content/uploads/2023/01/day-dien-don-cadivi-vcm-do.jpg',
    'vcm_vang'    => 'https://codienhaiau.com/wp-content/uploads/2023/01/day-dien-don-cadivi-vcm-vang.jpg',
    'vcm_den'     => 'https://codienhaiau.com/wp-content/uploads/2023/01/day-dien-don-cadivi-vcm-den.jpg',
    'vcm_trang'   => 'https://codienhaiau.com/wp-content/uploads/2023/01/day-dien-don-cadivi-vcm-trang.jpg',
    'vcm_vx'      => 'https://codienhaiau.com/wp-content/uploads/2023/01/day-dien-don-cadivi-vcm-vang-xanh.jpg',
    'vcmd_trang'  => 'https://codienhaiau.com/wp-content/uploads/2023/01/day-dien-doi-cadivi-vcmd-trang.jpg',
    'vcmd_do'     => 'https://codienhaiau.com/wp-content/uploads/2023/01/day-dien-doi-cadivi-vcmd-do.jpg',
    'vcmd_vang'   => 'https://codienhaiau.com/wp-content/uploads/2023/01/day-dien-doi-cadivi-vcmd-vang.jpg',
    'vcmd_xanh'   => 'https://codienhaiau.com/wp-content/uploads/2023/01/day-dien-doi-cadivi-vcmd-xanh.jpg',
    'cv_don'      => 'https://codienhaiau.com/wp-content/uploads/2023/01/day-dien-don-cadivi-cv-do.jpg',
    'vcv_tron'    => 'https://codienhaiau.com/wp-content/uploads/2023/01/day-dien-tron-cadivi-vcv.jpg',
    'cxv_2'       => 'https://thanhthienphu.vn/wp-content/uploads/2025/12/Cap-dien-Cadivi-CXV-2.jpg',
    'cxv_3'       => 'https://thanhthienphu.vn/wp-content/uploads/2025/12/Cap-dien-Cadivi-CXV-3.jpg',
    'cxv_4'       => 'https://thanhthienphu.vn/wp-content/uploads/2025/12/Cap-dien-Cadivi-CXV-4.jpg',
];

$localImgs = [];
foreach ($imgSources as $k => $u) {
    $filename = "cadivi-{$k}.jpg";
    $localPath = "{$imgDir}/{$filename}";
    $relPath = "uploads/products/cadivi/{$filename}";

    if (!file_exists($localPath) || filesize($localPath) < 500) {
        $ch = curl_init($u);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_TIMEOUT        => 15,
            CURLOPT_USERAGENT      => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/120.0.0.0 Safari/537.36',
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
        ]);
        $data = curl_exec($ch);
        curl_close($ch);
        if ($data && strlen($data) > 500) {
            file_put_contents($localPath, $data);
            echo "Downloaded image: {$relPath}\n";
        } else {
            echo "Failed to download image: {$u}\n";
        }
    }
    $localImgs[$k] = $relPath;
}

// 3. Insert or update Category "Dây cáp điện CADIVI"
$cat = $pdo->query("SELECT id FROM categories WHERE slug = 'day-cap-dien-cadivi'")->fetch(PDO::FETCH_ASSOC);
if (!$cat) {
    $pdo->query("
        INSERT INTO categories (name, slug, icon, description, sort_order, status)
        VALUES ('Dây cáp điện CADIVI', 'day-cap-dien-cadivi', 'zap', 'Tổng kho phân phối dây và cáp điện CADIVI chính hãng 100% ruột đồng nguyên chất, chiết khấu đại lý tốt nhất cho công trình dân dụng & công nghiệp.', 9, 1)
    ");
    $catId = (int)$pdo->lastInsertId();
    echo "Created category ID {$catId}: Dây cáp điện CADIVI\n";
} else {
    $catId = (int)$cat['id'];
    echo "Existing category ID {$catId}: Dây cáp điện CADIVI\n";
}

// Helper function to build search_index
function stripVn(string $str): string {
    $str = preg_replace('/[àáạảãâầấậẩẫăằắặẳẵ]/u', 'a', $str);
    $str = preg_replace('/[ÀÁẠẢÃÂẦẤẬẨẪĂẰẮẶẲẴ]/u', 'A', $str);
    $str = preg_replace('/[èéẹẻẽêềếệểễ]/u', 'e', $str);
    $str = preg_replace('/[ÈÉẸẺẼÊỀẾỆỂỄ]/u', 'E', $str);
    $str = preg_replace('/[ìíịỉĩ]/u', 'i', $str);
    $str = preg_replace('/[ÌÍỊỈĨ]/u', 'I', $str);
    $str = preg_replace('/[òóọỏõôồốộổỗơờớợởỡ]/u', 'o', $str);
    $str = preg_replace('/[ÒÓỌỎÕÔỒỐỘỔỖƠỜỚỢỞỠ]/u', 'O', $str);
    $str = preg_replace('/[ùúụủũưừứựửữ]/u', 'u', $str);
    $str = preg_replace('/[ÙÚỤỦŨƯỪỨỰỬỮ]/u', 'U', $str);
    $str = preg_replace('/[ỳýỵỷỹ]/u', 'y', $str);
    $str = preg_replace('/[ỲÝỴỶỸ]/u', 'Y', $str);
    $str = preg_replace('/[đ]/u', 'd', $str);
    $str = preg_replace('/[Đ]/u', 'D', $str);
    return mb_strtolower($str);
}

// 4. Products Data Array (Authentic Cadivi specs & models)
$cadiviProducts = [
    // --- 1. DÂY ĐƠN MỀM VCM ---
    [
        'name' => 'Dây điện đơn mềm CADIVI VCm 1.0 mm² - Đỏ (Cuộn 100m)',
        'slug' => 'day-dien-don-mem-cadivi-vcm-1-0-do-cuon-100m',
        'model_code' => 'VCM-1.0-DO',
        'sku' => 'CDV-VCM10-RD',
        'price' => 650000,
        'wholesale_price' => 585000,
        'wholesale_min_qty' => 5,
        'unit' => 'Cuộn 100m',
        'thumbnail' => $localImgs['vcm_do'],
        'short_desc' => 'Dây điện đơn mềm ruột đồng 1 lõi nhiều sợi, vỏ bọc PVC cao cấp cách điện 450/750V, chuyên dùng đi ống gen, bảng điện dân dụng.',
        'specs' => [
            'Thương hiệu' => 'CADIVI (Việt Nam)',
            'Mã hiệu' => 'VCm (Cu/PVC)',
            'Tiết diện ruột dẫn' => '1.0 mm²',
            'Kết cấu ruột dẫn' => 'Nhiều sợi đồng mềm xoắn đồng tâm',
            'Cấp điện áp' => '450/750V',
            'Vỏ cách điện' => 'PVC chịu nhiệt 70°C',
            'Màu sắc' => 'Đỏ',
            'Quy cách đóng gói' => 'Cuộn tròn 100 mét',
            'Tiêu chuẩn' => 'TCVN 6610-3 / IEC 60227-3',
        ],
    ],
    [
        'name' => 'Dây điện đơn mềm CADIVI VCm 1.5 mm² - Đỏ (Cuộn 100m)',
        'slug' => 'day-dien-don-mem-cadivi-vcm-1-5-do-cuon-100m',
        'model_code' => 'VCM-1.5-DO',
        'sku' => 'CDV-VCM15-RD',
        'price' => 950000,
        'wholesale_price' => 855000,
        'wholesale_min_qty' => 5,
        'unit' => 'Cuộn 100m',
        'thumbnail' => $localImgs['vcm_do'],
        'short_desc' => 'Dây điện đơn mềm ruột đồng CADIVI 1.5 mm² màu đỏ, phù hợp cho hệ thống đèn chiếu sáng, công tắc Panasonic và quạt điện.',
        'specs' => [
            'Thương hiệu' => 'CADIVI',
            'Mã hiệu' => 'VCm 1.5',
            'Tiết diện' => '1.5 mm²',
            'Vỏ bọc' => 'Nhựa PVC cao cấp chống cháy lan',
            'Cấp điện áp' => '450/750V',
            'Màu sắc' => 'Đỏ (Dây pha L)',
            'Độ dài' => 'Cuộn 100m',
            'Tiêu chuẩn' => 'TCVN 6610-3',
        ],
    ],
    [
        'name' => 'Dây điện đơn mềm CADIVI VCm 1.5 mm² - Xanh Dương (Cuộn 100m)',
        'slug' => 'day-dien-don-mem-cadivi-vcm-1-5-xanh-duong-cuon-100m',
        'model_code' => 'VCM-1.5-XANH',
        'sku' => 'CDV-VCM15-BL',
        'price' => 950000,
        'wholesale_price' => 855000,
        'wholesale_min_qty' => 5,
        'unit' => 'Cuộn 100m',
        'thumbnail' => $localImgs['vcm_xanh'],
        'short_desc' => 'Dây điện đơn mềm ruột đồng CADIVI 1.5 mm² màu xanh dương dùng cho dây trung tính (N) trong hệ thống điện nhà.',
        'specs' => [
            'Thương hiệu' => 'CADIVI',
            'Mã hiệu' => 'VCm 1.5',
            'Tiết diện' => '1.5 mm²',
            'Vỏ bọc' => 'PVC 70°C',
            'Cấp điện áp' => '450/750V',
            'Màu sắc' => 'Xanh dương (Dây nguội N)',
            'Độ dài' => 'Cuộn 100m',
            'Tiêu chuẩn' => 'TCVN 6610-3',
        ],
    ],
    [
        'name' => 'Dây điện đơn mềm CADIVI VCm 1.5 mm² - Vàng (Cuộn 100m)',
        'slug' => 'day-dien-don-mem-cadivi-vcm-1-5-vang-cuon-100m',
        'model_code' => 'VCM-1.5-VANG',
        'sku' => 'CDV-VCM15-YL',
        'price' => 950000,
        'wholesale_price' => 855000,
        'wholesale_min_qty' => 5,
        'unit' => 'Cuộn 100m',
        'thumbnail' => $localImgs['vcm_vang'],
        'short_desc' => 'Dây điện đơn mềm CADIVI VCm 1.5 mm² màu vàng, chuyên dùng cho lộ công tắc đảo chiều, lộ đèn phụ trợ.',
        'specs' => [
            'Thương hiệu' => 'CADIVI',
            'Mã hiệu' => 'VCm 1.5',
            'Tiết diện' => '1.5 mm²',
            'Vỏ bọc' => 'PVC cao cấp',
            'Màu sắc' => 'Vàng',
            'Độ dài' => 'Cuộn 100m',
        ],
    ],
    [
        'name' => 'Dây tiếp địa đơn mềm CADIVI VCm 1.5 mm² - Vàng sọc Xanh Lá (Cuộn 100m)',
        'slug' => 'day-tiep-dia-don-mem-cadivi-vcm-1-5-vang-xanh-cuon-100m',
        'model_code' => 'VCM-1.5-VX',
        'sku' => 'CDV-VCM15-YG',
        'price' => 980000,
        'wholesale_price' => 880000,
        'wholesale_min_qty' => 5,
        'unit' => 'Cuộn 100m',
        'thumbnail' => $localImgs['vcm_vx'],
        'short_desc' => 'Dây tiếp địa (dây te PE) đơn mềm CADIVI 1.5 mm² sọc vàng xanh lá tiêu chuẩn quốc tế, bảo vệ chống giật cho ổ cắm 3 chấu Panasonic.',
        'specs' => [
            'Thương hiệu' => 'CADIVI',
            'Mã hiệu' => 'VCm 1.5 (Earthing Cable)',
            'Tiết diện' => '1.5 mm²',
            'Màu sắc' => 'Vàng sọc Xanh lá (Tiếp địa Earth/PE)',
            'Vỏ bọc' => 'PVC 70°C',
            'Độ dài' => 'Cuộn 100m',
        ],
    ],
    [
        'name' => 'Dây điện đơn mềm CADIVI VCm 2.5 mm² - Đỏ (Cuộn 100m)',
        'slug' => 'day-dien-don-mem-cadivi-vcm-2-5-do-cuon-100m',
        'model_code' => 'VCM-2.5-DO',
        'sku' => 'CDV-VCM25-RD',
        'price' => 1520000,
        'wholesale_price' => 1368000,
        'wholesale_min_qty' => 5,
        'unit' => 'Cuộn 100m',
        'thumbnail' => $localImgs['vcm_do'],
        'short_desc' => 'Dây đơn mềm CADIVI 2.5 mm² màu đỏ tiêu chuẩn dây nguồn cho toàn bộ ổ cắm phòng ngủ, phòng khách, chịu tải đến 4.500W.',
        'specs' => [
            'Thương hiệu' => 'CADIVI',
            'Mã hiệu' => 'VCm 2.5',
            'Tiết diện ruột dẫn' => '2.5 mm²',
            'Dòng định mức' => '20A - 25A',
            'Công suất khuyến nghị' => 'Dưới 4.500W',
            'Vỏ bọc' => 'PVC dẻo đàn hồi cao',
            'Màu sắc' => 'Đỏ (Dây nóng L)',
            'Độ dài' => 'Cuộn 100m',
        ],
    ],
    [
        'name' => 'Dây điện đơn mềm CADIVI VCm 2.5 mm² - Xanh Dương (Cuộn 100m)',
        'slug' => 'day-dien-don-mem-cadivi-vcm-2-5-xanh-duong-cuon-100m',
        'model_code' => 'VCM-2.5-XANH',
        'sku' => 'CDV-VCM25-BL',
        'price' => 1520000,
        'wholesale_price' => 1368000,
        'wholesale_min_qty' => 5,
        'unit' => 'Cuộn 100m',
        'thumbnail' => $localImgs['vcm_xanh'],
        'short_desc' => 'Dây đơn mềm CADIVI 2.5 mm² màu xanh dương làm dây trung tính cho lộ ổ cắm sinh hoạt gia đình và căn hộ.',
        'specs' => [
            'Thương hiệu' => 'CADIVI',
            'Mã hiệu' => 'VCm 2.5',
            'Tiết diện' => '2.5 mm²',
            'Vỏ bọc' => 'PVC 70°C',
            'Màu sắc' => 'Xanh dương (Dây nguội N)',
            'Độ dài' => 'Cuộn 100m',
        ],
    ],
    [
        'name' => 'Dây tiếp địa đơn mềm CADIVI VCm 2.5 mm² - Vàng sọc Xanh Lá (Cuộn 100m)',
        'slug' => 'day-tiep-dia-don-mem-cadivi-vcm-2-5-vang-xanh-cuon-100m',
        'model_code' => 'VCM-2.5-VX',
        'sku' => 'CDV-VCM25-YG',
        'price' => 1550000,
        'wholesale_price' => 1395000,
        'wholesale_min_qty' => 5,
        'unit' => 'Cuộn 100m',
        'thumbnail' => $localImgs['vcm_vx'],
        'short_desc' => 'Dây tiếp địa 2.5 mm² sọc vàng xanh chuyên nối cọc tiếp địa bảo vệ an toàn cho tủ lạnh, máy giặt, bình nóng lạnh, lò vi sóng.',
        'specs' => [
            'Thương hiệu' => 'CADIVI',
            'Mã hiệu' => 'VCm 2.5 (Earthing)',
            'Tiết diện' => '2.5 mm²',
            'Màu sắc' => 'Vàng sọc Xanh lá',
            'Độ dài' => 'Cuộn 100m',
        ],
    ],
    [
        'name' => 'Dây điện đơn mềm CADIVI VCm 4.0 mm² - Đỏ (Cuộn 100m)',
        'slug' => 'day-dien-don-mem-cadivi-vcm-4-0-do-cuon-100m',
        'model_code' => 'VCM-4.0-DO',
        'sku' => 'CDV-VCM40-RD',
        'price' => 2380000,
        'wholesale_price' => 2142000,
        'wholesale_min_qty' => 3,
        'unit' => 'Cuộn 100m',
        'thumbnail' => $localImgs['vcm_do'],
        'short_desc' => 'Dây đơn mềm CADIVI 4.0 mm² tải dòng lớn, chuyên dùng cấp nguồn riêng cho máy lạnh điều hòa 1.5HP - 2.5HP, bình nước nóng, bếp từ đơn.',
        'specs' => [
            'Thương hiệu' => 'CADIVI',
            'Mã hiệu' => 'VCm 4.0',
            'Tiết diện' => '4.0 mm²',
            'Dòng định mức' => '30A - 35A',
            'Công suất' => 'Tối đa 6.500W',
            'Màu sắc' => 'Đỏ',
            'Độ dài' => 'Cuộn 100m',
        ],
    ],
    [
        'name' => 'Dây điện đơn mềm CADIVI VCm 4.0 mm² - Xanh Dương (Cuộn 100m)',
        'slug' => 'day-dien-don-mem-cadivi-vcm-4-0-xanh-duong-cuon-100m',
        'model_code' => 'VCM-4.0-XANH',
        'sku' => 'CDV-VCM40-BL',
        'price' => 2380000,
        'wholesale_price' => 2142000,
        'wholesale_min_qty' => 3,
        'unit' => 'Cuộn 100m',
        'thumbnail' => $localImgs['vcm_xanh'],
        'short_desc' => 'Dây đơn mềm CADIVI 4.0 mm² màu xanh dương làm dây trung tính cho các lộ tải công suất cao.',
        'specs' => [
            'Thương hiệu' => 'CADIVI',
            'Mã hiệu' => 'VCm 4.0',
            'Tiết diện' => '4.0 mm²',
            'Màu sắc' => 'Xanh dương',
            'Độ dài' => 'Cuộn 100m',
        ],
    ],
    [
        'name' => 'Dây điện đơn mềm CADIVI VCm 6.0 mm² - Đỏ (Cuộn 100m)',
        'slug' => 'day-dien-don-mem-cadivi-vcm-6-0-do-cuon-100m',
        'model_code' => 'VCM-6.0-DO',
        'sku' => 'CDV-VCM60-RD',
        'price' => 3550000,
        'wholesale_price' => 3195000,
        'wholesale_min_qty' => 2,
        'unit' => 'Cuộn 100m',
        'thumbnail' => $localImgs['vcm_do'],
        'short_desc' => 'Dây điện đơn mềm 6.0 mm² tải dòng cực đại 45A, chuyên dùng cho bếp từ đôi/ba cao cấp hoặc đường trục nhánh cấp tầng.',
        'specs' => [
            'Thương hiệu' => 'CADIVI',
            'Mã hiệu' => 'VCm 6.0',
            'Tiết diện' => '6.0 mm²',
            'Dòng định mức' => '40A - 45A',
            'Công suất' => 'Lên đến 8.500W',
            'Màu sắc' => 'Đỏ',
            'Độ dài' => 'Cuộn 100m',
        ],
    ],
    [
        'name' => 'Dây điện đơn mềm CADIVI VCm 10.0 mm² - Đỏ (Cuộn 100m)',
        'slug' => 'day-dien-don-mem-cadivi-vcm-10-do-cuon-100m',
        'model_code' => 'VCM-10-DO',
        'sku' => 'CDV-VCM100-RD',
        'price' => 5800000,
        'wholesale_price' => 5220000,
        'wholesale_min_qty' => 1,
        'unit' => 'Cuộn 100m',
        'thumbnail' => $localImgs['vcm_do'],
        'short_desc' => 'Dây điện đơn mềm 10 mm² ruột đồng nhiều sợi mềm uốn cong linh hoạt trong tủ điện phân phối chính, đấu nối Aptomat MCB tổng.',
        'specs' => [
            'Thương hiệu' => 'CADIVI',
            'Mã hiệu' => 'VCm 10',
            'Tiết diện' => '10.0 mm²',
            'Dòng định mức' => '60A - 65A',
            'Vỏ bọc' => 'PVC 70°C',
            'Độ dài' => 'Cuộn 100m',
        ],
    ],

    // --- 2. DÂY ĐÔI MỀM DẸP VCMD ---
    [
        'name' => 'Dây điện đôi mềm dẹp CADIVI VCmd 2x0.75 mm² - Trắng (Cuộn 100m)',
        'slug' => 'day-dien-doi-mem-dep-cadivi-vcmd-2x0-75-trang-cuon-100m',
        'model_code' => 'VCMD-2X0.75',
        'sku' => 'CDV-VCMD075-WH',
        'price' => 720000,
        'wholesale_price' => 648000,
        'wholesale_min_qty' => 5,
        'unit' => 'Cuộn 100m',
        'thumbnail' => $localImgs['vcmd_trang'],
        'short_desc' => 'Dây đôi dẹp 2 ruột đồng mềm bọc PVC dính liền màu trắng tinh tế, chuyên dùng đấu đèn tuýp, quạt hút, đèn trang trí.',
        'specs' => [
            'Thương hiệu' => 'CADIVI',
            'Mã hiệu' => 'VCmd 2x0.75',
            'Số lõi x Tiết diện' => '2 x 0.75 mm²',
            'Kết cấu' => '2 ruột đồng dẹp dính liền',
            'Cấp điện áp' => '300/500V',
            'Vỏ bọc' => 'PVC trắng',
            'Độ dài' => 'Cuộn 100m',
            'Tiêu chuẩn' => 'TCVN 6610-5',
        ],
    ],
    [
        'name' => 'Dây điện đôi mềm dẹp CADIVI VCmd 2x1.5 mm² - Trắng (Cuộn 100m)',
        'slug' => 'day-dien-doi-mem-dep-cadivi-vcmd-2x1-5-trang-cuon-100m',
        'model_code' => 'VCMD-2X1.5-TRANG',
        'sku' => 'CDV-VCMD15-WH',
        'price' => 1450000,
        'wholesale_price' => 1305000,
        'wholesale_min_qty' => 5,
        'unit' => 'Cuộn 100m',
        'thumbnail' => $localImgs['vcmd_trang'],
        'short_desc' => 'Dây đôi mềm dẹp VCmd 2x1.5 mm² thông dụng nhất cho công trình dân dụng, làm dây cắm ổ cắm kéo dài, đi nổi nẹp điện văn phòng.',
        'specs' => [
            'Thương hiệu' => 'CADIVI',
            'Mã hiệu' => 'VCmd 2x1.5',
            'Số lõi x Tiết diện' => '2 x 1.5 mm²',
            'Cấp điện áp' => '0.6/1kV',
            'Vỏ bọc' => 'PVC trắng cách điện 2 lớp',
            'Độ dài' => 'Cuộn 100m',
        ],
    ],
    [
        'name' => 'Dây điện đôi mềm dẹp CADIVI VCmd 2x1.5 mm² - Vàng (Cuộn 100m)',
        'slug' => 'day-dien-doi-mem-dep-cadivi-vcmd-2x1-5-vang-cuon-100m',
        'model_code' => 'VCMD-2X1.5-VANG',
        'sku' => 'CDV-VCMD15-YL',
        'price' => 1450000,
        'wholesale_price' => 1305000,
        'wholesale_min_qty' => 5,
        'unit' => 'Cuộn 100m',
        'thumbnail' => $localImgs['vcmd_vang'],
        'short_desc' => 'Dây điện đôi dẹp CADIVI VCmd 2x1.5 mm² màu vàng, vỏ bọc dày dặn chống rò rỉ điện.',
        'specs' => [
            'Thương hiệu' => 'CADIVI',
            'Mã hiệu' => 'VCmd 2x1.5',
            'Số lõi x Tiết diện' => '2 x 1.5 mm²',
            'Màu sắc' => 'Vàng',
            'Độ dài' => 'Cuộn 100m',
        ],
    ],
    [
        'name' => 'Dây điện đôi mềm dẹp CADIVI VCmd 2x2.5 mm² - Trắng (Cuộn 100m)',
        'slug' => 'day-dien-doi-mem-dep-cadivi-vcmd-2x2-5-trang-cuon-100m',
        'model_code' => 'VCMD-2X2.5-TRANG',
        'sku' => 'CDV-VCMD25-WH',
        'price' => 2350000,
        'wholesale_price' => 2115000,
        'wholesale_min_qty' => 3,
        'unit' => 'Cuộn 100m',
        'thumbnail' => $localImgs['vcmd_trang'],
        'short_desc' => 'Dây đôi mềm dẹp CADIVI 2x2.5 mm² tải cao cho ổ cắm công suất lớn, máy bơm, máy hút bụi, lò nướng.',
        'specs' => [
            'Thương hiệu' => 'CADIVI',
            'Mã hiệu' => 'VCmd 2x2.5',
            'Số lõi x Tiết diện' => '2 x 2.5 mm²',
            'Cấp điện áp' => '0.6/1kV',
            'Vỏ bọc' => 'PVC trắng',
            'Độ dài' => 'Cuộn 100m',
        ],
    ],

    // --- 3. DÂY ĐÔI TRÒN VCV ---
    [
        'name' => 'Cáp điện đôi tròn CADIVI VCV 2x1.5 mm² (Cuộn 100m)',
        'slug' => 'cap-dien-doi-tron-cadivi-vcv-2x1-5-cuon-100m',
        'model_code' => 'VCV-2X1.5',
        'sku' => 'CDV-VCV215',
        'price' => 1750000,
        'wholesale_price' => 1575000,
        'wholesale_min_qty' => 3,
        'unit' => 'Cuộn 100m',
        'thumbnail' => $localImgs['vcv_tron'],
        'short_desc' => 'Cáp điện đôi dạng tròn 2 lõi ruột đồng cách điện PVC, vỏ bọc tròn màu xám/đen chịu lực va đập tốt, dùng luồn ống ngầm hoặc đi ngoài trời.',
        'specs' => [
            'Thương hiệu' => 'CADIVI',
            'Mã hiệu' => 'VCV 2x1.5',
            'Kết cấu' => '2 lõi ruột đồng tròn bọc vỏ PVC tròn',
            'Cấp điện áp' => '0.6/1kV',
            'Độ dài' => 'Cuộn 100m',
        ],
    ],
    [
        'name' => 'Cáp điện đôi tròn CADIVI VCV 2x2.5 mm² (Cuộn 100m)',
        'slug' => 'cap-dien-doi-tron-cadivi-vcv-2x2-5-cuon-100m',
        'model_code' => 'VCV-2X2.5',
        'sku' => 'CDV-VCV225',
        'price' => 2750000,
        'wholesale_price' => 2475000,
        'wholesale_min_qty' => 3,
        'unit' => 'Cuộn 100m',
        'thumbnail' => $localImgs['vcv_tron'],
        'short_desc' => 'Cáp đôi tròn CADIVI 2x2.5 mm² chất lượng cao, vỏ PVC dẻo dai chống mài mòn, chống ẩm ướt tốt.',
        'specs' => [
            'Thương hiệu' => 'CADIVI',
            'Mã hiệu' => 'VCV 2x2.5',
            'Số lõi x Tiết diện' => '2 x 2.5 mm²',
            'Cấp điện áp' => '0.6/1kV',
            'Độ dài' => 'Cuộn 100m',
        ],
    ],

    // --- 4. CÁP ĐIỆN LỰC CV 1 LÕI ---
    [
        'name' => 'Cáp điện lực 1 lõi ruột đồng CADIVI CV 4.0 mm² (0.6/1kV)',
        'slug' => 'cap-dien-luc-1-loi-ruot-dong-cadivi-cv-4-0-mm2',
        'model_code' => 'CV-4.0',
        'sku' => 'CDV-CV40',
        'price' => 22900,
        'wholesale_price' => 20600,
        'wholesale_min_qty' => 50,
        'unit' => 'Mét',
        'thumbnail' => $localImgs['cv_don'],
        'short_desc' => 'Cáp điện lực hạ thế ruột đồng 1 lõi 7 sợi đồng bện tròn nén, cách điện PVC 0.6/1kV, làm đường dây trục phân phối.',
        'specs' => [
            'Thương hiệu' => 'CADIVI',
            'Mã hiệu' => 'CV 4.0 (Cu/PVC)',
            'Tiết diện' => '4.0 mm²',
            'Cấp điện áp' => '0.6/1kV',
            'Ruột dẫn' => 'Đồng 7 sợi bện',
            'Tiêu chuẩn' => 'AS/NZS 5000.1 / TCVN 6610-3',
        ],
    ],
    [
        'name' => 'Cáp điện lực 1 lõi ruột đồng CADIVI CV 6.0 mm² (0.6/1kV)',
        'slug' => 'cap-dien-luc-1-loi-ruot-dong-cadivi-cv-6-0-mm2',
        'model_code' => 'CV-6.0',
        'sku' => 'CDV-CV60',
        'price' => 33500,
        'wholesale_price' => 30100,
        'wholesale_min_qty' => 50,
        'unit' => 'Mét',
        'thumbnail' => $localImgs['cv_don'],
        'short_desc' => 'Cáp điện lực CV 6.0 mm² ruột đồng nguyên chất, khả năng truyền dẫn dòng điện ổn định, tổn hao điện áp cực thấp.',
        'specs' => [
            'Thương hiệu' => 'CADIVI',
            'Mã hiệu' => 'CV 6.0',
            'Tiết diện' => '6.0 mm²',
            'Cấp điện áp' => '0.6/1kV',
        ],
    ],
    [
        'name' => 'Cáp điện lực 1 lõi ruột đồng CADIVI CV 10.0 mm² (0.6/1kV)',
        'slug' => 'cap-dien-luc-1-loi-ruot-dong-cadivi-cv-10-mm2',
        'model_code' => 'CV-10',
        'sku' => 'CDV-CV100',
        'price' => 56000,
        'wholesale_price' => 50400,
        'wholesale_min_qty' => 50,
        'unit' => 'Mét',
        'thumbnail' => $localImgs['cv_don'],
        'short_desc' => 'Cáp điện lực CV 10 mm² làm dây nguồn chính từ công tơ điện lực vào tủ điện tổng căn hộ, nhà phố.',
        'specs' => [
            'Thương hiệu' => 'CADIVI',
            'Mã hiệu' => 'CV 10',
            'Tiết diện' => '10.0 mm²',
            'Cấp điện áp' => '0.6/1kV',
        ],
    ],
    [
        'name' => 'Cáp điện lực 1 lõi ruột đồng CADIVI CV 16.0 mm² (0.6/1kV)',
        'slug' => 'cap-dien-luc-1-loi-ruot-dong-cadivi-cv-16-mm2',
        'model_code' => 'CV-16',
        'sku' => 'CDV-CV160',
        'price' => 90000,
        'wholesale_price' => 81000,
        'wholesale_min_qty' => 30,
        'unit' => 'Mét',
        'thumbnail' => $localImgs['cv_don'],
        'short_desc' => 'Cáp điện lực CV 16 mm² tải dòng liên tục đến 95A, cấp nguồn tổng cho biệt thự, khách sạn, xưởng sản xuất nhỏ.',
        'specs' => [
            'Thương hiệu' => 'CADIVI',
            'Mã hiệu' => 'CV 16',
            'Tiết diện' => '16.0 mm²',
            'Cấp điện áp' => '0.6/1kV',
        ],
    ],
    [
        'name' => 'Cáp điện lực 1 lõi ruột đồng CADIVI CV 25.0 mm² (0.6/1kV)',
        'slug' => 'cap-dien-luc-1-loi-ruot-dong-cadivi-cv-25-mm2',
        'model_code' => 'CV-25',
        'sku' => 'CDV-CV250',
        'price' => 142000,
        'wholesale_price' => 127800,
        'wholesale_min_qty' => 20,
        'unit' => 'Mét',
        'thumbnail' => $localImgs['cv_don'],
        'short_desc' => 'Cáp điện lực CV 25 mm² truyền tải điện năng chính từ trạm biến áp hoặc tủ tổng MSB công trình.',
        'specs' => [
            'Thương hiệu' => 'CADIVI',
            'Mã hiệu' => 'CV 25',
            'Tiết diện' => '25.0 mm²',
            'Cấp điện áp' => '0.6/1kV',
        ],
    ],

    // --- 5. CÁP HẠ THẾ CXV (XLPE/PVC) ---
    [
        'name' => 'Cáp ngầm hạ thế CADIVI CXV 2x2.5 mm² (0.6/1kV)',
        'slug' => 'cap-ngam-ha-the-cadivi-cxv-2x2-5-mm2',
        'model_code' => 'CXV-2X2.5',
        'sku' => 'CDV-CXV225',
        'price' => 29500,
        'wholesale_price' => 26500,
        'wholesale_min_qty' => 50,
        'unit' => 'Mét',
        'thumbnail' => $localImgs['cxv_2'],
        'short_desc' => 'Cáp ngầm hạ thế 2 lõi ruột đồng, cách điện XLPE chịu nhiệt cao 90°C, vỏ ngoài PVC chống thấm nước, chuyên đi ngầm sân vườn, đèn đường.',
        'specs' => [
            'Thương hiệu' => 'CADIVI',
            'Mã hiệu' => 'CXV 2x2.5 (Cu/XLPE/PVC)',
            'Số lõi x Tiết diện' => '2 x 2.5 mm²',
            'Cách điện' => 'XLPE chịu nhiệt 90°C',
            'Vỏ bảo vệ' => 'PVC chống ẩm, chống lão hóa',
            'Cấp điện áp' => '0.6/1kV',
            'Tiêu chuẩn' => 'TCVN 5935-1 / IEC 60502-1',
        ],
    ],
    [
        'name' => 'Cáp ngầm hạ thế CADIVI CXV 2x4.0 mm² (0.6/1kV)',
        'slug' => 'cap-ngam-ha-the-cadivi-cxv-2x4-0-mm2',
        'model_code' => 'CXV-2X4.0',
        'sku' => 'CDV-CXV240',
        'price' => 44000,
        'wholesale_price' => 39600,
        'wholesale_min_qty' => 50,
        'unit' => 'Mét',
        'thumbnail' => $localImgs['cxv_2'],
        'short_desc' => 'Cáp điện ngầm CXV 2x4.0 mm² 0.6/1kV ruột đồng cách điện XLPE an toàn tuyệt đối, chịu tải thời tiết ẩm ướt khắc nghiệt.',
        'specs' => [
            'Thương hiệu' => 'CADIVI',
            'Mã hiệu' => 'CXV 2x4.0',
            'Số lõi x Tiết diện' => '2 x 4.0 mm²',
            'Cấp điện áp' => '0.6/1kV',
        ],
    ],
    [
        'name' => 'Cáp ngầm hạ thế CADIVI CXV 2x6.0 mm² (0.6/1kV)',
        'slug' => 'cap-ngam-ha-the-cadivi-cxv-2x6-0-mm2',
        'model_code' => 'CXV-2X6.0',
        'sku' => 'CDV-CXV260',
        'price' => 64000,
        'wholesale_price' => 57600,
        'wholesale_min_qty' => 30,
        'unit' => 'Mét',
        'thumbnail' => $localImgs['cxv_2'],
        'short_desc' => 'Cáp hạ thế CXV 2x6.0 mm² dùng cho nguồn vào tổng nhà dân dụng từ cột điện lực vào đồng hồ.',
        'specs' => [
            'Thương hiệu' => 'CADIVI',
            'Mã hiệu' => 'CXV 2x6.0',
            'Số lõi x Tiết diện' => '2 x 6.0 mm²',
            'Cấp điện áp' => '0.6/1kV',
        ],
    ],
    [
        'name' => 'Cáp điện hạ thế 3 lõi CADIVI CXV 3x4.0 mm² (0.6/1kV)',
        'slug' => 'cap-dien-ha-the-3-loi-cadivi-cxv-3x4-0-mm2',
        'model_code' => 'CXV-3X4.0',
        'sku' => 'CDV-CXV340',
        'price' => 65000,
        'wholesale_price' => 58500,
        'wholesale_min_qty' => 30,
        'unit' => 'Mét',
        'thumbnail' => $localImgs['cxv_3'],
        'short_desc' => 'Cáp 3 lõi CXV 3x4.0 mm² dùng cấp nguồn 3 pha hoặc 1 pha kèm dây tiếp địa an toàn cho máy bơm, thang máy, máy phát điện.',
        'specs' => [
            'Thương hiệu' => 'CADIVI',
            'Mã hiệu' => 'CXV 3x4.0',
            'Số lõi x Tiết diện' => '3 x 4.0 mm²',
            'Cấp điện áp' => '0.6/1kV',
        ],
    ],
    [
        'name' => 'Cáp điện hạ thế 4 lõi CADIVI CXV 4x4.0 mm² (0.6/1kV)',
        'slug' => 'cap-dien-ha-the-4-loi-cadivi-cxv-4x4-0-mm2',
        'model_code' => 'CXV-4X4.0',
        'sku' => 'CDV-CXV440',
        'price' => 86000,
        'wholesale_price' => 77400,
        'wholesale_min_qty' => 30,
        'unit' => 'Mét',
        'thumbnail' => $localImgs['cxv_4'],
        'short_desc' => 'Cáp 4 lõi CXV 4x4.0 mm² (3 pha + 1 trung tính) chuyên dùng cho nguồn điện 3 pha công trình, xưởng sản xuất.',
        'specs' => [
            'Thương hiệu' => 'CADIVI',
            'Mã hiệu' => 'CXV 4x4.0',
            'Số lõi x Tiết diện' => '4 x 4.0 mm²',
            'Cấp điện áp' => '0.6/1kV',
        ],
    ],
    [
        'name' => 'Cáp điện lực 3 pha 4 dây CADIVI CXV 3x16 + 1x10 mm² (0.6/1kV)',
        'slug' => 'cap-dien-luc-3-pha-4-day-cadivi-cxv-3x16-1x10-mm2',
        'model_code' => 'CXV-3X16+1X10',
        'sku' => 'CDV-CXV316110',
        'price' => 325000,
        'wholesale_price' => 292500,
        'wholesale_min_qty' => 20,
        'unit' => 'Mét',
        'thumbnail' => $localImgs['cxv_4'],
        'short_desc' => 'Cáp điện lực hạ thế 3 lõi pha 16mm² + 1 lõi trung tính 10mm² bọc XLPE/PVC, chuyên dùng cấp nguồn chính 3 pha cho tòa nhà, nhà hàng, khách sạn.',
        'specs' => [
            'Thương hiệu' => 'CADIVI',
            'Mã hiệu' => 'CXV 3x16 + 1x10 mm²',
            'Kết cấu' => '3 lõi pha 16mm² + 1 lõi N 10mm²',
            'Cấp điện áp' => '0.6/1kV',
            'Ruột dẫn' => 'Đồng ép chặt',
            'Tiêu chuẩn' => 'TCVN 5935-1 / IEC 60502-1',
        ],
    ],
];

echo "Preparing to insert " . count($cadiviProducts) . " Cadivi products...\n";

$stmtCheck = $pdo->prepare("SELECT id FROM products WHERE model_code = :code");
$stmtInsert = $pdo->prepare("
    INSERT INTO products (
        category_id, series_id, name, slug, sku, model_code, short_desc, description,
        price, wholesale_price, wholesale_min_qty, stock, unit, thumbnail, gallery, specs,
        is_featured, status, search_index, created_at, updated_at
    ) VALUES (
        :category_id, NULL, :name, :slug, :sku, :model_code, :short_desc, :description,
        :price, :wholesale_price, :wholesale_min_qty, 1000, :unit, :thumbnail, :gallery, :specs,
        1, 1, :search_index, NOW(), NOW()
    )
");

$stmtUpdate = $pdo->prepare("
    UPDATE products SET
        category_id = :category_id,
        name = :name,
        slug = :slug,
        sku = :sku,
        short_desc = :short_desc,
        description = :description,
        price = :price,
        wholesale_price = :wholesale_price,
        wholesale_min_qty = :wholesale_min_qty,
        unit = :unit,
        thumbnail = :thumbnail,
        gallery = :gallery,
        specs = :specs,
        search_index = :search_index,
        updated_at = NOW()
    WHERE id = :id
");

$inserted = 0;
$updated = 0;

foreach ($cadiviProducts as $p) {
    // Generate detailed description
    $desc = "<div class=\"product-full-desc\">\n";
    $desc .= "<h3>Giới thiệu sản phẩm " . htmlspecialchars($p['name'], ENT_QUOTES, 'UTF-8') . "</h3>\n";
    $desc .= "<p>" . htmlspecialchars($p['short_desc'], ENT_QUOTES, 'UTF-8') . "</p>\n";
    $desc .= "<h4>Ưu điểm nổi bật của Dây & Cáp điện CADIVI chính hãng:</h4>\n";
    $desc .= "<ul>\n";
    $desc .= "  <li><strong>100% Đồng nguyên chất:</strong> Ruột dẫn đồng tinh chất 99.99%, khả năng dẫn điện tối đa, tiết kiệm điện năng và chống sụt áp vượt trội.</li>\n";
    $desc .= "  <li><strong>Cách điện an toàn tuyệt đối:</strong> Lớp vỏ nhựa PVC/XLPE dẻo dai, chịu nhiệt tốt, chống rò rỉ điện và hạn chế tối đa nguy cơ chập cháy.</li>\n";
    $desc .= "  <li><strong>Đạt chuẩn quốc tế:</strong> Sản xuất theo hệ thống quản lý chất lượng ISO 9001:2015, tiêu chuẩn TCVN và IEC khắt khe.</li>\n";
    $desc .= "  <li><strong>Độ bền trên 30 năm:</strong> Chống oxy hóa ruột đồng, thích ứng với mọi điều kiện khí hậu nhiệt đới gió mùa tại Việt Nam.</li>\n";
    $desc .= "</ul>\n";
    $desc .= "<h4>Thông số kỹ thuật chi tiết:</h4>\n";
    $desc .= "<table class=\"table table--bordered specs-table\">\n<tbody>\n";
    foreach ($p['specs'] as $k => $v) {
        $desc .= "  <tr><th>" . htmlspecialchars($k, ENT_QUOTES, 'UTF-8') . "</th><td>" . htmlspecialchars($v, ENT_QUOTES, 'UTF-8') . "</td></tr>\n";
    }
    $desc .= "</tbody>\n</table>\n</div>";

    // Build search index
    $idxParts = [
        $p['name'],
        stripVn($p['name']),
        $p['model_code'],
        str_replace(['-', ' ', '.', '/'], '', $p['model_code']),
        $p['sku'],
        'Dây cáp điện CADIVI',
        'day cap dien cadivi cadivi ruot dong nguyen chat',
        $p['short_desc'],
        stripVn($p['short_desc']),
    ];
    foreach ($p['specs'] as $k => $v) {
        $idxParts[] = $v;
        $idxParts[] = stripVn($v);
    }
    $searchIndex = mb_strtolower(implode(' ', array_unique($idxParts)));

    $gallery = json_encode([$p['thumbnail']]);
    $specsJson = json_encode($p['specs'], JSON_UNESCAPED_UNICODE);

    $stmtCheck->execute(['code' => $p['model_code']]);
    $exists = $stmtCheck->fetch(PDO::FETCH_ASSOC);

    if ($exists) {
        $stmtUpdate->execute([
            'category_id' => $catId,
            'name' => $p['name'],
            'slug' => $p['slug'],
            'sku' => $p['sku'],
            'short_desc' => $p['short_desc'],
            'description' => $desc,
            'price' => $p['price'],
            'wholesale_price' => $p['wholesale_price'],
            'wholesale_min_qty' => $p['wholesale_min_qty'],
            'unit' => $p['unit'],
            'thumbnail' => $p['thumbnail'],
            'gallery' => $gallery,
            'specs' => $specsJson,
            'search_index' => $searchIndex,
            'id' => $exists['id'],
        ]);
        $updated++;
        echo "Updated [ID {$exists['id']}] {$p['model_code']}\n";
    } else {
        $stmtInsert->execute([
            'category_id' => $catId,
            'name' => $p['name'],
            'slug' => $p['slug'],
            'sku' => $p['sku'],
            'model_code' => $p['model_code'],
            'short_desc' => $p['short_desc'],
            'description' => $desc,
            'price' => $p['price'],
            'wholesale_price' => $p['wholesale_price'],
            'wholesale_min_qty' => $p['wholesale_min_qty'],
            'unit' => $p['unit'],
            'thumbnail' => $p['thumbnail'],
            'gallery' => $gallery,
            'specs' => $specsJson,
            'search_index' => $searchIndex,
        ]);
        $inserted++;
        echo "Inserted {$p['model_code']}: {$p['name']}\n";
    }
}

echo "\n============================================\n";
echo "Cadivi products processing complete!\n";
echo "Inserted: {$inserted} new products\n";
echo "Updated: {$updated} products\n";
