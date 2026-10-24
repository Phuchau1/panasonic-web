<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Đăng nhập Quản trị | Panasonic Pro</title>
    <link rel="icon" href="<?= asset('uploads/favicon.svg') ?>" type="image/svg+xml">
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= asset('css/main.css') ?>">
    <style>
        body { background: #0f172a; min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .login-box { width: 100%; max-width: 400px; background: #fff; padding: 2.5rem; border-radius: 12px; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.3); }
        .login-brand { text-align: center; margin-bottom: 2rem; }
        .login-brand .logo__mark { font-size: 2rem; color: #004098; font-weight: 800; }
    </style>
</head>
<body>
<div class="login-box">
    <div class="login-brand">
        <span class="logo__mark">Panasonic</span>
        <div style="font-size: 0.9rem; color: #64748b; font-weight: 600;">HỆ THỐNG QUẢN TRỊ</div>
    </div>

    <?php if ($msg = flash('error')): ?>
        <div class="flash flash--error mb-3" style="padding: 0.75rem; font-size: 0.85rem;"><?= icon('alert') ?> <?= e($msg) ?></div>
    <?php endif; ?>

    <form action="<?= url('/admin/login') ?>" method="post">
        <?= csrf_field() ?>
        <div class="form-group mb-3">
            <label class="field__label">Tên đăng nhập</label>
            <input type="text" name="username" class="field__input" required autofocus placeholder="admin">
        </div>
        <div class="form-group mb-3">
            <label class="field__label">Mật khẩu</label>
            <input type="password" name="password" class="field__input" required placeholder="••••••••">
        </div>
        <div style="display: flex; align-items: center; margin-bottom: 1.25rem;">
            <label style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.85rem; color: #475569; cursor: pointer; user-select: none;">
                <input type="checkbox" name="remember" value="1" checked style="accent-color: #004098; width: 17px; height: 17px;">
                <span>Duy trì đăng nhập (Không cần đăng nhập lại khi load)</span>
            </label>
        </div>
        <button type="submit" class="btn btn--primary btn--block btn--lg">Đăng nhập</button>
    </form>
    <div style="text-align: center; margin-top: 1.5rem; font-size: 0.8rem; color: #94a3b8;">
        Mặc định: <code>admin</code> / <code>Admin@123</code>
    </div>
</div>
</body>
</html>
