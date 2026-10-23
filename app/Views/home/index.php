<?php
/** @var array $categories */
/** @var array $series */
/** @var array $sections */
/** @var array $featured */
/** @var array $breakers */

// Danh sách danh mục icon trực quan ở đầu trang (Chuẩn 12 ô xếp đều 2 hàng x 6 cột)
$topQuickCats = [
    [
        'title' => 'Đèn Downlight',
        'image' => 'uploads/products/den-downlight-panasonic-12w-color-rim-doi-mau-3-mau-vien-vang.jpg',
        'link'  => url('/danh-muc/den-led-chieu-sang?q=downlight'),
    ],
    [
        'title' => 'Đèn Ốp Trần',
        'image' => 'uploads/products/den-led-panel-600x600-40w-panasonic.jpg',
        'link'  => url('/danh-muc/den-led-chieu-sang?q=tran'),
    ],
    [
        'title' => 'Đèn Bàn Học',
        'image' => 'uploads/products/den-ban-hoc-panasonic-20w-hhglt063319-cao-cap.jpg',
        'link'  => url('/danh-muc/den-led-chieu-sang?q=ban'),
    ],
    [
        'title' => 'Bóng Filament',
        'image' => 'uploads/products/bong-st-den-filament-e27-7w-panasonic.jpg',
        'link'  => url('/danh-muc/den-led-chieu-sang?q=filament'),
    ],
    [
        'title' => 'Đèn Gắn Tường',
        'image' => 'uploads/products/den-gan-tuong-ngoai-troi-6w-nanoco-nbls031-nbls031w.jpg',
        'link'  => url('/danh-muc/den-led-chieu-sang?q=tuong'),
    ],
    [
        'title' => 'Công Tắc Moderva',
        'image' => 'uploads/products/bo-1-cong-tac-b-1-chieu-co-chi-bao-da-quang-moderva-vang-anh-kim-wmf511myz-1vn.png',
        'link'  => url('/panasonic-moderva'),
    ],
    [
        'title' => 'Công Tắc Minerva',
        'image' => 'uploads/products/bo-cong-tac-vuong-panasonic-dong-minerva-mau-trang.jpg',
        'link'  => url('/panasonic-minerva'),
    ],
    [
        'title' => 'Aptomat MCB 6kA',
        'image' => 'uploads/products/aptomat-chong-giat-rcbo-2p-25a-32a-6ka-panasonic.jpg',
        'link'  => url('/danh-muc/thiet-bi-dong-cat'),
    ],
    [
        'title' => 'Cầu Dao Khối MCCB',
        'image' => 'uploads/products/mccb-panasonic-4p-800a-50ka-bbsf47800cmcv.jpg',
        'link'  => url('/danh-muc/thiet-bi-dong-cat?q=mccb'),
    ],
    [
        'title' => 'Quạt Trần Panasonic',
        'image' => 'uploads/products/quat-tran-panasonic-5-canh-f-60gds-be-mau-be.png',
        'link'  => url('/danh-muc/quat-tran-quat-hut'),
    ],
    [
        'title' => 'Ổ Cắm PCE IP67',
        'image' => 'uploads/products/f423-6-o-cam-am-loai-khong-kin-nuoc-dang-nghieng-32a-4p-400v-6h-ip44.jpg',
        'link'  => url('/danh-muc/o-cam-am-san-cong-nghiep'),
    ],
    [
        'title' => 'Dây Điện CADIVI',
        'image' => 'uploads/products/cadivi/cadivi-vcm_do.jpg',
        'link'  => url('/danh-muc/day-cap-dien-cadivi'),
    ],
];

// Dữ liệu MCB cho bảng tra cứu JS
$breakerJson = array_map(fn($b) => [
    'name'  => $b['name'],
    'model' => $b['model_code'],
    'amp'   => (float) $b['rated_current'],
    'ka'    => (float) $b['breaking_capacity'],
    'poles' => (int) $b['poles'],
    'price' => money($b['price']),
    'url'   => url('/san-pham/' . $b['slug']),
], $breakers);
?>

<div class="home-page-container">
    <!-- ==================== BANNER ĐỘNG CHÍNH HÃNG PANASONIC: CHẤT LƯỢNG CHUẨN NHẬT ==================== -->
    <div class="container hero-banner-section">
        <div class="hero-japan-wrapper">
            <div class="hero-banner-slider hero-japan-slider" data-hero-slider data-interval="5000">
                <div class="hero-banner-slides hero-japan-slides">
                    <!-- Slide 1: Bố cục Chuẩn Nhật Flagship (Ảnh nét chuẩn theo ảnh mẫu, KHÔNG lỗi mờ) -->
                    <div class="hero-banner-slide hero-japan-slide is-active">
                        <img src="<?= asset('uploads/banner-hero-japan-main.png') ?>" 
                             alt="Thiết bị điện xây dựng Panasonic - Chất lượng chuẩn Nhật - An toàn, bền bỉ, thẩm mỹ cho mọi không gian" 
                             class="hero-japan-base-img" 
                             width="1024" height="310">

                        <!-- Các vùng liên kết trong suốt 100%, không bị vệt đè -->
                        <a href="<?= url('/san-pham') ?>" class="hj-hotspot-link hj-hotspot-cta" title="Khám phá ngay sản phẩm Panasonic"></a>
                        <a href="<?= url('/danh-muc/cong-tac-o-cam') ?>" class="hj-hotspot-link hj-hotspot-home" title="Thiết bị điện Nhà Ở (Công tắc ổ cắm)"></a>
                        <a href="<?= url('/danh-muc/den-led-chieu-sang') ?>" class="hj-hotspot-link hj-hotspot-office" title="Chiếu sáng Văn Phòng & Tòa Nhà"></a>
                        <a href="<?= url('/danh-muc/thiet-bi-dong-cat') ?>" class="hj-hotspot-link hj-hotspot-project" title="Thiết bị điện Công Trình & Dự Án"></a>
                    </div>

                    <!-- Slide 2: Ảnh Không Gian Penthouse Hiện Đại Siêu Thực (8K Hasselblad) -->
                    <a href="<?= url('/panasonic-moderva') ?>" class="hero-banner-slide hero-japan-slide">
                        <img src="<?= asset('uploads/banners/hero-penthouse-luxury.jpg') ?>" 
                             alt="Thiết bị điện cao cấp Panasonic - Giải pháp nhà thông minh và thẩm mỹ đỉnh cao" 
                             class="hero-japan-base-img" 
                             loading="lazy" width="1920" height="1080">
                    </a>

                    <!-- Slide 3: Thiết Bị Điện & Công Tắc Ổ Cắm Moderva, Gen-X -->
                    <a href="<?= url('/danh-muc/cong-tac-o-cam') ?>" class="hero-banner-slide hero-japan-slide">
                        <img src="<?= asset('uploads/banners/banner-1-thiet-bi-dien.jpg') ?>" 
                             alt="Thiết bị điện xây dựng Panasonic - Chất lượng chuẩn Nhật - Moderva & Gen-X" 
                             class="hero-japan-base-img" loading="lazy" width="1536" height="602">
                    </a>

                    <!-- Slide 4: Cầu Dao Tự Động MCB, RCBO Chuẩn IEC/JIS -->
                    <a href="<?= url('/danh-muc/thiet-bi-dong-cat') ?>" class="hero-banner-slide">
                        <img src="<?= asset('uploads/banners/banner-2-aptomat-mcb.jpg') ?>" 
                             alt="Cầu dao tự động MCB RCBO chống giật Panasonic - Chuẩn quốc tế IEC / JIS" 
                             class="hero-japan-base-img" loading="lazy" width="977" height="384">
                    </a>

                    <!-- Slide 5: Đèn LED Downlight Âm Trần Panasonic -->
                    <a href="<?= url('/danh-muc/den-led-chieu-sang') ?>" class="hero-banner-slide">
                        <img src="<?= asset('uploads/banners/banner-3-den-led-downlight.jpg') ?>" 
                             alt="Đèn LED Downlight Panasonic - Bật đèn khác biệt, bảo vệ thị lực" 
                             class="hero-japan-base-img" loading="lazy" width="1920" height="800">
                    </a>
                </div>

                <!-- Nút Next / Prev chuyển banner mượt mà -->
                <button type="button" class="hj-slider-btn hj-slider-btn--prev" data-slider-prev aria-label="Banner trước">
                    <?= icon('chevron-left') ?>
                </button>
                <button type="button" class="hj-slider-btn hj-slider-btn--next" data-slider-next aria-label="Banner tiếp theo">
                    <?= icon('chevron-right') ?>
                </button>

                <!-- Dots chuyển slide -->
                <div class="hj-slider-dots" data-slider-dots>
                    <button type="button" class="hero-banner-dot hj-slider-dot is-active" data-slide-index="0" data-slider-dot aria-label="Banner 1"></button>
                    <button type="button" class="hero-banner-dot hj-slider-dot" data-slide-index="1" data-slider-dot aria-label="Banner 2"></button>
                    <button type="button" class="hero-banner-dot hj-slider-dot" data-slide-index="2" data-slider-dot aria-label="Banner 3"></button>
                    <button type="button" class="hero-banner-dot hj-slider-dot" data-slide-index="3" data-slider-dot aria-label="Banner 4"></button>
                    <button type="button" class="hero-banner-dot hj-slider-dot" data-slide-index="4" data-slider-dot aria-label="Banner 5"></button>
                </div>
            </div>

            <!-- Thanh Cam Kết Doanh Nghiệp 4 Trụ Cột (Trust Bar chuẩn như ảnh) -->
            <div class="hero-japan-trust-bar">
                <div class="hj-trust-item">
                    <div class="hj-trust-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="m9 12 2 2 4-4"/></svg>
                    </div>
                    <div class="hj-trust-text">
                        <strong class="hj-trust-title">100% Chính Hãng Panasonic</strong>
                        <span class="hj-trust-desc">CO/CQ, VAT chính hãng, bảo hành 24T</span>
                    </div>
                </div>

                <div class="hj-trust-divider"></div>

                <div class="hj-trust-item">
                    <div class="hj-trust-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="3" width="15" height="13"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
                    </div>
                    <div class="hj-trust-text">
                        <strong class="hj-trust-title">Giao Nhanh 2h – 4h</strong>
                        <span class="hj-trust-desc">Tổng kho sẵn hàng số lượng lớn tại TP.HCM & Long An</span>
                    </div>
                </div>

                <div class="hj-trust-divider"></div>

                <div class="hj-trust-item">
                    <div class="hj-trust-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="19" y1="5" x2="5" y2="19"/><circle cx="6.5" cy="6.5" r="2.5"/><circle cx="17.5" cy="17.5" r="2.5"/></svg>
                    </div>
                    <div class="hj-trust-text">
                        <strong class="hj-trust-title">Chiết Khấu Dự Án 15% – 35%</strong>
                        <span class="hj-trust-desc">Áp dụng giá tốt nhất cho nhà thầu M&E & đại lý</span>
                    </div>
                </div>

                <div class="hj-trust-divider"></div>

                <a href="<?= url('/bao-gia-cong-trinh') ?>" class="hj-trust-item">
                    <div class="hj-trust-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                    </div>
                    <div class="hj-trust-text">
                        <strong class="hj-trust-title">Bóc Tách Khối Lượng Miễn Phí</strong>
                        <span class="hj-trust-desc">Kỹ sư hỗ trợ lên báo giá Excel bản vẽ trong 30 phút</span>
                    </div>
                </a>
            </div>
        </div>
    </div>

    <!-- ==================== KHỐI ICON PHÂN LOẠI DANH MỤC TRỰC QUAN ==================== -->
    <section class="container quick-categories-section">
        <div class="category-block-header">
            <h2 class="cat-block-title">
                <span>CÁC CHỦNG LOẠI THIẾT BỊ ĐIỆN PANASONIC</span>
            </h2>
            <a href="<?= url('/san-pham') ?>" class="cat-block-more">Xem toàn bộ danh mục »</a>
        </div>

        <div class="quick-cat-grid">
            <?php foreach ($topQuickCats as $qc): ?>
                <a href="<?= e($qc['link']) ?>" class="quick-cat-card">
                    <div class="quick-cat-img">
                        <img src="<?= media($qc['image']) ?>" alt="<?= e($qc['title']) ?>" loading="lazy" width="105" height="105">
                    </div>
                    <span class="quick-cat-title"><?= e($qc['title']) ?></span>
                </a>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- ==================== CÁC KHỐI SẢN PHẨM PHÂN TÁCH THEO DANH MỤC (NHƯ TRONG ẢNH) ==================== -->
    <?php foreach ($sections as $sec): ?>
        <?php if (!empty($sec['items'])): ?>
            <section class="container category-showcase-section" id="<?= e($sec['id']) ?>">
                <!-- Thanh tiêu đề dạng dải ruy-băng xanh vát góc đặc trưng -->
                <div class="category-block-header">
                    <h2 class="cat-block-title">
                        <a href="<?= e($sec['link']) ?>"><?= e($sec['title']) ?></a>
                    </h2>
                    <a href="<?= e($sec['link']) ?>" class="cat-block-more">Xem tất cả »</a>
                </div>

                <!-- Dòng giới thiệu ưu điểm / tính năng -->
                <?php if (!empty($sec['bullets'])): ?>
                    <ul class="cat-block-bullets">
                        <?php foreach ($sec['bullets'] as $bullet): ?>
                            <li><span class="bullet-star">★</span> <?= e($bullet) ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>

                <!-- Grid 5 cột sản phẩm chuẩn e-commerce thực tế -->
                <div class="product-grid-5">
                    <?php foreach ($sec['items'] as $p): ?>
                        <?php require VIEW_PATH . '/partials/product_card.php'; ?>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endif; ?>
    <?php endforeach; ?>

    <!-- ==================== TRA CỨU CẦU DAO MCB/RCBO THEO TẢI THỰC TẾ ==================== -->
    <section class="container mcb-lookup-section" id="tra-cuu-mcb">
        <div class="category-block-header">
            <h2 class="cat-block-title">
                <a href="<?= url('/#tra-cuu-mcb') ?>">CÔNG CỤ TRA CỨU MCB / RCBO THEO CÔNG SUẤT TẢI AMPE</a>
            </h2>
            <a href="<?= url('/danh-muc/thiet-bi-dong-cat') ?>" class="cat-block-more">Tất cả MCB Panasonic »</a>
        </div>

        <ul class="cat-block-bullets">
            <li><span class="bullet-star">★</span> Tự động tính dòng làm việc thực tế $I_b = \frac{P}{U \cdot \cos\varphi}$ và chọn dòng định mức an toàn $I_n \ge 1.25 \times I_b$.</li>
            <li><span class="bullet-star">★</span> Đề xuất ngay tiết diện dây đồng (Cu/PVC) và cầu dao tự động Panasonic tương thích có sẵn tại kho.</li>
        </ul>

        <div class="mcb-tool-wrapper">
            <form class="mcb-calc-form" data-mcb-calc data-breakers='<?= e(json_encode($breakerJson, JSON_UNESCAPED_UNICODE)) ?>' onsubmit="return false">
                <div class="calc-row">
                    <label class="calc-field">
                        <span>Tổng công suất thiết bị (W):</span>
                        <input type="number" class="calc-input" name="power" min="1" max="200000" step="50" value="3500">
                    </label>
                    <label class="calc-field">
                        <span>Điện áp nguồn cấp:</span>
                        <select class="calc-input" name="phase">
                            <option value="1">1 pha – 220V (Dân dụng gia đình)</option>
                            <option value="3">3 pha – 380V (Công nghiệp, xưởng)</option>
                        </select>
                    </label>
                    <label class="calc-field">
                        <span>Hệ số công suất cosφ:</span>
                        <select class="calc-input" name="cosphi">
                            <option value="0.95">0.95 – Đèn LED, điện trở thuần</option>
                            <option value="0.85" selected>0.85 – Điều hòa, tủ lạnh thông thường</option>
                            <option value="0.80">0.80 – Động cơ, máy bơm nước</option>
                        </select>
                    </label>
                </div>

                <div class="calc-results-row" data-mcb-results>
                    <div class="res-card">
                        <small>Dòng làm việc (Ib):</small>
                        <strong data-out-ib>18.7 A</strong>
                    </div>
                    <div class="res-card res-card--highlight">
                        <small>Đề xuất MCB chọn:</small>
                        <strong data-out-in>25 A (2P)</strong>
                    </div>
                    <div class="res-card">
                        <small>Tiết diện dây khuyến nghị:</small>
                        <strong data-out-wire>4.0 mm²</strong>
                    </div>
                </div>

                <div class="calc-product-match" data-mcb-matched>
                    <!-- Tự động điền sản phẩm tương thích qua JS -->
                </div>
            </form>
        </div>
    </section>


    <!-- ==================== GỬI BẢNG DỰ TOÁN BÓC TÁCH VẬT TƯ ==================== -->
    <section class="container bom-upload-section">
        <div class="bom-box">
            <div class="bom-info">
                <h3>BÁO GIÁ DỰ ÁN & BÓC TÁCH KHỐI LƯỢNG CÔNG TRÌNH</h3>
                <p>Gửi danh sách vật tư dạng file Excel (.xlsx), PDF hoặc bản vẽ kỹ thuật CAD (.dwg). Đội ngũ kỹ sư Panasonic sẽ gửi bảng báo giá kèm mức chiết khấu tốt nhất trong 2 giờ làm việc.</p>
                <div class="bom-phones">
                    <span>Hotline báo giá: <strong><?= e(config('hotline')) ?></strong></span>
                    <span>Email: <strong><?= e(config('email')) ?></strong></span>
                </div>
            </div>
            <div class="bom-action">
                <a href="<?= url('/bao-gia-cong-trinh') ?>" class="btn-bom">GỬI YÊU CẦU BÁO GIÁ DỰ ÁN »</a>
            </div>
        </div>
    </section>
</div>
