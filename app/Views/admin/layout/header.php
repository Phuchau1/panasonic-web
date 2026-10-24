<?php
/** @var string $title */
$admin = admin_user();

$db = \Core\Database::getInstance();
$badgeOrders = 0;
$badgeQuotes = 0;
$badgeContacts = 0;
$badgeLowStock = 0;
try {
    $badgeOrders = (int) $db->fetchColumn("SELECT COUNT(*) FROM orders WHERE status = 'pending'");
    $badgeQuotes = (int) $db->fetchColumn("SELECT COUNT(*) FROM quotes WHERE status = 'pending'");
    $badgeContacts = (int) $db->fetchColumn("SELECT COUNT(*) FROM contacts WHERE status = 'new'");
    $badgeLowStock = (int) $db->fetchColumn("SELECT COUNT(*) FROM products WHERE status = 1 AND stock < 10");
} catch (\Throwable $e) {}
?><!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($title) ?> | Quản Trị Hệ Thống Panasonic</title>
    <link rel="icon" href="<?= asset('uploads/favicon.svg') ?>" type="image/svg+xml">
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= asset('css/main.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/responsive.css') ?>">
    <style>
        .admin-layout { display: flex; min-height: 100vh; background: #f8fafc; font-family: 'Be Vietnam Pro', sans-serif; }
        .admin-aside { width: 260px; background: #0f172a; color: #fff; flex-shrink: 0; display: flex; flex-direction: column; height: 100vh; position: sticky; top: 0; overflow-y: auto; }
        .admin-brand { padding: 1.25rem 1.5rem; border-bottom: 1px solid rgba(255,255,255,0.1); font-weight: 700; font-size: 1.15rem; color: #fff; display: flex; align-items: center; justify-content: space-between; }
        .admin-nav { padding: 0.5rem 0 1.5rem 0; flex: 1; }
        .admin-nav-group-title { font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.8px; color: #64748b; font-weight: 700; padding: 1.1rem 1.5rem 0.35rem 1.5rem; }
        .admin-nav a { display: flex; align-items: center; justify-content: space-between; padding: 0.65rem 1.5rem; color: #94a3b8; font-weight: 500; font-size: 0.88rem; text-decoration: none; transition: 0.2s; }
        .admin-nav a:hover, .admin-nav a.is-active { background: #1e293b; color: #fff; }
        .admin-nav a.is-active { border-left: 4px solid #004098; color: #38bdf8; background: #1e293b; font-weight: 600; }
        .admin-main { flex: 1; display: flex; flex-direction: column; min-width: 0; overflow-x: hidden; }
        .admin-topbar { height: 60px; background: #fff; border-bottom: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: space-between; padding: 0 1.5rem; position: sticky; top: 0; z-index: 100; }
        .admin-content { padding: 1.5rem; flex: 1; }
        .admin-card { background: #fff; border-radius: 10px; border: 1px solid #e2e8f0; padding: 1.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.05); }

        /* Sửa triệt để màu input admin: Trắng sáng, tương phản cao, dễ nhìn */
        .admin-content input[type="text"],
        .admin-content input[type="number"],
        .admin-content input[type="url"],
        .admin-content input[type="search"],
        .admin-content input[type="email"],
        .admin-content input[type="password"],
        .admin-content select,
        .admin-content textarea,
        .admin-content .field__input {
            background-color: #ffffff !important;
            color: #0f172a !important;
            border: 1.5px solid #cbd5e1 !important;
            border-radius: 7px !important;
            padding: 0.55rem 0.85rem !important;
            font-size: 0.9rem !important;
            font-family: inherit !important;
            box-shadow: 0 1px 2px rgba(0,0,0,0.03) !important;
            transition: all 0.2s ease !important;
            outline: none !important;
        }
        .admin-content input[type="file"] {
            background: #ffffff !important;
            border: 1.5px dashed #cbd5e1 !important;
            padding: 0.45rem 0.65rem !important;
            border-radius: 7px !important;
            color: #334155 !important;
        }
        .admin-content input:focus,
        .admin-content select:focus,
        .admin-content textarea:focus,
        .admin-content .field__input:focus {
            border-color: #004098 !important;
            box-shadow: 0 0 0 3px rgba(0, 64, 152, 0.12) !important;
            background-color: #ffffff !important;
        }
        .admin-content .field__label {
            color: #334155 !important;
            font-weight: 600 !important;
            font-size: 0.85rem !important;
            margin-bottom: 0.35rem !important;
            display: block !important;
        }

        /* Mobile drawer styles */
        @media (max-width: 991px) {
            .admin-aside {
                position: fixed;
                left: 0;
                top: 0;
                bottom: 0;
                z-index: 9999;
                transform: translateX(-100%);
                transition: transform 0.25s cubic-bezier(0.4, 0, 0.2, 1);
                box-shadow: 4px 0 20px rgba(0,0,0,0.3);
            }
            .admin-aside.is-open {
                transform: translateX(0);
            }
            .admin-backdrop {
                display: none;
                position: fixed;
                inset: 0;
                background: rgba(15, 23, 42, 0.6);
                backdrop-filter: blur(2px);
                z-index: 9998;
            }
            .admin-backdrop.is-open {
                display: block;
            }
            .admin-menu-toggle {
                display: inline-flex !important;
                align-items: center;
                justify-content: center;
                width: 38px;
                height: 38px;
                background: #f8fafc;
                border: 1.5px solid #cbd5e1;
                border-radius: 6px;
                color: #0f172a;
                cursor: pointer;
                margin-right: 0.75rem;
            }
            .admin-content {
                padding: 1rem;
            }
        }
        @media (min-width: 992px) {
            .admin-menu-toggle {
                display: none !important;
            }
            .admin-backdrop {
                display: none !important;
            }
        }
    </style>
</head>
<body>
<div class="admin-backdrop" id="adminBackdrop" onclick="toggleAdminSidebar(false)"></div>
<div class="admin-layout">
    <aside class="admin-aside" id="adminSidebar">
        <div class="admin-brand">
            <span style="display: flex; align-items: center; gap: 0.5rem;">
                <?= icon('zap') ?> Panasonic Admin
            </span>
            <button type="button" class="admin-menu-toggle" onclick="toggleAdminSidebar(false)" style="background: transparent; border: none; color: #94a3b8; font-size: 1.1rem; width: 30px; height: 30px; padding: 0;">
                <?= icon('x') ?>
            </button>
        </div>
        <?php $reqUri = $_SERVER['REQUEST_URI'] ?? ''; ?>
        <nav class="admin-nav">
            <!-- NHÓM 1: TỔNG QUAN & BÁO CÁO -->
            <div class="admin-nav-group-title">Tổng quan & Báo cáo</div>
            <a href="<?= url('/admin') ?>" class="<?= is_active('/admin') && !str_contains($reqUri, '/admin/') ? 'is-active' : '' ?>">
                <span style="display: flex; align-items: center; gap: 0.75rem;"><?= icon('home') ?> Bảng điều khiển</span>
            </a>
            <a href="<?= url('/admin/thong-ke') ?>" class="<?= str_contains($reqUri, '/admin/thong-ke') ? 'is-active' : '' ?>">
                <span style="display: flex; align-items: center; gap: 0.75rem;"><?= icon('calculator') ?> Báo cáo doanh thu</span>
            </a>

            <!-- NHÓM 2: BÁN HÀNG & DỰ ÁN -->
            <div class="admin-nav-group-title">Bán hàng & Dự án</div>
            <a href="<?= url('/admin/don-hang') ?>" class="<?= str_contains($reqUri, '/admin/don-hang') ? 'is-active' : '' ?>">
                <span style="display: flex; align-items: center; gap: 0.75rem;"><?= icon('cart') ?> Đơn đặt hàng</span>
                <?php if ($badgeOrders > 0): ?>
                    <span class="badge" style="background: #ef4444; color: #fff; font-size: 0.7rem; font-weight: 700; padding: 2px 7px; border-radius: 10px;"><?= $badgeOrders ?></span>
                <?php endif; ?>
            </a>
            <a href="<?= url('/admin/bao-gia') ?>" class="<?= str_contains($reqUri, '/admin/bao-gia') ? 'is-active' : '' ?>">
                <span style="display: flex; align-items: center; gap: 0.75rem;"><?= icon('upload') ?> Yêu cầu báo giá</span>
                <?php if ($badgeQuotes > 0): ?>
                    <span class="badge" style="background: #f59e0b; color: #fff; font-size: 0.7rem; font-weight: 700; padding: 2px 7px; border-radius: 10px;"><?= $badgeQuotes ?></span>
                <?php endif; ?>
            </a>
            <a href="<?= url('/admin/lien-he') ?>" class="<?= str_contains($reqUri, '/admin/lien-he') ? 'is-active' : '' ?>">
                <span style="display: flex; align-items: center; gap: 0.75rem;"><?= icon('mail') ?> Hộp thư liên hệ</span>
                <?php if ($badgeContacts > 0): ?>
                    <span class="badge" style="background: #0284c7; color: #fff; font-size: 0.7rem; font-weight: 700; padding: 2px 7px; border-radius: 10px;"><?= $badgeContacts ?></span>
                <?php endif; ?>
            </a>
            <a href="<?= url('/admin/du-an') ?>" class="<?= str_contains($reqUri, '/admin/du-an') ? 'is-active' : '' ?>">
                <span style="display: flex; align-items: center; gap: 0.75rem;"><?= icon('building') ?> Dự án công trình</span>
            </a>

            <!-- NHÓM 3: SẢN PHẨM & KHO -->
            <div class="admin-nav-group-title">Sản phẩm & Kho</div>
            <a href="<?= url('/admin/san-pham') ?>" class="<?= str_contains($reqUri, '/admin/san-pham') ? 'is-active' : '' ?>">
                <span style="display: flex; align-items: center; gap: 0.75rem;"><?= icon('box') ?> Quản lý sản phẩm</span>
                <?php if ($badgeLowStock > 0): ?>
                    <span class="badge" style="background: #dc2626; color: #fff; font-size: 0.7rem; font-weight: 700; padding: 2px 7px; border-radius: 10px;" title="Cảnh báo tồn kho thấp"><?= $badgeLowStock ?></span>
                <?php endif; ?>
            </a>
            <a href="<?= url('/admin/danh-muc') ?>" class="<?= str_contains($reqUri, '/admin/danh-muc') ? 'is-active' : '' ?>">
                <span style="display: flex; align-items: center; gap: 0.75rem;"><?= icon('grid') ?> Danh mục thiết bị</span>
            </a>
            <a href="<?= url('/admin/dong-series') ?>" class="<?= str_contains($reqUri, '/admin/dong-series') ? 'is-active' : '' ?>">
                <span style="display: flex; align-items: center; gap: 0.75rem;"><?= icon('layers') ?> Dòng sản phẩm (Series)</span>
            </a>
            <a href="<?= url('/admin/danh-gia') ?>" class="<?= str_contains($reqUri, '/admin/danh-gia') ? 'is-active' : '' ?>">
                <span style="display: flex; align-items: center; gap: 0.75rem;"><?= icon('star') ?> Đánh giá sản phẩm</span>
            </a>

            <!-- NHÓM 4: HỆ THỐNG & CÀI ĐẶT -->
            <div class="admin-nav-group-title">Hệ thống & Cài đặt</div>
            <a href="<?= url('/admin/thanh-vien') ?>" class="<?= str_contains($reqUri, '/admin/thanh-vien') ? 'is-active' : '' ?>">
                <span style="display: flex; align-items: center; gap: 0.75rem;"><?= icon('users') ?> Khách hàng & Thành viên</span>
            </a>
            <a href="<?= url('/admin/cai-dat') ?>" class="<?= str_contains($reqUri, '/admin/cai-dat') ? 'is-active' : '' ?>">
                <span style="display: flex; align-items: center; gap: 0.75rem;"><?= icon('settings') ?> Cài đặt website</span>
            </a>
            <a href="<?= url('/') ?>" target="_blank">
                <span style="display: flex; align-items: center; gap: 0.75rem;"><?= icon('eye') ?> Xem Website</span>
                <span style="font-size: 0.75rem; color: #64748b;"><?= icon('arrow-right') ?></span>
            </a>
        </nav>
        <div style="padding: 1rem 1.5rem; border-top: 1px solid rgba(255,255,255,0.1); background: #0b1120;">
            <div style="font-size: 0.85rem; font-weight: 600; color: #f1f5f9;"><?= e($admin['full_name'] ?? 'Admin') ?></div>
            <div style="font-size: 0.75rem; color: #64748b;"><?= e($admin['username'] ?? '') ?> &bull; <span style="color: #38bdf8; text-transform: uppercase; font-size: 0.7rem; font-weight: 700;"><?= e($admin['role'] ?? 'admin') ?></span></div>
            <a href="<?= url('/admin/logout') ?>" style="display: inline-flex; align-items: center; gap: 0.35rem; margin-top: 0.5rem; font-size: 0.8rem; color: #f87171; text-decoration: none; font-weight: 500;">
                <?= icon('logout') ?> Đăng xuất
            </a>
        </div>
    </aside>

    <div class="admin-main">
        <header class="admin-topbar">
            <div style="display: flex; align-items: center;">
                <button type="button" class="admin-menu-toggle" onclick="toggleAdminSidebar(true)" aria-label="Mở menu quản trị">
                    <?= icon('menu') ?>
                </button>
                <strong style="font-size: 1.05rem; color: #0f172a;"><?= e($title) ?></strong>
            </div>
            <div style="display: flex; align-items: center; gap: 0.75rem;">
                <a href="<?= url('/admin/thong-ke') ?>" class="badge badge--primary" style="text-decoration: none; display: inline-flex; align-items: center; gap: 0.35rem; padding: 0.35rem 0.75rem;">
                    <?= icon('zap') ?> <span>Hệ thống phân phối điện</span>
                </a>
            </div>
        </header>

        <div class="admin-content">
            <?php if ($msg = flash('success')): ?>
                <div class="flash flash--success mb-3"><?= icon('check-circle') ?> <span><?= e($msg) ?></span></div>
            <?php endif; ?>
            <?php if ($msg = flash('error')): ?>
                <div class="flash flash--error mb-3"><?= icon('alert') ?> <span><?= e($msg) ?></span></div>
            <?php endif; ?>

<script>
function toggleAdminSidebar(show) {
    const aside = document.getElementById('adminSidebar');
    const backdrop = document.getElementById('adminBackdrop');
    if (aside && backdrop) {
        if (show) {
            aside.classList.add('is-open');
            backdrop.classList.add('is-open');
        } else {
            aside.classList.remove('is-open');
            backdrop.classList.remove('is-open');
        }
    }
}
</script>
