<?php
/** @var array $master */
/** @var array $series */
/** @var array $catalogs */
?>

<div class="page-header">
    <div class="container">
        <nav class="breadcrumb" aria-label="Đường dẫn trang">
            <a href="<?= url('/') ?>"><?= icon('home') ?> Trang chủ</a>
            <span class="breadcrumb__sep"><?= icon('chevron-right') ?></span>
            <span class="breadcrumb__current">Tải Catalogue 2026</span>
        </nav>
        <div class="page-header__content">
            <h1 class="page-title">Tải Catalogue & Bảng Giá Thiết Bị Điện Panasonic 2026</h1>
            <p class="page-desc">Tập hợp tài liệu thông số kỹ thuật, catalogue sản phẩm và bảng giá niêm yết chính thức áp dụng cho các dòng thiết bị điện Panasonic.</p>
        </div>
    </div>
</div>

<div class="container section">
    <!-- Catalogue Master 2026 Banner -->
    <div class="catalog-download-hero">
        <div class="catalog-download-hero__content">
            <span class="badge badge--warning"><?= icon('award') ?> Phiên bản mới nhất</span>
            <h2>Catalogue Tổng Thiết Bị Điện Panasonic 2026 (PDF)</h2>
            <p>Bao gồm đầy đủ các nhóm thiết bị: Công tắc ổ cắm tất cả các series, thiết bị đóng cắt an toàn MCB/RCBO, đèn chiếu sáng dân dụng và công nghiệp, quạt trần, quạt hút thông gió.</p>
            <div class="catalog-download-hero__actions">
                <a href="<?= asset($master['url']) ?>" class="btn btn--primary btn--xl" download target="_blank">
                    <?= icon('download') ?> Tải Catalogue Đầy Đủ (PDF)
                </a>
                <a href="<?= url('/bao-gia-cong-trinh') ?>" class="btn btn--outline btn--xl">
                    <?= icon('file-text') ?> Yêu cầu bảng giá Excel chiết khấu
                </a>
            </div>
        </div>
        <div class="catalog-download-hero__cover">
            <div class="pdf-book-mockup">
                <div class="pdf-book-badge">2026</div>
                <div class="pdf-book-brand">Panasonic</div>
                <div class="pdf-book-title">Electric Works<br>Catalogue & Price List</div>
            </div>
        </div>
    </div>

    <!-- Danh sách Catalogue theo từng Chủng Loại / Series -->
    <div class="section__head mt-5">
        <div>
            <span class="section__eyebrow">Tài liệu phân nhánh</span>
            <h2 class="section__title">Catalogue chuyên đề & Datasheet kỹ thuật</h2>
        </div>
    </div>

    <div class="catalog-cards-grid">
        <!-- Series Switch & Socket -->
        <?php foreach ($series as $s): ?>
            <div class="catalog-item-card">
                <div class="catalog-item-card__icon" style="background: <?= e($s['color']) ?>20; color: <?= e($s['color']) ?>">
                    <?= icon('switch') ?>
                </div>
                <div class="catalog-item-card__body">
                    <h3>Catalogue Panasonic <?= e($s['name']) ?></h3>
                    <p><?= e($s['tagline'] ?? 'Dòng thiết bị cao cấp chính hãng') ?></p>
                    <div class="catalog-item-card__footer">
                        <span class="file-type"><span class="badge badge--sm">PDF</span> 2.4 MB</span>
                        <a href="<?= asset($s['catalog_url'] ?? 'uploads/catalogs/panasonic-wide-series.pdf') ?>" class="link-download" download target="_blank">
                            <?= icon('download') ?> Tải về
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>

        <!-- Catalogue Đóng cắt MCB -->
        <div class="catalog-item-card">
            <div class="catalog-item-card__icon" style="background: #e0f2fe; color: #0284c7">
                <?= icon('breaker') ?>
            </div>
            <div class="catalog-item-card__body">
                <h3>Thiết bị đóng cắt an toàn MCB / RCBO</h3>
                <p>Thông số dòng cắt kA, đường cong đặc tính B/C/D, bảng phối hợp bảo vệ chọn lọc.</p>
                <div class="catalog-item-card__footer">
                    <span class="file-type"><span class="badge badge--sm">PDF</span> 4.8 MB</span>
                    <a href="<?= asset('uploads/catalogs/panasonic-mcb-rcbo.pdf') ?>" class="link-download" download target="_blank">
                        <?= icon('download') ?> Tải về
                    </a>
                </div>
            </div>
        </div>

        <!-- Catalogue Quạt thông gió -->
        <div class="catalog-item-card">
            <div class="catalog-item-card__icon" style="background: #ecfdf5; color: #059669">
                <?= icon('fan') ?>
            </div>
            <div class="catalog-item-card__body">
                <h3>Quạt trần & Quạt hút thông gió IAQ</h3>
                <p>Giải pháp thông khí chất lượng cao trong nhà, biểu đồ lưu lượng và áp suất cột áp tĩnh.</p>
                <div class="catalog-item-card__footer">
                    <span class="file-type"><span class="badge badge--sm">PDF</span> 3.6 MB</span>
                    <a href="<?= asset('uploads/catalogs/panasonic-quat-tran.pdf') ?>" class="link-download" download target="_blank">
                        <?= icon('download') ?> Tải về
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
