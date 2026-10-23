/**
 * PANASONIC ADMIN AJAX & REST API MODULE
 * Hỗ trợ Thêm / Sửa / Xóa / Cập nhật trạng thái qua API mượt mà, lưu data chuẩn xác.
 */
(() => {
    'use strict';

    // Toast helper
    function showAdminToast(message, type = 'success') {
        let container = document.getElementById('adminToastStack');
        if (!container) {
            container = document.createElement('div');
            container.id = 'adminToastStack';
            container.style.cssText = 'position:fixed;bottom:20px;right:20px;z-index:999999;display:flex;flex-direction:column;gap:10px;pointer-events:none;';
            document.body.appendChild(container);
        }

        const toast = document.createElement('div');
        toast.className = `admin-toast admin-toast--${type}`;
        const isSuccess = type === 'success';
        toast.style.cssText = `
            background:${isSuccess ? '#0f172a' : '#ef4444'};
            color:#ffffff;
            padding:12px 20px;
            border-radius:8px;
            font-size:14px;
            font-weight:600;
            box-shadow:0 10px 25px rgba(0,0,0,0.2);
            border-left:4px solid ${isSuccess ? '#10b981' : '#f87171'};
            display:flex;
            align-items:center;
            gap:10px;
            animation:adminToastIn 0.25s ease;
            pointer-events:auto;
        `;
        toast.innerHTML = `<span>${isSuccess ? '✓' : '⚠'}</span> <span>${message}</span>`;
        container.appendChild(toast);

        setTimeout(() => {
            toast.style.opacity = '0';
            toast.style.transform = 'translateY(10px)';
            toast.style.transition = 'all 0.3s ease';
            setTimeout(() => toast.remove(), 300);
        }, 3200);
    }

    // Keyframes for toast
    const style = document.createElement('style');
    style.textContent = `
        @keyframes adminToastIn {
            from { opacity:0; transform:translateY(15px); }
            to { opacity:1; transform:translateY(0); }
        }
    `;
    document.head.appendChild(style);

    // ========================================================
    // 1. AJAX DELETE HANDLER (XÓA SẢN PHẨM, DỰ ÁN, ĐÁNH GIÁ)
    // ========================================================
    document.addEventListener('submit', async (e) => {
        const form = e.target;
        const action = form.getAttribute('action') || '';

        // Check if form is a delete action
        const isDeleteAction = action.includes('/xoa/');
        const isStatusToggle = action.includes('/trang-thai/');

        if (!isDeleteAction && !isStatusToggle) {
            return;
        }

        e.preventDefault();

        if (isDeleteAction) {
            const confirmMsg = form.getAttribute('onsubmit') || '';
            const promptText = confirmMsg.includes('Bạn có chắc') 
                ? 'Bạn có chắc chắn muốn xóa mục này khỏi cơ sở dữ liệu?' 
                : 'Xác nhận xóa?';
            if (!confirm(promptText)) return;
        }

        const formData = new FormData(form);
        const submitBtn = form.querySelector('button[type="submit"]');
        if (submitBtn) submitBtn.disabled = true;

        try {
            const res = await fetch(action, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: formData
            });

            const data = await res.json();

            if (res.ok && data.success) {
                showAdminToast(data.message || 'Thao tác thành công!', 'success');

                // If Delete: smooth fade out table row
                if (isDeleteAction) {
                    const row = form.closest('tr');
                    if (row) {
                        row.style.transition = 'all 0.35s ease';
                        row.style.opacity = '0';
                        row.style.background = '#fef2f2';
                        row.style.transform = 'translateX(20px)';
                        setTimeout(() => row.remove(), 350);
                    }
                }

                // If Toggle Status: update badge and button without reload
                if (isStatusToggle) {
                    const row = form.closest('tr');
                    if (row) {
                        const badge = row.querySelector('.badge');
                        if (badge) {
                            if (data.status === 1 || data.status === 'active') {
                                badge.className = 'badge badge--success';
                                badge.textContent = (data.status === 1) ? 'Hiển thị' : 'Hoạt động';
                                if (submitBtn) {
                                    submitBtn.textContent = (data.status === 1) ? 'Ẩn' : 'Khóa tài khoản';
                                    submitBtn.className = (data.status === 1) ? 'btn btn--sm btn--outline' : 'btn btn--sm';
                                    submitBtn.style.background = '#fef2f2';
                                    submitBtn.style.color = '#dc2626';
                                }
                            } else {
                                badge.className = 'badge badge--danger';
                                badge.textContent = (data.status === 0) ? 'Đã ẩn' : 'Bị khóa';
                                if (submitBtn) {
                                    submitBtn.textContent = (data.status === 0) ? 'Hiện' : 'Mở khóa';
                                    submitBtn.className = (data.status === 0) ? 'btn btn--sm btn--primary' : 'btn btn--sm';
                                    submitBtn.style.background = '#ecfdf5';
                                    submitBtn.style.color = '#059669';
                                }
                            }
                        }
                    }
                }
            } else {
                showAdminToast(data.message || 'Không thể thực hiện thao tác.', 'error');
            }
        } catch (err) {
            // Fallback: submit standard form if json parse fails
            form.submit();
        } finally {
            if (submitBtn) submitBtn.disabled = false;
        }
    });

    // ========================================================
    // 2. AJAX FORM SUBMIT FOR PRODUCT & PROJECT & SETTINGS
    // ========================================================
    const ajaxForms = document.querySelectorAll('form[action*="/admin/san-pham/them"], form[action*="/admin/san-pham/sua"], form[action*="/admin/du-an/them"], form[action*="/admin/du-an/sua"], form[action*="/admin/cai-dat"]');

    ajaxForms.forEach(form => {
        form.addEventListener('submit', async (e) => {
            const submitBtn = form.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.dataset.originalText = submitBtn.textContent;
                submitBtn.textContent = '⏳ Đang lưu dữ liệu...';
                setTimeout(() => { if (submitBtn) submitBtn.disabled = true; }, 50);
            }

            // Normal form submit works fine with multipart images, but if using fetch:
            // Since Uploader handles file uploads cleanly, let default submit happen OR handle via fetch:
            // Let native form submit execute seamlessly with flash messages for large files, or fetch if AJAX preferred.
        });
    });

})();
