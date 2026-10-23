<div class="container site-auth-page">
    <div class="auth-card-standalone">
        <div class="auth-modal__header">
            <div class="auth-modal__badge">PANASONIC VIỆT NAM</div>
            <h1 class="auth-modal__title">Đăng Nhập Tài Khoản</h1>
            <p class="auth-modal__sub">Quản lý đơn hàng, lưu danh mục bóc tách vật tư và hưởng chính sách chiết khấu tốt nhất.</p>
        </div>

        <div class="auth-modal__body">
            <!-- Alert thông báo lỗi / thành công -->
            <div class="auth-alert" id="authAlert" hidden role="alert">
                <span class="auth-alert__icon" id="authAlertIcon"></span>
                <span class="auth-alert__text" id="authAlertText"></span>
            </div>

            <!-- Nút Đăng nhập Nhanh Mạng Xã Hội -->
            <div class="auth-social-wrap">
                <div class="auth-social-grid">
                    <button type="button" class="btn-social btn-google" id="btnGoogleAuth" title="Đăng nhập bảo mật với Google">
                        <svg class="social-icon" width="20" height="20" viewBox="0 0 24 24">
                            <path fill="#4285F4" d="M23.745 12.27c0-.7-.06-1.4-.19-2.07H12v4.51h6.6c-.29 1.52-1.14 2.82-2.4 3.68v3.05h3.88c2.27-2.09 3.665-5.17 3.665-9.17z"/>
                            <path fill="#34A853" d="M12 24c3.24 0 5.95-1.08 7.93-2.91l-3.88-3.05c-1.08.72-2.45 1.16-4.05 1.16-3.12 0-5.77-2.1-6.72-4.93H1.25v3.15C3.26 21.36 7.33 24 12 24z"/>
                            <path fill="#FBBC05" d="M5.28 14.27c-.25-.72-.38-1.49-.38-2.27s.13-1.55.38-2.27V6.58H1.25C.45 8.18 0 9.99 0 12s.45 3.82 1.25 5.42l4.03-3.15z"/>
                            <path fill="#EA4335" d="M12 4.75c1.77 0 3.35.61 4.6 1.8l3.42-3.42C17.95 1.19 15.24 0 12 0 7.33 0 3.26 2.64 1.25 6.58l4.03 3.15c.95-2.83 3.6-4.98 6.72-4.98z"/>
                        </svg>
                        <span>Tiếp tục với Google</span>
                    </button>

                    <button type="button" class="btn-social btn-facebook" id="btnFacebookAuth" title="Đăng nhập tiện lợi với Facebook">
                        <svg class="social-icon" width="20" height="20" viewBox="0 0 24 24">
                            <path fill="#1877F2" d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                        </svg>
                        <span>Tiếp tục với Facebook</span>
                    </button>
                </div>

                <div class="auth-divider">
                    <span>HOẶC SỬ DỤNG TÀI KHOẢN</span>
                </div>
            </div>

            <!-- Form Đăng Nhập -->
            <form id="formLogin" class="auth-form" novalidate>
                <input type="hidden" name="_token" value="<?= csrf_token() ?>">

                <div class="auth-form__group">
                    <label for="loginIdentifier" class="auth-form__label">Email hoặc Số điện thoại</label>
                    <div class="auth-input-wrap">
                        <span class="auth-input-icon"><?= icon('mail') ?></span>
                        <input type="text" id="loginIdentifier" name="identifier" class="auth-input" placeholder="nhathau@congtrinh.vn hoặc 090..." required autocomplete="username">
                    </div>
                </div>

                <div class="auth-form__group">
                    <div class="auth-form__flex-label">
                        <label for="loginPassword" class="auth-form__label">Mật khẩu</label>
                        <a href="javascript:void(0)" class="auth-forgot-link" id="linkForgotPassword">Quên mật khẩu?</a>
                    </div>
                    <div class="auth-input-wrap">
                        <span class="auth-input-icon"><?= icon('shield') ?></span>
                        <input type="password" id="loginPassword" name="password" class="auth-input" placeholder="••••••••" required autocomplete="current-password">
                        <button type="button" class="auth-toggle-pwd" data-toggle-pwd="#loginPassword" aria-label="Xem mật khẩu">
                            <?= icon('eye') ?>
                        </button>
                    </div>
                </div>

                <div class="auth-form__check-row">
                    <label class="auth-checkbox-label">
                        <input type="checkbox" name="remember" id="loginRemember" checked>
                        <span>Ghi nhớ đăng nhập trên thiết bị này</span>
                    </label>
                </div>

                <button type="submit" class="auth-submit-btn" id="btnLoginSubmit">
                    <span class="btn-text">ĐĂNG NHẬP VÀO HỆ THỐNG</span>
                    <span class="btn-spinner" hidden></span>
                </button>

                <div class="auth-switch-prompt">
                    Chưa có tài khoản? <a href="<?= url('/dang-ky') ?>" class="auth-switch-link">Đăng ký thành viên mới »</a>
                </div>
            </form>
        </div>
    </div>
</div>
