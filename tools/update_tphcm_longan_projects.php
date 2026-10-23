<?php
$pdo = new PDO('mysql:host=localhost;dbname=panasonic_shop;charset=utf8mb4', 'root', '');

$pdo->exec('TRUNCATE TABLE projects');

$projects = [
    [
        'title'           => 'Quần Thể Landmark 81 & Vinhomes Central Park',
        'slug'            => 'landmark-81-vinhomes-central-park',
        'category'        => 'Đại Dự Án Đô Thị & Tòa Nhà Chọc Trời',
        'client_name'     => 'Tập đoàn Vingroup',
        'location'        => '720A Điện Biên Phủ, Phường 22, Quận Bình Thạnh, TP. Hồ Chí Minh',
        'scale'           => 'Tòa tháp Landmark 81 tầng & 17 block căn hộ cao cấp ven sông Sài Gòn',
        'supplied_items'  => 'Cung cấp hệ thống cầu dao tự động MCB 6kA-10kA, aptomat chống rò RCBO Panasonic, tủ điện phân phối hạ thế, công tắc ổ cắm cao cấp dòng Moderva và Gen-X.',
        'completion_year' => 2024,
        'thumbnail'       => 'uploads/projects/landmark-81.jpg',
        'sort_order'      => 1,
    ],
    [
        'title'           => 'Khu Đô Thị Sinh Thái Sông Nước Waterpoint Nam Long',
        'slug'            => 'waterpoint-nam-long-ben-luc-long-an',
        'category'        => 'Đại Đô Thị Sinh Thái Ven Sông',
        'client_name'     => 'Tập đoàn Nam Long & Nishi Nippon Railroad (Nhật Bản)',
        'location'        => 'Tỉnh Lộ 830, Xã An Thạnh, Huyện Bến Lức, Tỉnh Long An',
        'scale'           => '355 ha – Hơn 10.000 căn nhà phố, biệt thự đơn lập, dinh thự ven sông Vàm Cỏ Đông',
        'supplied_items'  => 'Cung cấp trọn gói thiết bị điện Panasonic: Công tắc ổ cắm dòng Halumie & Wide Series, cầu dao an toàn MCB 6kA, tủ điện âm tường Nanoco, đèn LED Downlight đổi màu và quạt trần 5 cánh Panasonic.',
        'completion_year' => 2025,
        'thumbnail'       => 'uploads/projects/waterpoint-nam-long.jpg',
        'sort_order'      => 2,
    ],
    [
        'title'           => 'Khu Căn Hộ Hạng Sang Masterise Lumière Riverside',
        'slug'            => 'masterise-lumiere-riverside-thao-dien',
        'category'        => 'Căn Hộ Hạng Sang & Penthouse',
        'client_name'     => 'Masterise Homes (Tập đoàn Masterise)',
        'location'        => '259 Xa Lộ Hà Nội, Phường An Phú, TP. Thủ Đức, TP. Hồ Chí Minh',
        'scale'           => '2 tháp đôi 44 tầng với 1.030 căn hộ hạng sang chuẩn xanh',
        'supplied_items'  => 'Đèn LED âm trần Downlight Color Rim 3 màu cao cấp, công tắc mặt vuông Panasonic Minerva màu xám ánh kim, hệ thống quạt thông gió âm trần siêu êm Panasonic FV-15EGD2.',
        'completion_year' => 2024,
        'thumbnail'       => 'uploads/projects/masterise-lumiere.jpg',
        'sort_order'      => 3,
    ],
    [
        'title'           => 'Đại Đô Thị Vệ Tinh T&T City Millennia Cần Giuộc',
        'slug'            => 'tt-city-millennia-can-giuoc-long-an',
        'category'        => 'Khu Đô Thị Kiểu Mẫu Nam Sài Gòn',
        'client_name'     => 'Tập đoàn T&T Group',
        'location'        => 'Xã Long Hậu, Huyện Cần Giuộc, Tỉnh Long An (Giáp ranh Nhà Bè, TP.HCM)',
        'scale'           => '267 ha – Quần thể nhà phố thương mại shophouse, biệt thự ven sông và chung cư cao tầng',
        'supplied_items'  => 'Cung cấp thiết bị đóng cắt tủ hạ thế, MCB 1P/2P 16A-63A, ổ cắm chống thấm nước ngoài trời IP66 Panasonic, đèn LED chiếu sáng kiến trúc ngoài trời.',
        'completion_year' => 2025,
        'thumbnail'       => 'uploads/projects/tt-city-millennia.jpg',
        'sort_order'      => 4,
    ],
    [
        'title'           => 'Khu Đô Thị Bán Đảo Vạn Phúc City Thủ Đức',
        'slug'            => 'van-phuc-city-thu-duc-tphcm',
        'category'        => 'Khu Đô Thị Ven Sông Sài Gòn',
        'client_name'     => 'Tập đoàn Bất động sản Đại Phúc (Van Phuc Group)',
        'location'        => 'Quốc lộ 13, Phường Hiệp Bình Phước, TP. Thủ Đức, TP. Hồ Chí Minh',
        'scale'           => '198 ha – Bán đảo 3 mặt giáp sông Sài Gòn, phố thương mại sầm uất',
        'supplied_items'  => 'Thiết bị điện hoàn thiện cao cấp Panasonic Wide Series màu trắng tuyết, tủ điện an toàn 12-18 modules, cảm biến chuyển động gắn trần và hệ thống quạt hút nối ống gió Panasonic.',
        'completion_year' => 2024,
        'thumbnail'       => 'uploads/projects/van-phuc-city.jpg',
        'sort_order'      => 5,
    ],
    [
        'title'           => 'Khu Đô Thị Đảo Nhật Mizuki Park Bình Chánh',
        'slug'            => 'mizuki-park-binh-chanh-tphcm',
        'category'        => 'Khu Căn Hộ & Biệt Thự Phong Cách Nhật',
        'client_name'     => 'Tập đoàn Nam Long & Hankyu Hanshin Properties (Nhật Bản)',
        'location'        => 'Đại lộ Nguyễn Văn Linh, Xã Bình Hưng, Huyện Bình Chánh, TP. Hồ Chí Minh',
        'scale'           => '26 ha – Căn hộ Flora Mizuki & Nhà phố biệt thự đảo Valora Mizuki',
        'supplied_items'  => '100% căn hộ lắp đặt công tắc ổ cắm Panasonic Full Color & Wide, hệ thống aptomat bảo vệ quá tải và ngắn mạch tiêu chuẩn JIS Nhật Bản, chuông cửa màn hình và đèn LED chiếu sáng thông minh.',
        'completion_year' => 2024,
        'thumbnail'       => 'uploads/projects/mizuki-park.jpg',
        'sort_order'      => 6,
    ],
    [
        'title'           => 'Khu Công Nghiệp & Cụm Nhà Xưởng Kizuna Long Hậu',
        'slug'            => 'kcn-long-hau-kizuna-can-giuoc-long-an',
        'category'        => 'Khu Công Nghiệp & Nhà Xưởng Xanh',
        'client_name'     => 'Công ty Cổ phần Long Hậu & Kizuna JV Corporation',
        'location'        => 'KCN Long Hậu, Xã Long Hậu, Huyện Cần Giuộc, Tỉnh Long An',
        'scale'           => '500 ha nhà xưởng dịch vụ tiêu chuẩn công nghiệp Nhật Bản',
        'supplied_items'  => 'Cầu dao khối MCCB Panasonic 3P/4P từ 100A đến 800A (dòng cắt 36kA-50kA), phích cắm & ổ cắm công nghiệp kín nước PCE Panasonic IP67, quạt hút thông gió công nghiệp lưu lượng gió lớn.',
        'completion_year' => 2025,
        'thumbnail'       => 'uploads/projects/kcn-long-hau.jpg',
        'sort_order'      => 7,
    ]
];

$stmt = $pdo->prepare('INSERT INTO projects (title, slug, category, client_name, location, scale, supplied_items, completion_year, thumbnail, is_featured, sort_order, status, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 1, ?, 1, NOW())');

foreach ($projects as $pj) {
    $stmt->execute([
        $pj['title'],
        $pj['slug'],
        $pj['category'],
        $pj['client_name'],
        $pj['location'],
        $pj['scale'],
        $pj['supplied_items'],
        $pj['completion_year'],
        $pj['thumbnail'],
        $pj['sort_order']
    ]);
}

echo "Successfully populated " . count($projects) . " projects focused on TP.HCM and Long An!\n";
