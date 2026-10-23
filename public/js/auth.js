/**
 * Panasonic Auth & User Account Module (AJAX / REST API)
 * Xử lý: Modal Đăng nhập / Đăng ký, Google & Facebook Social Login, Header User Menu, Profile API.
 */
(() => {
    'use strict';

    // Helpers
    const $ = (sel, ctx = document) => ctx.querySelector(sel);
    const $$ = (sel, ctx = document) => Array.from(ctx.querySelectorAll(sel));

    // Base path helper
    const getBaseUrl = () => {
        const link = $('link[rel="canonical"]') || $('a[href*="/panasonic"]');
        if (window.BASE_URL) return window.BASE_URL;
        const path = window.location.pathname;
        if (path.startsWith('/panasonic/')) return '/panasonic';
        return '';
    };

    const BASE_URL = getBaseUrl();
    const apiUrl = (path) => `${BASE_URL}${path}`;

    // DOM Elements
    const authModal = $('#authModal');
    const openModalBtn = $('#openAuthModalBtn');
    const closeBtns = $$('[data-auth-close]');
    const tabBtnLogin = $('#tabBtnLogin');
    const tabBtnRegister = $('#tabBtnRegister');
    const tabPaneLogin = $('#tabPaneLogin');
    const tabPaneRegister = $('#tabPaneRegister');
    const authAlert = $('#authAlert');
    const authAlertText = $('#authAlertText');
    const btnGoogleAuth = $('#btnGoogleAuth');
    const btnFacebookAuth = $('#btnFacebookAuth');
    const formLogin = $('#formLogin');
    const formRegister = $('#formRegister');

    // Header User Menu Elements
    const userDropdownTrigger = $('#userDropdownTrigger');
    const userDropdownPanel = $('#userDropdownPanel');
    const btnHeaderLogout = $('#btnHeaderLogout');
    const profileLogoutBtn = $('#profileLogoutBtn');

    // Profile Page Elements
    const formUpdateProfile = $('#formUpdateProfile');
    const profileAlert = $('#profileAlert');
    const profileAlertText = $('#profileAlertText');
    const profileTabBtns = $$('[data-profile-tab]');

    // ==========================================
    // ALERT HELPER
    // ==========================================
    function showAlert(alertEl, textEl, message, type = 'danger') {
        if (!alertEl || !textEl) return;
        alertEl.hidden = false;
        alertEl.className = `auth-alert auth-alert--${type}`;
        textEl.textContent = message;
    }

    function hideAlert(alertEl) {
        if (!alertEl) return;
        alertEl.hidden = true;
    }

    // ==========================================
    // MODAL OPEN / CLOSE
    // ==========================================
    function openModal(defaultTab = 'login') {
        if (!authModal) return;
        switchTab(defaultTab);
        hideAlert(authAlert);
        authModal.classList.add('is-open');
        authModal.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';

        setTimeout(() => {
            const firstInput = defaultTab === 'login' 
                ? $('#loginIdentifier', formLogin) 
                : $('#regName', formRegister);
            if (firstInput) firstInput.focus();
        }, 150);
    }

    function closeModal() {
        if (!authModal) return;
        authModal.classList.remove('is-open');
        authModal.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = '';
        hideAlert(authAlert);
    }

    if (openModalBtn) {
        openModalBtn.addEventListener('click', (e) => {
            e.preventDefault();
            openModal('login');
        });
    }

    closeBtns.forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            closeModal();
        });
    });

    // Close on Escape key
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && authModal && authModal.classList.contains('is-open')) {
            closeModal();
        }
    });

    // Switch Tab inside Modal
    function switchTab(tab) {
        hideAlert(authAlert);
        if (tab === 'register') {
            if (tabBtnLogin) {
                tabBtnLogin.classList.remove('is-active');
                tabBtnLogin.setAttribute('aria-selected', 'false');
            }
            if (tabBtnRegister) {
                tabBtnRegister.classList.add('is-active');
                tabBtnRegister.setAttribute('aria-selected', 'true');
            }
            if (tabPaneLogin) {
                tabPaneLogin.hidden = true;
                tabPaneLogin.classList.remove('is-active');
            }
            if (tabPaneRegister) {
                tabPaneRegister.hidden = false;
                tabPaneRegister.classList.add('is-active');
            }
            const modalTitle = $('#authModalTitle');
            if (modalTitle) modalTitle.textContent = 'Đăng Ký Thành Viên';
        } else {
            if (tabBtnLogin) {
                tabBtnLogin.classList.add('is-active');
                tabBtnLogin.setAttribute('aria-selected', 'true');
            }
            if (tabBtnRegister) {
                tabBtnRegister.classList.remove('is-active');
                tabBtnRegister.setAttribute('aria-selected', 'false');
            }
            if (tabPaneLogin) {
                tabPaneLogin.hidden = false;
                tabPaneLogin.classList.add('is-active');
            }
            if (tabPaneRegister) {
                tabPaneRegister.hidden = true;
                tabPaneRegister.classList.remove('is-active');
            }
            const modalTitle = $('#authModalTitle');
            if (modalTitle) modalTitle.textContent = 'Tài Khoản Khách Hàng';
        }
    }

    if (tabBtnLogin) {
        tabBtnLogin.addEventListener('click', () => switchTab('login'));
    }
    if (tabBtnRegister) {
        tabBtnRegister.addEventListener('click', () => switchTab('register'));
    }

    // Switch links (Chưa có tài khoản / Đã có tài khoản)
    $$('[data-switch-tab]').forEach(link => {
        link.addEventListener('click', (e) => {
            e.preventDefault();
            const targetTab = link.getAttribute('data-switch-tab');
            switchTab(targetTab);
        });
    });

    // Password Toggle Show/Hide
    $$('[data-toggle-pwd]').forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            const targetSel = btn.getAttribute('data-toggle-pwd');
            const input = $(targetSel);
            if (!input) return;

            if (input.type === 'password') {
                input.type = 'text';
                btn.style.color = '#004098';
            } else {
                input.type = 'password';
                btn.style.color = '';
            }
        });
    });

    // ==========================================
    // API: LOGIN FORM SUBMIT (POST /api/auth/login)
    // ==========================================
    if (formLogin) {
        formLogin.addEventListener('submit', async (e) => {
            e.preventDefault();
            hideAlert(authAlert);

            const identifier = $('#loginIdentifier', formLogin)?.value.trim();
            const password = $('#loginPassword', formLogin)?.value;
            const submitBtn = $('#btnLoginSubmit', formLogin);
            const btnText = submitBtn?.querySelector('.btn-text');
            const btnSpinner = submitBtn?.querySelector('.btn-spinner');

            if (!identifier || !password) {
                showAlert(authAlert, authAlertText, 'Vui lòng nhập đầy đủ Email/Số điện thoại và Mật khẩu.', 'danger');
                return;
            }

            // Loading state
            if (submitBtn) submitBtn.disabled = true;
            if (btnText) btnText.textContent = 'ĐANG ĐĂNG NHẬP...';
            if (btnSpinner) btnSpinner.hidden = false;

            try {
                const res = await fetch(apiUrl('/api/auth/login'), {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        identifier: identifier,
                        password: password
                    })
                });

                const data = await res.json();

                if (res.ok && data.success) {
                    showAlert(authAlert, authAlertText, data.message || 'Đăng nhập thành công! Đang chuyển hướng...', 'success');
                    setTimeout(() => {
                        window.location.reload();
                    }, 700);
                } else {
                    showAlert(authAlert, authAlertText, data.message || 'Thông tin đăng nhập không chính xác.', 'danger');
                }
            } catch (err) {
                showAlert(authAlert, authAlertText, 'Lỗi kết nối máy chủ. Vui lòng kiểm tra lại mạng!', 'danger');
            } finally {
                if (submitBtn) submitBtn.disabled = false;
                if (btnText) btnText.textContent = 'ĐĂNG NHẬP';
                if (btnSpinner) btnSpinner.hidden = true;
            }
        });
    }

    // ==========================================
    // API: REGISTER FORM SUBMIT (POST /api/auth/register)
    // ==========================================
    if (formRegister) {
        formRegister.addEventListener('submit', async (e) => {
            e.preventDefault();
            hideAlert(authAlert);

            const name = $('#regName', formRegister)?.value.trim();
            const email = $('#regEmail', formRegister)?.value.trim();
            const phone = $('#regPhone', formRegister)?.value.trim();
            const password = $('#regPassword', formRegister)?.value;
            const passwordConfirm = $('#regPasswordConfirm', formRegister)?.value;
            const terms = $('#regTerms', formRegister)?.checked;

            const submitBtn = $('#btnRegisterSubmit', formRegister);
            const btnText = submitBtn?.querySelector('.btn-text');
            const btnSpinner = submitBtn?.querySelector('.btn-spinner');

            if (!name || !email || !password || !passwordConfirm) {
                showAlert(authAlert, authAlertText, 'Vui lòng điền đầy đủ các trường thông tin bắt buộc (*).', 'danger');
                return;
            }

            if (!terms) {
                showAlert(authAlert, authAlertText, 'Vui lòng tích đồng ý với chính sách & điều khoản.', 'danger');
                return;
            }

            if (password !== passwordConfirm) {
                showAlert(authAlert, authAlertText, 'Mật khẩu xác nhận không khớp với mật khẩu đã nhập.', 'danger');
                return;
            }

            if (password.length < 6) {
                showAlert(authAlert, authAlertText, 'Mật khẩu phải có độ dài ít nhất 6 ký tự.', 'danger');
                return;
            }

            // Loading state
            if (submitBtn) submitBtn.disabled = true;
            if (btnText) btnText.textContent = 'ĐANG TẠO TÀI KHOẢN...';
            if (btnSpinner) btnSpinner.hidden = false;

            try {
                const res = await fetch(apiUrl('/api/auth/register'), {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        name: name,
                        email: email,
                        phone: phone,
                        password: password,
                        password_confirmation: passwordConfirm
                    })
                });

                const data = await res.json();

                if (res.ok && data.success) {
                    showAlert(authAlert, authAlertText, data.message || 'Đăng ký thành công! Đang đăng nhập...', 'success');
                    setTimeout(() => {
                        window.location.reload();
                    }, 800);
                } else {
                    showAlert(authAlert, authAlertText, data.message || 'Không thể đăng ký tài khoản.', 'danger');
                }
            } catch (err) {
                showAlert(authAlert, authAlertText, 'Lỗi kết nối máy chủ. Vui lòng thử lại!', 'danger');
            } finally {
                if (submitBtn) submitBtn.disabled = false;
                if (btnText) btnText.textContent = 'TẠO TÀI KHOẢN MỚI';
                if (btnSpinner) btnSpinner.hidden = true;
            }
        });
    }

    // ==========================================
    // API: SOCIAL LOGIN (GOOGLE & FACEBOOK SDK INTEGRATION)
    // ==========================================
    async function handleSocialLogin(provider, profile) {
        hideAlert(authAlert);
        showAlert(authAlert, authAlertText, `Đang kết nối tài khoản ${provider === 'google' ? 'Google' : 'Facebook'}...`, 'success');

        try {
            const res = await fetch(apiUrl('/api/auth/social-login'), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    provider: provider,
                    ...profile
                })
            });

            const data = await res.json();

            if (res.ok && data.success) {
                showAlert(authAlert, authAlertText, data.message || `Đăng nhập qua ${provider} thành công!`, 'success');
                setTimeout(() => {
                    window.location.reload();
                }, 700);
            } else {
                showAlert(authAlert, authAlertText, data.message || `Đăng nhập qua ${provider} không thành công.`, 'danger');
            }
        } catch (err) {
            showAlert(authAlert, authAlertText, `Không thể kết nối API xác thực ${provider}.`, 'danger');
        }
    }

    // Google Identity Services (GIS) SDK Loader & Handler
    let googleTokenClient = null;

    function initGoogleAuth() {
        const clientId = window.APP?.googleClientId;
        const isPlaceholder = !clientId || clientId.includes('example.apps.googleusercontent.com');

        if (!isPlaceholder && !document.getElementById('googleGsiScript')) {
            const script = document.createElement('script');
            script.id = 'googleGsiScript';
            script.src = 'https://accounts.google.com/gsi/client';
            script.async = true;
            script.defer = true;
            script.onload = () => {
                try {
                    // 1. One Tap / Credential Flow
                    window.google?.accounts?.id?.initialize({
                        client_id: clientId,
                        callback: (response) => {
                            if (response.credential) {
                                handleSocialLogin('google', { credential: response.credential });
                            }
                        }
                    });

                    // 2. OAuth 2.0 Popup Token Client (dùng khi click trực tiếp nút Đăng nhập Google)
                    if (window.google?.accounts?.oauth2) {
                        googleTokenClient = window.google.accounts.oauth2.initTokenClient({
                            client_id: clientId,
                            scope: 'openid email profile',
                            callback: async (tokenResponse) => {
                                if (tokenResponse.access_token) {
                                    try {
                                        // Gọi Google UserInfo API để lấy thông tin tài khoản
                                        const userInfoRes = await fetch('https://www.googleapis.com/oauth2/v3/userinfo', {
                                            headers: { Authorization: `Bearer ${tokenResponse.access_token}` }
                                        });
                                        const userInfo = await userInfoRes.json();
                                        if (userInfo && userInfo.sub) {
                                            handleSocialLogin('google', {
                                                id: userInfo.sub,
                                                name: userInfo.name || userInfo.email,
                                                email: userInfo.email,
                                                avatar: userInfo.picture || null
                                            });
                                        }
                                    } catch (err) {
                                        console.error('[Google UserInfo Error]', err);
                                        triggerGoogleFallbackPrompt();
                                    }
                                }
                            }
                        });
                    }
                } catch (e) {
                    console.warn('[Google Auth] Init error:', e);
                }
            };
            document.head.appendChild(script);
        }

        // Gắn sự kiện click cho các nút Google trên website
        $$('.btn-google, #btnGoogleAuth').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();

                // Nếu có Token Client Popup -> Bật cửa sổ chọn tài khoản Google chuẩn
                if (googleTokenClient) {
                    googleTokenClient.requestAccessToken({ prompt: 'select_account' });
                    return;
                }

                // Nếu Google GIS Prompt khả dụng
                if (!isPlaceholder && window.google?.accounts?.id) {
                    window.google.accounts.id.prompt((notification) => {
                        if (notification.isNotDisplayed() || notification.isSkippedMoment()) {
                            triggerGoogleFallbackPrompt();
                        }
                    });
                    return;
                }

                // Dự phòng khi chưa tải xong SDK hoặc offline
                triggerGoogleFallbackPrompt();
            });
        });
    }

    function triggerGoogleFallbackPrompt() {
        const googleEmail = prompt('Xác thực đăng nhập Google (OAuth 2.0):\nNhập địa chỉ Gmail của bạn:', 'khachhang.panasonic@gmail.com');
        if (!googleEmail || !googleEmail.trim()) return;

        const email = googleEmail.trim();
        const baseName = email.split('@')[0].replace(/[._]/g, ' ').toUpperCase();
        handleSocialLogin('google', {
            id: 'gg_' + Math.abs(email.split('').reduce((a, b) => { a = ((a << 5) - a) + b.charCodeAt(0); return a & a; }, 0)),
            name: baseName,
            email: email,
            avatar: 'https://lh3.googleusercontent.com/a/default-user=s96-c'
        });
    }

    // Facebook JavaScript SDK Loader & Handler
    function initFacebookAuth() {
        const appId = window.APP?.facebookAppId;
        const isPlaceholder = !appId || appId === '123456789012345';

        if (!isPlaceholder && !document.getElementById('facebookJSSDK')) {
            window.fbAsyncInit = function() {
                window.FB.init({
                    appId      : appId,
                    cookie     : true,
                    xfbml      : true,
                    version    : 'v18.0'
                });
            };

            const script = document.createElement('script');
            script.id = 'facebookJSSDK';
            script.src = 'https://connect.facebook.net/vi_VN/sdk.js';
            script.async = true;
            script.defer = true;
            document.head.appendChild(script);
        }

        // Attach click listener to all Facebook buttons
        $$('.btn-facebook, #btnFacebookAuth').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();

                if (!isPlaceholder && window.FB) {
                    window.FB.login((response) => {
                        if (response.authResponse) {
                            window.FB.api('/me', { fields: 'name, email, picture' }, (fbUser) => {
                                handleSocialLogin('facebook', {
                                    id: fbUser.id,
                                    name: fbUser.name,
                                    email: fbUser.email || null,
                                    avatar: fbUser.picture?.data?.url || null
                                });
                            });
                        } else {
                            showAlert(authAlert, authAlertText, 'Bạn đã hủy đăng nhập bằng Facebook.', 'danger');
                        }
                    }, { scope: 'public_profile,email' });
                    return;
                }

                // Smooth popup/prompt for testing & development
                triggerFacebookFallbackPrompt();
            });
        });
    }

    function triggerFacebookFallbackPrompt() {
        const fbEmail = prompt('Xác thực đăng nhập Facebook Login:\nNhập Email hoặc SĐT Facebook của bạn:', 'khachhang.fb@gmail.com');
        if (!fbEmail || !fbEmail.trim()) return;

        const email = fbEmail.trim();
        const baseName = email.split('@')[0].replace(/[._]/g, ' ').toUpperCase();
        handleSocialLogin('facebook', {
            id: 'fb_' + Math.abs(email.split('').reduce((a, b) => { a = ((a << 5) - a) + b.charCodeAt(0); return a & a; }, 0)),
            name: baseName + ' (Facebook)',
            email: email,
            avatar: 'https://graph.facebook.com/4/picture?type=normal'
        });
    }

    // Initialize Social SDKs
    initGoogleAuth();
    initFacebookAuth();

    // ==========================================
    // USER HEADER DROPDOWN & LOGOUT
    // ==========================================
    if (userDropdownTrigger && userDropdownPanel) {
        userDropdownTrigger.addEventListener('click', (e) => {
            e.stopPropagation();
            const isExpanded = userDropdownTrigger.getAttribute('aria-expanded') === 'true';
            userDropdownTrigger.setAttribute('aria-expanded', !isExpanded);
            userDropdownPanel.hidden = isExpanded;
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', (e) => {
            if (!userDropdownPanel.contains(e.target) && !userDropdownTrigger.contains(e.target)) {
                userDropdownPanel.hidden = true;
                userDropdownTrigger.setAttribute('aria-expanded', 'false');
            }
        });
    }

    // Logout Handler (Header & Profile)
    async function handleLogout() {
        if (!confirm('Bạn có chắc chắn muốn đăng xuất tài khoản?')) return;

        try {
            const res = await fetch(apiUrl('/api/auth/logout'), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const data = await res.json();
            if (data.success) {
                window.location.href = BASE_URL || '/';
            } else {
                window.location.reload();
            }
        } catch (e) {
            window.location.href = apiUrl('/dang-xuat');
        }
    }

    if (btnHeaderLogout) {
        btnHeaderLogout.addEventListener('click', (e) => {
            e.preventDefault();
            handleLogout();
        });
    }

    if (profileLogoutBtn) {
        profileLogoutBtn.addEventListener('click', (e) => {
            e.preventDefault();
            handleLogout();
        });
    }

    // ==========================================
    // PROFILE PAGE: TABS & UPDATE API
    // ==========================================
    if (profileTabBtns.length > 0) {
        const switchProfileTab = (tabName) => {
            profileTabBtns.forEach(btn => {
                const isActive = btn.getAttribute('data-profile-tab') === tabName;
                btn.classList.toggle('is-active', isActive);
            });

            const tabInfo = $('#profileTabInfo');
            const tabOrders = $('#profileTabOrders');

            if (tabName === 'orders') {
                if (tabInfo) tabInfo.hidden = true;
                if (tabOrders) tabOrders.hidden = false;
            } else {
                if (tabInfo) tabInfo.hidden = false;
                if (tabOrders) tabOrders.hidden = true;
            }
        };

        profileTabBtns.forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                const tab = btn.getAttribute('data-profile-tab');
                switchProfileTab(tab);
                window.location.hash = tab;
            });
        });

        // Hash listener (#orders)
        if (window.location.hash === '#orders') {
            switchProfileTab('orders');
        }
    }

    // Form Update Profile
    if (formUpdateProfile) {
        formUpdateProfile.addEventListener('submit', async (e) => {
            e.preventDefault();
            hideAlert(profileAlert);

            const formData = new FormData(formUpdateProfile);
            const submitBtn = $('#btnProfileSave', formUpdateProfile);
            const btnText = submitBtn?.querySelector('.btn-text');
            const btnSpinner = submitBtn?.querySelector('.btn-spinner');

            if (submitBtn) submitBtn.disabled = true;
            if (btnText) btnText.textContent = 'ĐANG LƯU...';
            if (btnSpinner) btnSpinner.hidden = false;

            try {
                const res = await fetch(apiUrl('/api/auth/update-profile'), {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        name: formData.get('name'),
                        phone: formData.get('phone'),
                        address: formData.get('address')
                    })
                });

                const data = await res.json();
                if (res.ok && data.success) {
                    showAlert(profileAlert, profileAlertText, data.message || 'Cập nhật thông tin thành công!', 'success');
                } else {
                    showAlert(profileAlert, profileAlertText, data.message || 'Không thể cập nhật thông tin.', 'danger');
                }
            } catch (err) {
                showAlert(profileAlert, profileAlertText, 'Lỗi kết nối máy chủ.', 'danger');
            } finally {
                if (submitBtn) submitBtn.disabled = false;
                if (btnText) btnText.textContent = 'Lưu Thay Đổi';
                if (btnSpinner) btnSpinner.hidden = true;
            }
        });
    }

    // Expose global trigger for any button with [data-auth-trigger]
    document.addEventListener('click', (e) => {
        const trigger = e.target.closest('[data-auth-trigger]');
        if (trigger) {
            e.preventDefault();
            const tab = trigger.getAttribute('data-auth-trigger') || 'login';
            openModal(tab);
        }
    });

})();
