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

    // Cập nhật giao diện thanh tiến trình đơn hàng (Stepper) ngay lập tức
    function updateOrderStepperUI(newStatus) {
        const stepOrder = ['pending', 'confirmed', 'shipping', 'completed'];
        const newIdx = stepOrder.indexOf(newStatus);
        if (newIdx === -1) return;

        const checkSvg = '<svg class="icon " viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 6 9 17l-5-5"></path></svg>';

        // 1. Cập nhật các vòng tròn và nhãn bước
        document.querySelectorAll('.stepper-item').forEach(item => {
            const idx = parseInt(item.dataset.index, 10);
            const circle = item.querySelector('.stepper-circle');
            const label = item.querySelector('.stepper-label');
            if (!circle || !label) return;

            const isDone = idx < newIdx || (newStatus === 'completed' && idx <= newIdx);
            const isCurrent = idx === newIdx && newStatus !== 'completed';

            if (isDone) {
                circle.style.background = '#059669';
                circle.style.color = '#fff';
                circle.style.boxShadow = '0 2px 6px rgba(5,150,105,0.25)';
                circle.style.border = 'none';
                circle.innerHTML = checkSvg;
                label.style.color = '#059669';
                label.style.fontWeight = '700';
            } else if (isCurrent) {
                circle.style.background = '#004098';
                circle.style.color = '#fff';
                circle.style.boxShadow = '0 0 0 4px rgba(0,64,152,0.18)';
                circle.style.border = 'none';
                circle.innerHTML = (idx + 1).toString();
                label.style.color = '#004098';
                label.style.fontWeight = '700';
            } else {
                circle.style.background = '#f8fafc';
                circle.style.color = '#94a3b8';
                circle.style.boxShadow = 'none';
                circle.style.border = '1.5px solid #cbd5e1';
                circle.innerHTML = (idx + 1).toString();
                label.style.color = '#94a3b8';
                label.style.fontWeight = '500';
            }
        });

        // 2. Cập nhật đường kẻ nối giữa các bước
        document.querySelectorAll('.stepper-line').forEach(line => {
            const lineIdx = parseInt(line.dataset.line, 10);
            if (lineIdx < newIdx || (newStatus === 'completed')) {
                line.style.background = '#059669';
            } else {
                line.style.background = '#e2e8f0';
            }
        });

        // 3. Cập nhật badge trạng thái hiện tại
        const badge = document.getElementById('currentStatusBadge');
        if (badge) {
            const labelMap = {
                'pending': 'Chờ xác nhận',
                'confirmed': 'Đã xác nhận',
                'shipping': 'Đang giao hàng',
                'completed': 'Hoàn thành',
                'cancelled': 'Đã huỷ'
            };
            badge.textContent = labelMap[newStatus] || newStatus;
        }
    }

    // ========================================================
    // 1. AJAX DELETE & STATUS HANDLER
    // ========================================================
    document.addEventListener('submit', async (e) => {
        const form = e.target;
        const action = form.getAttribute('action') || '';

        const isOrderProcess = action.includes('/admin/don-hang/trang-thai/');
        const isDeleteAction = action.includes('/xoa/');
        const isStatusToggle = action.includes('/trang-thai/') && !isOrderProcess;

        if (!isDeleteAction && !isStatusToggle && !isOrderProcess) {
            return;
        }

        e.preventDefault();

        // Xử lý riêng cho tiến trình chuyển trạng thái đơn hàng
        if (isOrderProcess) {
            const submitBtn = form.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.textContent = 'Đang xử lý...';
            }
            const formData = new FormData(form);
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
                    showAdminToast(data.message || 'Đã cập nhật trạng thái đơn hàng!', 'success');
                    // Tự động cập nhật thanh tiến trình xử lý đơn hàng ngay lập tức
                    if (data.status) {
                        updateOrderStepperUI(data.status);
                    }
                    // Tự động tải lại trang sau 400ms để cập nhật danh mục form tiếp theo
                    setTimeout(() => {
                        window.location.reload();
                    }, 400);
                } else {
                    showAdminToast(data.message || 'Không thể cập nhật trạng thái.', 'error');
                    if (submitBtn) {
                        submitBtn.disabled = false;
                        submitBtn.textContent = 'Cập nhật';
                    }
                }
            } catch (err) {
                form.submit();
            }
            return;
        }

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
