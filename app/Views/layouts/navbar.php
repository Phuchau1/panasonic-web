<?php
use App\Models\Category;
use App\Models\ProductSeries;

// Dữ liệu dùng cho mobile drawer
$navCategories = $navCategories ?? (new Category())->active();
$navSeries     = $navSeries ?? (new ProductSeries())->active();
?>

<!-- ==================== MOBILE DRAWER NAVIGATION ==================== -->
<div class="drawer-backdrop" data-drawer-close></div>
<aside class="drawer" id="mobileDrawer" aria-label="Menu di động" aria-hidden="true" tabindex="-1">
    <div class="drawer__head">
        <a href="<?= url('/') ?>" class="logo logo--light">
            <span class="logo__mark">Panasonic</span>
            <span class="logo__sub">Thiết Bị Điện Chính Hãng</span>
        </a>
        <button class="drawer__close" type="button" aria-label="Đóng menu" data-drawer-close><?= icon('x') ?></button>
    </div>

    <div class="drawer__body">
        <a href="<?= url('/tai-catalogue') ?>" class="btn btn--catalog btn--block"><?= icon('download') ?><span><small>Tải ngay</small>Catalogue 2026 PDF</span></a>

        <p class="drawer__label">Danh mục sản phẩm</p>
        <ul class="drawer__list">
            <li><a href="<?= url('/') ?>"><?= icon('home') ?> Trang chủ</a></li>
            <li><a href="<?= url('/san-pham') ?>"><?= icon('grid') ?> Tất cả sản phẩm</a></li>
            <?php foreach ($navCategories as $c): ?>
                <li><a href="<?= url('/danh-muc/' . $c['slug']) ?>"><?= icon($c['icon']) ?> <?= e($c['name']) ?> <span class="drawer__count"><?= (int) $c['product_count'] ?></span></a></li>
            <?php endforeach; ?>
        </ul>

        <p class="drawer__label">Dòng Series Nổi Bật</p>
        <div class="drawer__series">
            <?php foreach ($navSeries as $s): ?>
                <a class="series-chip" href="<?= url('/panasonic-' . $s['slug']) ?>"><span class="series-chip__dot" style="--dot: <?= e($s['color']) ?>"></span><?= e($s['name']) ?></a>
            <?php endforeach; ?>
        </div>

        <p class="drawer__label">Dự Án & Liên Hệ</p>
        <ul class="drawer__list">
            <li><a href="<?= url('/du-an') ?>"><?= icon('building') ?> Dự Án Tiêu Biểu (TP.HCM & Long An)</a></li>
            <li><a href="<?= url('/bao-gia-cong-trinh') ?>"><?= icon('upload') ?> Gửi Báo Giá & Bóc Tách Vật Tư</a></li>
            <li><a href="<?= url('/lien-he') ?>"><?= icon('map-pin') ?> Liên Hệ & Bản Đồ Kho Hàng</a></li>
            <li><a href="<?= url('/gio-hang') ?>"><?= icon('cart') ?> Giỏ hàng (<span data-cart-count><?= cart_count() ?></span>)</a></li>
        </ul>
    </div>

    <div class="drawer__foot">
        <a href="tel:<?= e(preg_replace('/\D/', '', config('hotline'))) ?>" class="btn btn--primary btn--block"><?= icon('phone') ?> Gọi <?= e(config('hotline')) ?></a>
        <a href="https://zalo.me/<?= e(config('zalo')) ?>" target="_blank" rel="noopener" class="btn btn--outline btn--block"><?= icon('chat') ?> Chat Zalo nhận báo giá</a>
    </div>
</aside>

<!-- ==================== FLASH NOTIFICATIONS ==================== -->
<?php if ($msg = flash('success')): ?>
    <div class="flash flash--success" role="status" data-autohide><?= icon('check-circle') ?> <span><?= e($msg) ?></span><button type="button" aria-label="Đóng" data-dismiss><?= icon('x') ?></button></div>
<?php endif; ?>
<?php if ($msg = flash('error')): ?>
    <div class="flash flash--error" role="alert" data-autohide><?= icon('alert') ?> <span><?= e($msg) ?></span><button type="button" aria-label="Đóng" data-dismiss><?= icon('x') ?></button></div>
<?php endif; ?>
