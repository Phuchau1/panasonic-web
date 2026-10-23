<?php
/** @var array $category */
/** @var array $sections */

$catSlug = $category['slug'] ?? '';

// Cấu hình hình ảnh thật, thông số và cam kết phân phối cho từng danh mục
$catMetaLookup = [
    'cong-tac-o-cam' => [
        'hero_img'     => 'uploads/products/bo-cong-tac-o-cam-panasonic-refina-mau-xam-anh-kim-1.jpg',
        'badge'        => 'TIÊU CHUẨN JIS NHẬT BẢN • VẬT LIỆU CHỐNG CHÁY UREA RESIN',
        'heading'      => 'Công Tắc & Ổ Cắm Panasonic Chính Hãng',
        'sub_headline' => 'Dẫn đầu thị trường với 6 dòng series tinh tế từ phổ thông đến biệt thự cao cấp (Moderva, Gen-X, Minerva, Halumie, Wide, Refina). Tiếp điểm hợp kim bạc nguyên chất đạt tuổi thọ 40.000 lần bật tắt và ổ cắm 16A siêu bền.',
        'tag'          => '6 Dòng Series Tinh Tế',
        'series_desc'  => [
            'Gen-X'   => 'Nhôm xước phay siêu mỏng 2mm',
            'Minerva' => 'Mặt phẳng tràn viền nguyên khối',
            'Moderva' => 'Mặt vuông chuẩn phong cách Châu Âu',
            'Refina'  => 'Viền ánh kim bo cong thanh thoát',
            'Wide'    => 'Dòng thông dụng & bán chạy nhất',
            'Halumie' => 'Bo cong trang nhã, phím êm ái',
        ]
    ],
    'thiet-bi-dong-cat' => [
        'hero_img'     => 'uploads/banners/banner-2-aptomat-mcb.jpg',
        'badge'        => 'TIÊU CHUẨN IEC 60898 & JIS C 8201 • BẢO VỆ ĐA TẦNG TOÀN DIỆN',
        'heading'      => 'Thiết Bị Đóng Cắt MCB, MCCB & RCBO Panasonic',
        'sub_headline' => 'Hệ thống thiết bị đóng cắt bảo vệ điện an toàn tối đa: Aptomat tép MCB, cầu dao khối MCCB và aptomat chống dòng rò RCBO/ELB. Buồng dập hồ quang hợp kim bạc siêu tốc, ngắt mạch trong 0.03 giây, chống chập cháy tuyệt đối.',
        'tag'          => 'Dòng cắt 6kA - 50kA • CO/CQ',
        'series_desc'  => [
            'Aptomat MCB'     => 'Bảo vệ quá tải 6kA - 10kA (1P - 4P)',
            'Cầu Dao MCCB'    => 'Khối đúc công nghiệp tải lớn 10A - 800A',
            'Chống Giật RCBO' => 'Tự ngắt dòng rò 0.03s siêu an toàn',
            'Tủ Điện Âm Tường'=> 'Mặt kim loại sang trọng từ 8 - 40 đường',
        ]
    ],
    'den-led-chieu-sang' => [
        'hero_img'     => 'uploads/banners/banner-3-den-led-downlight.jpg',
        'badge'        => 'CHIP LED NHẬT BẢN • TIẾT KIỆM 85% ĐIỆN NĂNG • CRI > 80',
        'heading'      => 'Đèn LED Chiếu Sáng Panasonic Cao Cấp',
        'sub_headline' => 'Công nghệ chiếu sáng thân thiện mắt (Flicker-Free không nhấp nháy), độ hoàn màu cao tái hiện màu sắc chân thực. Thân nhôm tản nhiệt nguyên khối giúp kéo dài tuổi thọ chip LED trên 25.000 giờ, đổi mới 24 tháng chính hãng.',
        'tag'          => 'Tuổi thọ 25.000h • Đổi mới 24T',
        'series_desc'  => [
            'Neo Slim 2G'       => 'Thân siêu mỏng 8mm cho trần sát',
            'DN Series'         => 'Thân nhôm tản nhiệt nguyên khối',
            'Color Rim'         => 'Viền kim loại đổi 3 màu linh hoạt',
            'Đèn Bàn Chống Cận' => 'Chuẩn RG0 không ánh sáng xanh hại mắt',
            'LED Dây & Nguồn'   => 'Dải sáng liên tục hắt trần thạch cao',
            'Đèn Gắn Tường'     => 'Chống nước IP65 chịu mọi thời tiết',
        ]
    ],
    'quat-tran-quat-hut' => [
        'hero_img'     => 'uploads/products/quat-tran-panasonic-5-canh-f-60gfn.png',
        'badge'        => 'ĐỘNG CƠ DC TIẾT KIỆM 50% ĐIỆN • HỆ THỐNG AN TOÀN 3 CẤP ĐỘC QUYỀN',
        'heading'      => 'Quạt Trần & Quạt Hút Thông Gió Panasonic',
        'sub_headline' => 'Vận hành êm ái vượt trội với động cơ bạc đạn kín bôi trơn vĩnh cửu. Dòng quạt trần 5 cánh DC cao cấp tích hợp chế độ tạo gió tự nhiên 1/f Yuragi, khóa cánh an toàn và công tắc tự ngắt phòng tránh mọi rủi ro.',
        'tag'          => 'Bạc đạn kín • Êm ái tuyệt đối',
        'series_desc'  => [
            'Quạt Trần 5 Cánh'  => 'Động cơ DC 9 cấp gió thông minh',
            'Quạt Chắn Gió'     => 'Ngăn bụi & chống thất thoát điều hòa',
            'Quạt Hút Thông Gió'=> 'Hút ẩm & khử mùi siêu tốc nhà tắm',
        ]
    ],
    'o-cam-am-san-cong-nghiep' => [
        'hero_img'     => 'uploads/products/f423-6-o-cam-am-loai-khong-kin-nuoc-dang-nghieng-32a-4p-400v-6h-ip44.jpg',
        'badge'        => 'VẬT LIỆU POLYAMIDE 6 SIÊU BỀN • TIÊU CHUẨN IP44 / IP67',
        'heading'      => 'Ổ Cắm Âm Sàn & Phích Cắm Công Nghiệp PCE',
        'sub_headline' => 'Giải pháp cấp nguồn chuyên nghiệp cho nhà xưởng, trung tâm thương mại và công trình cao cấp. Ổ cắm âm sàn Panasonic bằng hợp kim nhôm đúc sang trọng kết hợp phích cắm công nghiệp PCE chịu va đập và chống nước tuyệt đối.',
        'tag'          => 'Chống nước IP44/IP67 • Tải 16A-125A',
        'series_desc'  => [
            'Phích Cắm Di Động' => 'Polyamide 6 cách điện tuyệt đối',
            'Ổ Cắm Nổi PCE'     => 'Lắp tường công nghiệp tải cao',
            'Ổ Cắm Nối PCE'     => 'Đầu nối nguồn di động tại công trường',
            'Ổ Cắm Âm PCE'      => 'Lắp mặt tủ điện phân phối nhà xưởng',
        ]
    ],
    'may-bom-panasonic' => [
        'hero_img'     => 'uploads/products/may-bom-nuoc-day-cao-panasonic-gp-201jxk-200w.jpg',
        'badge'        => '100% DÂY ĐỒNG NGUYÊN CHẤT • BUỒNG BƠM ĐỒNG THAU CHỐNG RỈ SÉT',
        'heading'      => 'Máy Bơm Nước Panasonic Chính Hãng',
        'sub_headline' => 'Dòng máy bơm nước đẩy cao và tăng áp tự động Panasonic vận hành mạnh mẽ, êm ái với rơ-le nhiệt tự ngắt khi quá nhiệt, đảm bảo nguồn nước sinh hoạt liên tục và bền bỉ suốt hơn 10 năm sử dụng.',
        'tag'          => 'Bảo hành 24T • Đầy đủ phụ tùng',
    ],
    'may-nuoc-nong-panasonic' => [
        'hero_img'     => 'uploads/banners/banner-may-nuoc-nong.png',
        'badge'        => 'BÌNH CHỨA THÉP KHÔNG GỈ TRÁNG MEN • CẦU DAO CHỐNG GIẬT ELB TÍCH HỢP',
        'heading'      => 'Máy Nước Nóng & Bình Nóng Lạnh Panasonic',
        'sub_headline' => 'Máy nước nóng Panasonic thế hệ mới với công nghệ giữ nhiệt xốp cách nhiệt PU đúc khuôn, vỏ máy chống tia nước bắn IP25 và cầu dao chống rò điện ELB lưỡng cực bảo vệ tối đa cho cả gia đình.',
        'tag'          => 'Bình chứa bảo hành 7 năm',
    ],
    'may-loc-khong-khi' => [
        'hero_img'     => 'uploads/banners/banner-4-showroom-homelighting.jpg',
        'badge'        => 'CÔNG NGHỆ NANOE™ X ĐỘC QUYỀN • KHỬ MÙI & ỨC CHẾ 99.9% VI KHUẨN',
        'heading'      => 'Máy Lọc Không Khí & Hút Ẩm Panasonic',
        'sub_headline' => 'Giải pháp không khí sạch tiêu chuẩn Nhật Bản với màng lọc HEPA cao cấp loại bỏ 99.97% bụi mịn PM2.5 cùng công nghệ phân tử nước Nanoe™ X khử sạch mùi hôi và kháng khuẩn tối ưu.',
        'tag'          => 'Lọc bụi PM2.5 • Chuẩn Nhật Bản',
    ],
    'day-cap-dien-cadivi' => [
        'hero_img'     => 'uploads/products/cadivi/cadivi-vcm_do.jpg',
        'badge'        => 'TIÊU CHUẨN TCVN 6610 & IEC 60227 • ĐỒNG TINH CHẤT 99.99%',
        'heading'      => 'Dây & Cáp Điện CADIVI Chính Hãng',
        'sub_headline' => 'Tổng kho phân phối dây và cáp điện CADIVI chính hãng cho công trình dân dụng và dự án cơ điện (M&E). Cam kết 100% ruột đồng nguyên chất 99.99%, cách điện an toàn chống cháy lan, đầy đủ CO/CQ xuất xưởng và giá sỉ chiết khấu cao nhất.',
        'tag'          => 'Đồng 99.99% • Đủ CO/CQ Xuất Xưởng',
        'series_desc'  => [
            'Dây Đơn Mềm VCm'        => 'Dây đơn ruột đồng mềm dẻo 1.0 - 10 mm² âm tường',
            'Dây Đôi Mềm VCmd & VCV' => 'Dây đôi dẹp & đôi tròn mềm cấp nguồn gia dụng',
            'Cáp Điện Lực CV'        => 'Cáp 1 lõi 0.6/1kV cấp nguồn tủ điện trục chính',
            'Cáp Ngầm Hạ Thế CXV'    => 'Cáp ngầm 1-4 lõi cách điện XLPE chịu tải cao',
        ]
    ],
];

$meta = $catMetaLookup[$catSlug] ?? [
    'hero_img'     => 'uploads/banners/banner-thiet-bi-dien-panasonic.jpg',
    'badge'        => 'TIÊU CHUẨN CÔNG NGHỆ NHẬT BẢN • PHÂN PHỐI CHÍNH HÃNG',
    'heading'      => $category['name'] . ' Panasonic Chính Hãng',
    'sub_headline' => $category['description'] ?: 'Thiết bị điện Panasonic chính hãng - dẫn đầu về an toàn, độ bền và thiết kế tinh tế theo tiêu chuẩn công nghệ Nhật Bản. Bảo hành chính hãng 24 tháng, chiết khấu dự án tốt nhất.',
    'tag'          => 'Phân phối chính hãng 100%',
    'series_desc'  => [],
];
?>
<div class="category-page">
    <!-- Breadcrumb điều hướng -->
    <div class="page-header page-header--cat">
        <div class="container">
            <nav class="breadcrumb" aria-label="Đường dẫn trang">
                <a href="<?= url('/') ?>"><?= icon('home') ?> Trang chủ</a>
                <span class="breadcrumb__sep"><?= icon('chevron-right') ?></span>
                <a href="<?= url('/san-pham') ?>">Sản phẩm</a>
                <span class="breadcrumb__sep"><?= icon('chevron-right') ?></span>
                <span class="breadcrumb__current"><?= e($category['name']) ?></span>
            </nav>
        </div>
    </div>

    <div class="container section-cat">
        <!-- ==================== MASTER CORPORATE HERO SHOWCASE BANNER (COMPACT) ==================== -->
        <section class="cat-corp-hero">
            <div class="cat-corp-hero__inner">
                <!-- Cột thông tin & Cam kết phân phối gọn gàng -->
                <div class="cat-corp-hero__content">
                    <div class="cat-corp-hero__topline">
                        <span class="cat-corp-hero__badge">
                            <span class="corp-verified-icon">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M20 6L9 17l-5-5"/>
                                </svg>
                            </span>
                            <span><?= e($meta['badge']) ?></span>
                        </span>
                    </div>

                    <h1 class="cat-corp-hero__title"><?= e($meta['heading']) ?></h1>

                    <p class="cat-corp-hero__desc">
                        <?= e($meta['sub_headline']) ?>
                    </p>

                    <!-- 4 Cam Kết Doanh Nghiệp Gọn Gàng (Compact Grid) -->
                    <div class="cat-corp-trust-grid">
                        <div class="cat-trust-item">
                            <div class="cat-trust-icon cat-trust-icon--shield">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                                </svg>
                            </div>
                            <div class="cat-trust-text">
                                <strong>Bảo hành chính hãng 24T</strong>
                                <small>1 đổi 1 nhanh chóng</small>
                            </div>
                        </div>

                        <div class="cat-trust-item">
                            <div class="cat-trust-icon cat-trust-icon--tag">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/>
                                    <line x1="7" y1="7" x2="7.01" y2="7"/>
                                </svg>
                            </div>
                            <div class="cat-trust-text">
                                <strong>Chiết khấu dự án 35% - 42%</strong>
                                <small>Thầu thợ & đại lý</small>
                            </div>
                        </div>

                        <div class="cat-trust-item">
                            <div class="cat-trust-icon cat-trust-icon--cert">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                    <polyline points="14 2 14 8 20 8"/>
                                    <line x1="16" y1="13" x2="8" y2="13"/>
                                </svg>
                            </div>
                            <div class="cat-trust-text">
                                <strong>Đầy đủ CO/CQ & VAT</strong>
                                <small>Nghiệm thu chuẩn</small>
                            </div>
                        </div>

                        <div class="cat-trust-item">
                            <div class="cat-trust-icon cat-trust-icon--truck">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="1" y="3" width="15" height="13"/>
                                    <polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/>
                                    <circle cx="5.5" cy="18.5" r="2.5"/>
                                    <circle cx="18.5" cy="18.5" r="2.5"/>
                                </svg>
                            </div>
                            <div class="cat-trust-text">
                                <strong>Sẵn kho giao hỏa tốc 2h</strong>
                                <small>Kho HN & TP.HCM</small>
                            </div>
                        </div>
                    </div>

                    <!-- 3 Nút Tính Năng Thao Tác Nằm Gọn Trên 1 Hàng -->
                    <div class="cat-corp-hero__actions">
                        <a href="<?= url('/tai-catalogue') ?>" class="btn-corp-act btn-corp-act--primary" title="Tải bảng giá niêm yết Panasonic">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                                <polyline points="7 10 12 15 17 10"/>
                                <line x1="12" y1="15" x2="12" y2="3"/>
                            </svg>
                            <span>Tải Bảng Giá PDF</span>
                        </a>

                        <a href="<?= url('/bao-gia-cong-trinh') ?>" class="btn-corp-act btn-corp-act--secondary" title="Gửi danh mục nhận báo giá chiết khấu">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                <polyline points="14 2 14 8 20 8"/>
                                <line x1="12" y1="18" x2="12" y2="12"/>
                                <line x1="9" y1="15" x2="15" y2="15"/>
                            </svg>
                            <span>Báo Giá Dự Án</span>
                        </a>

                        <a href="tel:<?= e(preg_replace('/\D/', '', config('hotline'))) ?>" class="btn-corp-act btn-corp-act--phone" title="Gọi kỹ sư tư vấn kỹ thuật">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                                <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>
                            </svg>
                            <span>Kỹ sư: <?= e(config('hotline')) ?></span>
                        </a>
                    </div>
                </div>

                <!-- Cột hình ảnh sản phẩm chụp thực tế gọn gàng -->
                <div class="cat-corp-hero__media">
                    <div class="cat-showcase-frame">
                        <div class="cat-showcase-tag">
                            <span class="cat-tag-dot"></span>
                            <span><?= e($meta['tag']) ?></span>
                        </div>
                        <?php 
                        $isCadiviCat = ($catSlug === 'day-cap-dien-cadivi');
                        $brandCatName = $isCadiviCat ? 'CADIVI' : 'Panasonic';
                        $guaranteeNote = $isCadiviCat ? '100% CADIVI chính hãng nguyên cuộn tem niêm phong' : '100% Panasonic chính hãng nguyên hộp';
                        ?>
                        <img src="<?= media($meta['hero_img']) ?>" alt="<?= e($category['name']) ?> <?= $brandCatName ?> Chính Hãng" class="cat-showcase-img" loading="eager">
                        <div class="cat-showcase-footer">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#22c55e" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                            <span><?= $guaranteeNote ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ==================== THANH ĐIỀU HƯỚNG NHANH CÁC DÒNG SẢN PHẨM ==================== -->
        <?php if (!empty($sections)): ?>
        <nav class="cat-quick-nav" aria-label="Chọn nhanh dòng sản phẩm">
            <div class="cat-quick-nav__header">
                <div class="cat-quick-nav__title">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#004098" stroke-width="2.5">
                        <rect x="3" y="3" width="7" height="7"/>
                        <rect x="14" y="3" width="7" height="7"/>
                        <rect x="14" y="14" width="7" height="7"/>
                        <rect x="3" y="14" width="7" height="7"/>
                    </svg>
                    <strong>CHỌN NHANH DÒNG THIẾT BỊ:</strong>
                </div>
                <span class="cat-quick-nav__hint hide-sm">Nhấp vào dòng sản phẩm để cuộn nhanh đến danh sách</span>
            </div>

            <div class="cat-quick-nav__grid">
                <?php foreach ($sections as $sec): 
                    $subDesc = $meta['series_desc'][$sec['label']] ?? ($sec['bullets'][0] ?? 'Dòng thiết bị cao cấp');
                ?>
                    <a href="#<?= e($sec['id']) ?>" class="cat-quick-chip">
                        <div class="cat-quick-chip__thumb">
                            <img src="<?= media($sec['icon_img']) ?>" alt="<?= e($sec['label']) ?>" loading="lazy">
                        </div>
                        <div class="cat-quick-chip__meta">
                            <span class="cat-quick-chip__name"><?= e($sec['label']) ?></span>
                            <small class="cat-quick-chip__sub"><?= e($subDesc) ?></small>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        </nav>

        <!-- ==================== CÁC KHỐI SẢN PHẨM THEO DÒNG / CHUYÊN MỤC ==================== -->
        <div class="cat-sections-container">
            <?php foreach ($sections as $sec): ?>
                <?php if (!empty($sec['products'])): ?>
                <section class="cat-section-card" id="<?= e($sec['id']) ?>">
                    <!-- Header dòng sản phẩm chuẩn Doanh nghiệp -->
                    <div class="cat-section-card__head">
                        <div class="cat-head-left">
                            <span class="cat-head-bar"></span>
                            <div class="cat-head-title-wrap">
                                <h2 class="cat-head-title"><?= e($sec['title']) ?></h2>
                                <span class="cat-head-badge">Tiêu Chuẩn Chính Hãng</span>
                            </div>
                        </div>
                        <a href="<?= e($sec['more_url']) ?>" class="cat-head-more-btn">
                            <span>Xem tất cả sản phẩm dòng <?= e($sec['label']) ?></span>
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                        </a>
                    </div>

                    <!-- Điểm nổi bật kỹ thuật của dòng sản phẩm -->
                    <?php if (!empty($sec['bullets'])): ?>
                        <div class="cat-section-card__specs">
                            <?php foreach ($sec['bullets'] as $b): ?>
                                <div class="cat-spec-pill">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#004098" stroke-width="2.5" class="cat-spec-check">
                                        <polyline points="20 6 9 17 4 12"/>
                                    </svg>
                                    <span><?= e($b) ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <!-- Lưới hiển thị 6 sản phẩm đại diện -->
                    <div class="cat-section-card__grid">
                        <?php foreach ($sec['products'] as $p): ?>
                            <?php include __DIR__ . '/../partials/product_card.php'; ?>
                        <?php endforeach; ?>
                    </div>
                </section>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>

        <!-- ==================== BANNER HỖ TRỢ KỸ THUẬT & DỰ TOÁN CÔNG TRÌNH ==================== -->
        <section class="cat-support-banner">
            <div class="cat-support-banner__inner">
                <div class="cat-support-content">
                    <span class="cat-support-tag">DÀNH CHO CHỦ ĐẦU TƯ, NHÀ THẦU & KỸ SƯ CƠ ĐIỆN (M&E)</span>
                    <h3 class="cat-support-title">Cần Bóc Tách Bản Vẽ & Báo Giá <?= $isCadiviCat ? 'Dây Cáp Điện CADIVI & Thiết Bị Điện' : 'Thiết Bị Điện Panasonic' ?> Cho Dự Án?</h3>
                    <p class="cat-support-desc">
                        Đội ngũ kỹ sư của chúng tôi sẵn sàng hỗ trợ bóc tách khối lượng từ bản vẽ CAD/PDF, tư vấn tối ưu chủng loại và gửi báo giá chiết khấu đại lý tốt nhất trong vòng 30 phút.
                    </p>
                </div>
                <div class="cat-support-actions">
                    <a href="<?= url('/bao-gia-cong-trinh') ?>" class="btn-support-quote">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="12" y1="18" x2="12" y2="12"/><line x1="9" y1="15" x2="15" y2="15"/></svg>
                        <span>Gửi Bản Vẽ Nhận Báo Giá</span>
                    </a>
                    <a href="tel:<?= e(preg_replace('/\D/', '', config('hotline'))) ?>" class="btn-support-call">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                        <span>Hotline Kỹ Thuật: <?= e(config('hotline')) ?></span>
                    </a>
                </div>
            </div>
        </section>
        <?php endif; ?>
    </div>
</div>
