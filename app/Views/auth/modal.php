<!-- Modal Đăng nhập / Đăng ký (AJAX & API Ready) -->
<div class="auth-modal" id="authModal" aria-hidden="true" role="dialog" aria-modal="true" aria-labelledby="authModalTitle">
    <div class="auth-modal__backdrop" id="authBackdrop" data-auth-close></div>
    
    <div class="auth-modal__dialog">
        <button type="button" class="auth-modal__close" data-auth-close aria-label="Đóng cửa sổ">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <line x1="18" y1="6" x2="6" y2="18"></line>
                <line x1="6" y1="6" x2="18" y2="18"></line>
            </svg>
        </button>

        <div class="auth-modal__header">
            <div class="auth-modal__badge">PANASONIC VIỆT NAM</div>
            <h2 class="auth-modal__title" id="authModalTitle">Tài Khoản Khách Hàng</h2>
            <p class="auth-modal__sub">Đăng nhập để theo dõi đơn hàng, lưu bảng bóc tách vật tư và nhận chiết khấu đại lý tốt nhất.</p>
        </div>

        <!-- Tab chuyển đổi Đăng nhập / Đăng ký -->
        <div class="auth-tabs" role="tablist">
            <button type="button" class="auth-tab-btn is-active" id="tabBtnLogin" data-target="#tabPaneLogin" role="tab" aria-selected="true">
                Đăng Nhập
            </button>
            <button type="button" class="auth-tab-btn" id="tabBtnRegister" data-target="#tabPaneRegister" role="tab" aria-selected="false">
                Đăng Ký Tài Khoản
            </button>
        </div>

        <div class="auth-modal__body">
            <!-- Alert thông báo lỗi / thành công -->
            <div class="auth-alert" id="authAlert" hidden role="alert">
                <span class="auth-alert__icon" id="authAlertIcon"></span>
                <span class="auth-alert__text" id="authAlertText"></span>
            </div>

            <!-- Nút Đăng nhập Nhanh Mạng Xã Hội (Google & Facebook) -->
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

            <!-- Pane 1: Đăng Nhập -->
            <div class="auth-tab-pane is-active" id="tabPaneLogin" role="tabpanel">
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
                        <span class="btn-text">ĐĂNG NHẬP</span>
                        <span class="btn-spinner" hidden></span>
                    </button>

                    <div class="auth-switch-prompt">
                        Chưa có tài khoản? <a href="javascript:void(0)" class="auth-switch-link" data-switch-tab="register">Đăng ký thành viên ngay »</a>
                    </div>
                </form>
            </div>

            <!-- Pane 2: Đăng Ký -->
            <div class="auth-tab-pane" id="tabPaneRegister" role="tabpanel" hidden>
                <form id="formRegister" class="auth-form" novalidate>
                    <input type="hidden" name="_token" value="<?= csrf_token() ?>">

                    <div class="auth-form__group">
                        <label for="regName" class="auth-form__label">Họ và tên <span class="req">*</span></label>
                        <div class="auth-input-wrap">
                            <span class="auth-input-icon"><?= icon('user') ?></span>
                            <input type="text" id="regName" name="name" class="auth-input" placeholder="Nguyễn Văn A" required>
                        </div>
                    </div>

                    <div class="auth-form__row-2">
                        <div class="auth-form__group">
                            <label for="regEmail" class="auth-form__label">Địa chỉ Email <span class="req">*</span></label>
                            <div class="auth-input-wrap">
                                <span class="auth-input-icon"><?= icon('mail') ?></span>
                                <input type="email" id="regEmail" name="email" class="auth-input" placeholder="name@example.com" required>
                            </div>
                        </div>

                        <div class="auth-form__group">
                            <label for="regPhone" class="auth-form__label">Số điện thoại</label>
                            <div class="auth-input-wrap">
                                <span class="auth-input-icon"><?= icon('phone') ?></span>
                                <input type="tel" id="regPhone" name="phone" class="auth-input" placeholder="0909xxxxxx">
                            </div>
                        </div>
                    </div>

                    <div class="auth-form__row-2">
                        <div class="auth-form__group">
                            <label for="regPassword" class="auth-form__label">Mật khẩu <span class="req">*</span></label>
                            <div class="auth-input-wrap">
                                <span class="auth-input-icon"><?= icon('shield') ?></span>
                                <input type="password" id="regPassword" name="password" class="auth-input" placeholder="Tối thiểu 6 ký tự" required autocomplete="new-password">
                                <button type="button" class="auth-toggle-pwd" data-toggle-pwd="#regPassword" aria-label="Xem mật khẩu">
                                    <?= icon('eye') ?>
                                </button>
                            </div>
                        </div>

                        <div class="auth-form__group">
                            <label for="regPasswordConfirm" class="auth-form__label">Xác nhận mật khẩu <span class="req">*</span></label>
                            <div class="auth-input-wrap">
                                <span class="auth-input-icon"><?= icon('shield') ?></span>
                                <input type="password" id="regPasswordConfirm" name="password_confirmation" class="auth-input" placeholder="Nhập lại mật khẩu" required autocomplete="new-password">
                                <button type="button" class="auth-toggle-pwd" data-toggle-pwd="#regPasswordConfirm" aria-label="Xem mật khẩu">
                                    <?= icon('eye') ?>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="auth-form__check-row">
                        <label class="auth-checkbox-label">
                            <input type="checkbox" name="terms" id="regTerms" checked required>
                            <span>Tôi đồng ý với chính sách bảo mật & quy định đại lý của Panasonic</span>
                        </label>
                    </div>

                    <button type="submit" class="auth-submit-btn" id="btnRegisterSubmit">
                        <span class="btn-text">TẠO TÀI KHOẢN MỚI</span>
                        <span class="btn-spinner" hidden></span>
                    </button>

                    <div class="auth-switch-prompt">
                        Đã có tài khoản? <a href="javascript:void(0)" class="auth-switch-link" data-switch-tab="login">Đăng nhập tại đây »</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
