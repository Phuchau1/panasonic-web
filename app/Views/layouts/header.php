<?php
/** @var string $title */
/** @var string $metaDescription */

use App\Models\Category;
use App\Models\ProductSeries;

// Dữ liệu danh mục & series dùng chung
$navCategories = $navCategories ?? (new Category())->active();
$navSeries     = $navSeries ?? (new ProductSeries())->active();
?><!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title><?= e($title) ?></title>
    <meta name="description" content="<?= e($metaDescription) ?>">
    <meta name="theme-color" content="#004098">
    <meta name="csrf-token" content="<?= e(csrf_token()) ?>">
    <meta property="og:type" content="website">
    <meta property="og:title" content="<?= e($title) ?>">
    <meta property="og:description" content="<?= e($metaDescription) ?>">
    <meta property="og:site_name" content="<?= e(config('name')) ?>">
    <?php $canonicalPath = substr((string) parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH), strlen(base_path())) ?: '/'; ?>
    <link rel="canonical" href="<?= e(full_url($canonicalPath)) ?>">
    <link rel="icon" href="<?= asset('favicon.ico') ?>" sizes="any">
    <link rel="icon" href="<?= asset('uploads/favicon.svg') ?>" type="image/svg+xml">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preload" as="style" href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;500;600;700;800;900&display=swap">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;500;600;700;800;900&display=swap" media="print" onload="this.media='all'">
    <noscript>
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;500;600;700;800;900&display=swap">
    </noscript>
    <link rel="stylesheet" href="<?= asset('css/main.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/responsive.css') ?>">
    <script>
        window.APP = {
            baseUrl: <?= json_encode(base_path()) ?>,
            base: <?= json_encode(base_path()) ?>,
            csrf: <?= json_encode(csrf_token()) ?>,
            googleClientId: <?= json_encode(config('google_client_id', '')) ?>,
            facebookAppId: <?= json_encode(config('facebook_app_id', '')) ?>,
            routes: {
                search: <?= json_encode(url('/api/tim-kiem')) ?>,
                cartAdd: <?= json_encode(url('/gio-hang/them')) ?>,
                cartUpdate: <?= json_encode(url('/gio-hang/cap-nhat')) ?>,
                cartRemove: <?= json_encode(url('/gio-hang/xoa')) ?>,
                cart: <?= json_encode(url('/gio-hang')) ?>,
                products: <?= json_encode(url('/san-pham')) ?>
            }
        };
        window.BASE_URL = window.APP.baseUrl;
    </script>
</head>
<body>
<a class="skip-link" href="#main-content">Bỏ qua tới nội dung chính</a>

<!-- ==================== HEADER DOANH NGHIỆP PANASONIC (3-TIER PROFESSIONAL) ==================== -->
<header class="header-corp" id="siteHeader">
    <!-- TIER 1: THANH CAM KẾT DOANH NGHIỆP TRÊN CÙNG -->
    <div class="header-topbar">
        <div class="container header-topbar__inner">
            <div class="topbar-left">
                <span class="topbar-badge">CHÍNH HÃNG</span>
                <span class="topbar-text">Tổng kho thiết bị điện Panasonic tại TP.HCM & Long An • 100% CO/CQ, VAT đầy đủ</span>
            </div>
            <div class="topbar-right hide-sm">
                <a href="<?= url('/du-an') ?>" class="topbar-link"><?= icon('building') ?> Dự Án Thực Tế</a>
                <span class="topbar-dot">•</span>
                <a href="<?= url('/tai-catalogue') ?>" class="topbar-link"><?= icon('file-text') ?> Bảng Giá Catalogue 2026</a>
                <span class="topbar-dot">•</span>
                <a href="<?= url('/bao-gia-cong-trinh') ?>" class="topbar-link topbar-link--highlight"><?= icon('zap') ?> Báo Giá Dự Án (M&E)</a>
            </div>
        </div>
    </div>

    <!-- TIER 2: THANH CHÍNH (LOGO, Ô TÌM KIẾM TRUNG TÂM RỘNG RÃI, HOTLINE, GIỎ HÀNG, USER) -->
    <div class="header-mainbar">
        <div class="container header-mainbar__inner">
            <!-- Nút Burger cho Mobile -->
            <button class="header__burger hide-lg" type="button" aria-label="Mở menu" aria-controls="mobileDrawer" aria-expanded="false" data-drawer-open>
                <?= icon('menu') ?>
            </button>

            <!-- 1. Logo Panasonic Doanh Nghiệp -->
            <a href="<?= url('/') ?>" class="corp-logo" aria-label="<?= e(config('name')) ?> – Trang chủ">
                <span class="corp-logo__brand">Panasonic</span>
                <span class="corp-logo__sub">THIẾT BỊ ĐIỆN CHÍNH HÃNG</span>
            </a>

            <!-- 2. Ô TÌM KIẾM TRUNG TÂM SIÊU RỘNG RÃI (CHÍNH GIỮA) -->
            <div class="corp-search-wrapper">
                <form class="corp-search-form" action="<?= url('/san-pham') ?>" method="get" role="search" data-quicksearch autocomplete="off">
                    <label for="q" class="sr-only">Tìm sản phẩm</label>
                    <div class="corp-search-input-box">
                        <span class="corp-search-icon"><?= icon('search') ?></span>
                        <input id="q" name="q" type="search" class="corp-search-input search__input"
                               placeholder="Tìm theo tên sản phẩm, mã hàng (WEV, MCB, Đèn Downlight, F-60, Moderva...)..."
                               value="<?= e($_GET['q'] ?? '') ?>"
                               autocomplete="off">
                        <button type="submit" class="corp-search-submit-btn" aria-label="Tìm kiếm">
                            <?= icon('search') ?> <span>Tìm kiếm</span>
                        </button>
                    </div>

                    <!-- Bảng Dropdown Gợi Ý Sản Phẩm Trực Tiếp Ngay Dưới Ô Search -->
                    <div class="search__dropdown" id="searchResults" role="listbox" hidden></div>
                </form>
            </div>

            <!-- 3. Cụm Tiện Ích: Hotline Kỹ Sư, Báo Giá Nhanh, Giỏ Hàng, Tài Khoản -->
            <div class="corp-header-actions">
                <!-- Hotline Kỹ Sư Tư Vấn 24/7 -->
                <a href="tel:<?= e(preg_replace('/\D/', '', config('hotline'))) ?>" class="corp-action-pill hide-md" title="Gọi tư vấn kỹ thuật thiết bị điện">
                    <div class="corp-pill-icon corp-pill-icon--phone"><?= icon('phone') ?></div>
                    <div class="corp-pill-text">
                        <small>Kỹ sư tư vấn 24/7</small>
                        <strong><?= e(config('hotline')) ?></strong>
                    </div>
                </a>

                <!-- Giỏ hàng -->
                <a href="<?= url('/gio-hang') ?>" class="corp-action-cart" aria-label="Xem giỏ hàng">
                    <div class="corp-cart-icon-wrap">
                        <?= icon('cart') ?>
                        <span class="corp-cart-badge" data-cart-count><?= cart_count() ?></span>
                    </div>
                    <div class="corp-cart-text hide-sm">
                        <small>Đơn hàng</small>
                        <strong>Giỏ hàng</strong>
                    </div>
                </a>

                <!-- Tài khoản người dùng / Đăng nhập -->
                <?php if (is_logged_in()): $user = auth_user(); ?>
                    <div class="huser-menu-wrap" id="headerUserMenu">
                        <button type="button" class="huser-btn" id="userDropdownTrigger" aria-expanded="false" aria-haspopup="true">
                            <?php if (!empty($user['avatar'])): ?>
                                <img src="<?= e($user['avatar']) ?>" alt="<?= e($user['name']) ?>" class="huser-avatar-img">
                            <?php else: ?>
                                <span class="huser-avatar-badge"><?= mb_strtoupper(mb_substr($user['name'] ?? 'U', 0, 1)) ?></span>
                            <?php endif; ?>
                            <div class="corp-user-meta hide-sm">
                                <small>Tài khoản</small>
                                <span class="huser-name"><?= e($user['name']) ?></span>
                            </div>
                            <svg class="huser-arrow hide-sm" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M6 9l6 6 6-6"/></svg>
                        </button>
                        <div class="huser-dropdown-panel" id="userDropdownPanel" hidden>
                            <div class="huser-dd-profile">
                                <strong><?= e($user['name']) ?></strong>
                                <small><?= e($user['email'] ?? '') ?></small>
                            </div>
                            <ul class="huser-dd-nav">
                                <li><a href="<?= url('/tai-khoan') ?>"><?= icon('user') ?> Thông tin tài khoản</a></li>
                                <li><a href="<?= url('/tai-khoan#orders') ?>"><?= icon('cart') ?> Đơn hàng đã đặt</a></li>
                                <li class="huser-dd-divider"></li>
                                <li><button type="button" class="huser-dd-logout" id="btnHeaderLogout"><?= icon('logout') ?> Đăng xuất</button></li>
                            </ul>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="huser-auth-wrap" id="headerAuthWrap">
                        <button type="button" class="hauth-btn" id="openAuthModalBtn" title="Đăng nhập hoặc đăng ký">
                            <svg class="hauth-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                <circle cx="12" cy="7" r="4"></circle>
                            </svg>
                            <span class="hauth-text hide-sm">Đăng nhập</span>
                        </button>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- TIER 3: THANH ĐIỀU HƯỚNG DANH MỤC & CÁC CHUYÊN TRANG (NAVBAR DOANH NGHIỆP) -->
    <nav class="header-navbar hide-md" aria-label="Điều hướng chính">
        <div class="container header-navbar__inner">
            <!-- Nút Danh mục sản phẩm (Mega Menu) -->
            <div class="nav-cat-dropdown">
                <a href="<?= url('/san-pham') ?>" class="nav-cat-btn">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M3 12h18M3 6h18M3 18h18"/></svg>
                    <span>DANH MỤC THIẾT BỊ</span>
                    <?= icon('chevron-down', 'nav-cat-caret') ?>
                </a>

                <div class="mega-corp-menu" role="menu">
                    <div class="mega__col">
                        <p class="mega__title">Chủng loại thiết bị điện</p>
                        <?php foreach ($navCategories as $c): ?>
                            <a class="mega__link" href="<?= url('/danh-muc/' . $c['slug']) ?>" role="menuitem">
                                <span class="mega__icon"><?= icon($c['icon']) ?></span>
                                <span><?= e($c['name']) ?><small><?= (int) $c['product_count'] ?> sản phẩm</small></span>
                            </a>
                        <?php endforeach; ?>
                    </div>
                    <div class="mega__col">
                        <p class="mega__title">Các dòng Series Công Tắc</p>
                        <div class="mega__series">
                            <?php foreach ($navSeries as $s): ?>
                                <a class="series-chip" href="<?= url('/panasonic-' . $s['slug']) ?>" role="menuitem">
                                    <span class="series-chip__dot" style="--dot: <?= e($s['color']) ?>"></span><?= e($s['name']) ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                        <a href="<?= url('/tai-catalogue') ?>" class="mega__promo">
                            <?= icon('file-text') ?>
                            <span><strong>Catalogue Panasonic 2026</strong><small>Bảng giá niêm yết & thông số kỹ thuật</small></span>
                            <?= icon('arrow-right') ?>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Các liên kết trang chính -->
            <ul class="corp-nav-links">
                <li><a class="corp-nav-item <?= is_active('/') ?>" href="<?= url('/') ?>"><?= icon('home') ?> Trang chủ</a></li>
                <li><a class="corp-nav-item <?= is_active('/danh-muc/cong-tac-o-cam') ?>" href="<?= url('/danh-muc/cong-tac-o-cam') ?>">Công Tắc Ổ Cắm</a></li>
                <li><a class="corp-nav-item <?= is_active('/danh-muc/thiet-bi-dong-cat') ?>" href="<?= url('/danh-muc/thiet-bi-dong-cat') ?>">Aptomat MCB/RCBO</a></li>
                <li><a class="corp-nav-item <?= is_active('/danh-muc/den-led-chieu-sang') ?>" href="<?= url('/danh-muc/den-led-chieu-sang') ?>">Đèn LED Chiếu Sáng</a></li>
                <li><a class="corp-nav-item <?= is_active('/danh-muc/quat-tran-quat-hut') ?>" href="<?= url('/danh-muc/quat-tran-quat-hut') ?>">Quạt Trần & Quạt Hút</a></li>
                <li><a class="corp-nav-item <?= is_active('/danh-muc/day-cap-dien-cadivi') ?>" href="<?= url('/danh-muc/day-cap-dien-cadivi') ?>">Dây Cáp CADIVI</a></li>
                <li>
                    <a class="corp-nav-item corp-nav-item--hot <?= is_active('/du-an') ?>" href="<?= url('/du-an') ?>">
                        <?= icon('building') ?> Dự Án Thực Tế
                        <span class="badge-nav-hot">HOT</span>
                    </a>
                </li>
                <li><a class="corp-nav-item <?= is_active('/bao-gia-cong-trinh') ?>" href="<?= url('/bao-gia-cong-trinh') ?>"><?= icon('file-text') ?> Báo Giá Dự Án</a></li>
                <li><a class="corp-nav-item <?= is_active('/lien-he') ?>" href="<?= url('/lien-he') ?>"><?= icon('map-pin') ?> Tổng Kho & Liên Hệ</a></li>
            </ul>
        </div>
    </nav>
</header>

<!-- Modal Đăng nhập & Đăng ký (đặt ngoài header để tránh transform translateY che khuất) -->
<?php require VIEW_PATH . '/auth/modal.php'; ?>

<script>
(function() {
    var header = document.getElementById('siteHeader');
    if (!header) return;

    var lastY = window.pageYOffset || document.documentElement.scrollTop || 0;
    var isHidden = false;
    var threshold = 4; // Ngưỡng độ lệch cuộn chuột để bắt đầu kích hoạt

    function onHeaderScroll() {
        var currentY = Math.max(0, window.pageYOffset || document.documentElement.scrollTop || 0);

        // Về gần đỉnh trang (<= 30px): Luôn hiển thị đầy đủ menu
        if (currentY <= 30) {
            if (isHidden) {
                header.classList.remove('header--hidden');
                isHidden = false;
            }
            header.classList.remove('header--scrolled');
            lastY = currentY;
            return;
        }

        header.classList.add('header--scrolled');

        var diff = currentY - lastY;

        // Bỏ qua nếu độ lệch quá nhỏ để tránh nhấp nháy, giữ lastY tích lũy
        if (Math.abs(diff) < threshold) {
            return;
        }

        // KÉO XUỐNG: Menu từ từ trượt lên trên và ẩn đi
        if (diff > 0 && currentY > 50) {
            if (!isHidden) {
                header.classList.add('header--hidden');
                isHidden = true;
                var res = document.getElementById('searchResults');
                if (res) {
                    res.style.display = 'none';
                    res.setAttribute('hidden', '');
                }
            }
        }
        // KÉO LÊN: Menu từ từ trượt xuống hiện ra
        else if (diff < 0) {
            if (isHidden) {
                header.classList.remove('header--hidden');
                isHidden = false;
            }
        }

        lastY = currentY;
    }

    window.addEventListener('scroll', onHeaderScroll, { passive: true });
    window.__headerScrollBound = true;
})();
</script>
