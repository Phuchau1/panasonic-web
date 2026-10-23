<div class="admin-card mb-4">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h2 style="font-size: 1.3rem; margin: 0; color: #0f172a;">Cài Đặt Hệ Thống Website & Thông Tin Doanh Nghiệp</h2>
            <p style="font-size: 0.85rem; color: #64748b; margin: 0.25rem 0 0 0;">
                Mọi thay đổi tại đây sẽ được đồng bộ ngay lập tức lên Header, Footer, Trang Liên hệ, Trang Báo giá và API trên toàn bộ website.
            </p>
        </div>
        <a href="<?= url('/') ?>" target="_blank" class="btn btn--outline"><?= icon('eye') ?> Xem Website ngoài</a>
    </div>
</div>

<form method="post" action="<?= url('/admin/cai-dat') ?>" class="admin-card">
    <?= csrf_field() ?>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
        <!-- Cột 1: Thông tin thương hiệu & Hotline -->
        <div>
            <h3 style="font-size: 1.1rem; color: #004098; margin: 0 0 1rem 0; border-bottom: 2px solid #e2e8f0; padding-bottom: 0.5rem;">
                <?= icon('zap') ?> Thông Tin Đại Lý & Thương Hiệu
            </h3>

            <div class="form-group mb-3">
                <label class="form-label">Tên hiển thị Website</label>
                <input type="text" name="name" value="<?= e(config('name')) ?>" class="form-control" required>
            </div>

            <div class="form-group mb-3">
                <label class="form-label">Đơn vị chủ quản / Công ty phân phối</label>
                <input type="text" name="company" value="<?= e(config('company')) ?>" class="form-control" required>
            </div>

            <div class="form-group mb-3">
                <label class="form-label">Khẩu hiệu / Slogan chính</label>
                <input type="text" name="tagline" value="<?= e(config('tagline')) ?>" class="form-control">
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;" class="mb-3">
                <div class="form-group">
                    <label class="form-label">Hotline tư vấn chính <span class="text-danger">*</span></label>
                    <input type="text" name="hotline" value="<?= e(config('hotline')) ?>" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Hotline kinh doanh dự án</label>
                    <input type="text" name="hotline_sales" value="<?= e(config('hotline_sales')) ?>" class="form-control">
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;" class="mb-3">
                <div class="form-group">
                    <label class="form-label">Số điện thoại Zalo</label>
                    <input type="text" name="zalo" value="<?= e(config('zalo')) ?>" class="form-control">
                </div>
                <div class="form-group">
                    <label class="form-label">Email tiếp nhận báo giá</label>
                    <input type="email" name="email" value="<?= e(config('email')) ?>" class="form-control" required>
                </div>
            </div>

            <div class="form-group mb-3">
                <label class="form-label">Mã số thuế doanh nghiệp</label>
                <input type="text" name="tax_code" value="<?= e(config('tax_code')) ?>" class="form-control">
            </div>
        </div>

        <!-- Cột 2: Địa chỉ kho & Cấu hình OAuth -->
        <div>
            <h3 style="font-size: 1.1rem; color: #004098; margin: 0 0 1rem 0; border-bottom: 2px solid #e2e8f0; padding-bottom: 0.5rem;">
                <?= icon('map-pin') ?> Địa Chỉ Kho Bãi & Vận Chuyển
            </h3>

            <div class="form-group mb-3">
                <label class="form-label">Địa chỉ kho tổng (Hà Nội, TP.HCM & Long An)</label>
                <textarea name="address" class="form-control" rows="3" required><?= e(config('address')) ?></textarea>
            </div>

            <div class="form-group mb-3">
                <label class="form-label">Thời gian làm việc & giao hàng</label>
                <input type="text" name="working_hours" value="<?= e(config('working_hours')) ?>" class="form-control" required>
            </div>

            <div class="form-group mb-4">
                <label class="form-label">Giá trị đơn hàng miễn phí vận chuyển (₫)</label>
                <input type="number" name="free_ship_from" value="<?= (int)config('free_ship_from', 2000000) ?>" class="form-control">
            </div>

            <h3 style="font-size: 1.1rem; color: #004098; margin: 1.5rem 0 1rem 0; border-bottom: 2px solid #e2e8f0; padding-bottom: 0.5rem;">
                <?= icon('shield') ?> Tích Hợp Đăng Nhập Mạng Xã Hội (API OAuth)
            </h3>

            <div class="form-group mb-3">
                <label class="form-label">Google Client ID (Google OAuth 2.0)</label>
                <input type="text" name="google_client_id" value="<?= e(config('google_client_id', '')) ?>" class="form-control" placeholder="xxxx.apps.googleusercontent.com">
                <small class="text-muted">Dùng cho đăng nhập bằng tài khoản Google trên Header và Modal.</small>
            </div>

            <div class="form-group mb-4">
                <label class="form-label">Facebook App ID</label>
                <input type="text" name="facebook_app_id" value="<?= e(config('facebook_app_id', '')) ?>" class="form-control" placeholder="1234567890">
                <small class="text-muted">Dùng cho đăng nhập bằng tài khoản Facebook.</small>
            </div>
        </div>
    </div>

    <div style="border-top: 1px solid #e2e8f0; padding-top: 1.25rem; display: flex; justify-content: flex-end;">
        <button type="submit" class="btn btn--primary" style="padding: 0.65rem 2rem; font-weight: 600;">
            Lưu Thay Đổi & Đồng Bộ Toàn Hệ Thống
        </button>
    </div>
</form>
