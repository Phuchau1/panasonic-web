<?php
/** @var string $title */
$admin = admin_user();
?><!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($title) ?> | Quản Trị Hệ Thống Panasonic</title>
    <link rel="icon" href="<?= asset('uploads/favicon.svg') ?>" type="image/svg+xml">
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= asset('css/main.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/responsive.css') ?>">
    <style>
        .admin-layout { display: flex; min-height: 100vh; background: #f8fafc; font-family: 'Be Vietnam Pro', sans-serif; }
        .admin-aside { width: 260px; background: #0f172a; color: #fff; flex-shrink: 0; display: flex; flex-direction: column; }
        .admin-brand { padding: 1.5rem; border-bottom: 1px solid rgba(255,255,255,0.1); font-weight: 700; font-size: 1.15rem; color: #fff; display: flex; align-items: center; gap: 0.5rem; }
        .admin-nav { padding: 1rem 0; flex: 1; }
        .admin-nav a { display: flex; align-items: center; gap: 0.75rem; padding: 0.85rem 1.5rem; color: #94a3b8; font-weight: 500; text-decoration: none; transition: 0.2s; }
        .admin-nav a:hover, .admin-nav a.is-active { background: #1e293b; color: #fff; }
        .admin-nav a.is-active { border-left: 4px solid #004098; color: #38bdf8; }
        .admin-main { flex: 1; display: flex; flex-direction: column; overflow-x: hidden; }
        .admin-topbar { height: 60px; background: #fff; border-bottom: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: space-between; padding: 0 1.5rem; }
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
    </style>
</head>
<body>
<div class="admin-layout">
    <aside class="admin-aside">
        <div class="admin-brand">
            <?= icon('zap') ?> Panasonic Admin
        </div>
        <?php $reqUri = $_SERVER['REQUEST_URI'] ?? ''; ?>
        <nav class="admin-nav">
            <a href="<?= url('/admin') ?>" class="<?= is_active('/admin') && !str_contains($reqUri, '/admin/') ? 'is-active' : '' ?>"><?= icon('home') ?> Tổng quan</a>
            <a href="<?= url('/admin/san-pham') ?>" class="<?= str_contains($reqUri, '/admin/san-pham') ? 'is-active' : '' ?>"><?= icon('grid') ?> Quản lý sản phẩm</a>
            <a href="<?= url('/admin/don-hang') ?>" class="<?= str_contains($reqUri, '/admin/don-hang') ? 'is-active' : '' ?>"><?= icon('cart') ?> Đơn đặt hàng</a>
            <a href="<?= url('/admin/bao-gia') ?>" class="<?= str_contains($reqUri, '/admin/bao-gia') ? 'is-active' : '' ?>"><?= icon('upload') ?> Yêu cầu báo giá</a>
            <a href="<?= url('/admin/du-an') ?>" class="<?= str_contains($reqUri, '/admin/du-an') ? 'is-active' : '' ?>"><?= icon('building') ?> Dự án công trình</a>
            <a href="<?= url('/admin/thanh-vien') ?>" class="<?= str_contains($reqUri, '/admin/thanh-vien') ? 'is-active' : '' ?>"><?= icon('users') ?> Thành viên & Khách</a>
            <a href="<?= url('/admin/danh-gia') ?>" class="<?= str_contains($reqUri, '/admin/danh-gia') ? 'is-active' : '' ?>"><?= icon('star') ?> Đánh giá sản phẩm</a>
            <a href="<?= url('/admin/cai-dat') ?>" class="<?= str_contains($reqUri, '/admin/cai-dat') ? 'is-active' : '' ?>"><?= icon('settings') ?> Cài đặt website</a>
            <a href="<?= url('/') ?>" target="_blank"><?= icon('eye') ?> Xem Website</a>
        </nav>
        <div style="padding: 1rem 1.5rem; border-top: 1px solid rgba(255,255,255,0.1);">
            <div style="font-size: 0.85rem; color: #cbd5e1;"><?= e($admin['full_name'] ?? 'Admin') ?></div>
            <div style="font-size: 0.75rem; color: #64748b;"><?= e($admin['username'] ?? '') ?> (<?= e($admin['role'] ?? '') ?>)</div>
            <a href="<?= url('/admin/logout') ?>" style="display: inline-block; margin-top: 0.5rem; font-size: 0.8rem; color: #ef4444;"><?= icon('logout') ?> Đăng xuất</a>
        </div>
    </aside>

    <div class="admin-main">
        <header class="admin-topbar">
            <div><strong><?= e($title) ?></strong></div>
            <div><span class="badge badge--primary">Hệ thống phân phối điện</span></div>
        </header>

        <div class="admin-content">
            <?php if ($msg = flash('success')): ?>
                <div class="flash flash--success mb-3"><?= icon('check-circle') ?> <span><?= e($msg) ?></span></div>
            <?php endif; ?>
            <?php if ($msg = flash('error')): ?>
                <div class="flash flash--error mb-3"><?= icon('alert') ?> <span><?= e($msg) ?></span></div>
            <?php endif; ?>
