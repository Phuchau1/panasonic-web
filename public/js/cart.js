/**
 * PANASONIC ELECTRIC PRO - ENTERPRISE CART & CHECKOUT
 * Tương tác giỏ hàng, Cascading Địa chỉ Tỉnh/Huyện/Xã, Sổ địa chỉ Shopee/TikTok style,
 * Hóa đơn VAT, Cổng thanh toán & Xác nhận chuyển khoản VietQR Napas
 */

(function () {
    'use strict';

    const getCsrfToken = () => window.APP?.csrf || document.querySelector('meta[name="csrf-token"]')?.content || '';

    function getBasePath() {
        if (window.APP && typeof window.APP.baseUrl === 'string' && window.APP.baseUrl !== '') {
            return window.APP.baseUrl.replace(/\/$/, '');
        }
        if (window.APP && typeof window.APP.base === 'string' && window.APP.base !== '') {
            return window.APP.base.replace(/\/$/, '');
        }
        if (window.BASE_URL && typeof window.BASE_URL === 'string') {
            return window.BASE_URL.replace(/\/$/, '');
        }
        const path = window.location.pathname;
        if (path.startsWith('/panasonic')) {
            return '/panasonic';
        }
        return '';
    }

    function apiUrl(path) {
        const base = getBasePath();
        const cleanPath = '/' + path.replace(/^\//, '');
        return base + cleanPath;
    }

    // -------------------------------------------------------------
    // 1. Thêm vào giỏ hàng (Add to Cart)
    // -------------------------------------------------------------
    document.addEventListener('click', async function (e) {
        const btn = e.target.closest('[data-add-cart]');
        if (!btn) return;

        e.preventDefault();
        const productId = btn.dataset.addCart;
        let qty = 1;

        if (btn.dataset.qtySource) {
            const input = document.querySelector(btn.dataset.qtySource);
            if (input) qty = parseInt(input.value, 10) || 1;
        } else if (btn.dataset.qty) {
            qty = parseInt(btn.dataset.qty, 10) || 1;
        }

        const originalText = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = 'Đang thêm...';

        try {
            const formData = new FormData();
            formData.append('_token', getCsrfToken());
            formData.append('product_id', productId);
            formData.append('qty', qty);

            const res = await fetch(apiUrl('/gio-hang/them'), {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-Token': getCsrfToken()
                }
            });

            let data;
            try {
                data = await res.json();
            } catch (jsonErr) {
                data = null;
            }

            if (data && data.success) {
                updateCartBadge(data.cart.count);
                if (window.showToast) window.showToast(data.message, 'success');
                // Tự động mở Drawer Giỏ hàng bên phải và render danh sách mới nhất
                if (typeof window.openCartDrawer === 'function') {
                    window.openCartDrawer(data.cart);
                }
            } else if (data) {
                if (window.showToast) window.showToast(data.message || 'Lỗi thêm vào giỏ', 'error');
            } else if (res.status === 419) {
                if (window.showToast) window.showToast('Phiên làm việc hết hạn, vui lòng tải lại trang.', 'warning');
            } else {
                if (window.showToast) window.showToast('Không thể kết nối máy chủ (' + res.status + ').', 'error');
            }
        } catch (err) {
            if (window.showToast) window.showToast('Không thể kết nối máy chủ: ' + (err.message || ''), 'error');
        } finally {
            btn.disabled = false;
            btn.innerHTML = originalText;
        }
    });

    // -------------------------------------------------------------
    // 2. Tăng giảm số lượng tại trang chi tiết (Detail Quantity)
    // -------------------------------------------------------------
    document.querySelectorAll('[data-quantity]').forEach(wrap => {
        const input = wrap.querySelector('.qty-input');
        if (!input) return;
        const min = parseInt(input.min, 10) || 1;
        const max = parseInt(input.max, 10) || 9999;

        wrap.querySelector('[data-qty-action="decrease"]')?.addEventListener('click', () => {
            let val = parseInt(input.value, 10) || 1;
            if (val > min) input.value = val - 1;
        });

        wrap.querySelector('[data-qty-action="increase"]')?.addEventListener('click', () => {
            let val = parseInt(input.value, 10) || 1;
            if (val < max) input.value = val + 1;
        });
    });

    // -------------------------------------------------------------
    // 3. Tương tác trong Giỏ Hàng (Cập nhật số lượng / Xóa item)
    // -------------------------------------------------------------
    document.querySelectorAll('[data-cart-qty]').forEach(control => {
        const productId = control.dataset.cartQty;
        const input = control.querySelector('.qty-input');
        const btnDec = control.querySelector('[data-action="dec"]');
        const btnInc = control.querySelector('[data-action="inc"]');

        btnDec?.addEventListener('click', () => {
            let val = parseInt(input.value, 10) || 1;
            if (val > 1) {
                input.value = val - 1;
                syncCart(productId, val - 1);
            }
        });

        btnInc?.addEventListener('click', () => {
            let val = parseInt(input.value, 10) || 1;
            input.value = val + 1;
            syncCart(productId, val + 1);
        });

        input?.addEventListener('change', () => {
            let val = parseInt(input.value, 10) || 1;
            if (val < 1) val = 1;
            input.value = val;
            syncCart(productId, val);
        });
    });

    // Xóa sản phẩm khỏi giỏ
    document.querySelectorAll('[data-cart-remove]').forEach(btn => {
        btn.addEventListener('click', async function () {
            if (!confirm('Bạn có chắc muốn xóa thiết bị này khỏi danh sách dự toán?')) return;
            const productId = btn.dataset.cartRemove;

            try {
                const formData = new FormData();
                formData.append('_token', getCsrfToken());
                formData.append('product_id', productId);

                const res = await fetch(apiUrl('/gio-hang/xoa'), {
                    method: 'POST',
                    body: formData,
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                const data = await res.json();

                if (data.success) {
                    const row = document.querySelector(`[data-cart-row="${productId}"]`);
                    row?.remove();
                    updateCartUi(data.cart);
                    if (window.showToast) window.showToast('Đã xóa thiết bị khỏi giỏ.', 'success');
                    if (data.cart.count === 0) location.reload();
                }
            } catch (err) {
                if (window.showToast) window.showToast('Lỗi cập nhật giỏ hàng', 'error');
            }
        });
    });

    // Hàm gọi API đồng bộ giỏ hàng
    async function syncCart(productId, qty) {
        try {
            const formData = new FormData();
            formData.append('_token', getCsrfToken());
            formData.append('product_id', productId);
            formData.append('qty', qty);

            const res = await fetch(apiUrl('/gio-hang/cap-nhat'), {
                method: 'POST',
                body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const data = await res.json();

            if (data.success) {
                updateCartUi(data.cart);
            }
        } catch (err) {
            console.error('Lỗi sync cart:', err);
        }
    }

    // Cập nhật DOM giỏ hàng
    function updateCartUi(cart) {
        updateCartBadge(cart.count);

        const subtotalEl = document.querySelector('[data-cart-subtotal]');
        const discountEl = document.querySelector('[data-cart-discount]');
        const totalEl = document.querySelector('[data-cart-total]');

        if (subtotalEl) subtotalEl.textContent = cart.subtotal_text;
        if (discountEl) discountEl.textContent = cart.discount > 0 ? `-${cart.discount_text}` : '0 đ';
        if (totalEl) totalEl.textContent = cart.total_text;

        // Cập nhật từng hàng
        cart.lines?.forEach(line => {
            const row = document.querySelector(`[data-cart-row="${line.id}"]`);
            if (row) {
                const unitEl = row.querySelector('[data-unit-price]');
                const totalLineEl = row.querySelector('[data-line-total]');
                if (unitEl) unitEl.textContent = line.unit_price_text;
                if (totalLineEl) totalLineEl.textContent = line.line_total_text;
            }
        });
    }

    function updateCartBadge(count) {
        document.querySelectorAll('[data-cart-count]').forEach(el => {
            el.textContent = count;
        });
    }

    // -------------------------------------------------------------
    // 4. API Tỉnh / Huyện / Xã Cascade
    // -------------------------------------------------------------
    function setupCascadeLocation(provinceEl, districtEl, wardEl) {
        if (!provinceEl || !districtEl || !wardEl) return;

        // Load danh sách Tỉnh/Thành
        async function loadProvinces() {
            try {
                const res = await fetch(apiUrl('/api/locations/provinces'));
                const provinces = await res.json();
                
                provinceEl.innerHTML = '<option value="">-- Chọn Tỉnh / Thành phố --</option>';
                provinces.forEach(p => {
                    const opt = document.createElement('option');
                    opt.value = p.name;
                    opt.textContent = p.name;
                    opt.dataset.code = p.code;
                    provinceEl.appendChild(opt);
                });
            } catch (err) {
                console.error('Lỗi tải tỉnh thành:', err);
            }
        }

        provinceEl.addEventListener('change', async function () {
            const selectedOpt = provinceEl.options[provinceEl.selectedIndex];
            const pCode = selectedOpt?.dataset.code;

            districtEl.innerHTML = '<option value="">-- Chọn Quận / Huyện --</option>';
            districtEl.disabled = true;
            wardEl.innerHTML = '<option value="">-- Chọn Phường / Xã --</option>';
            wardEl.disabled = true;

            if (!pCode) return;

            districtEl.disabled = false;
            districtEl.innerHTML = '<option value="">Đang tải Quận / Huyện...</option>';

            try {
                const res = await fetch(apiUrl(`/api/locations/districts?province_code=${pCode}`));
                const districts = await res.json();

                districtEl.innerHTML = '<option value="">-- Chọn Quận / Huyện --</option>';
                districts.forEach(d => {
                    const opt = document.createElement('option');
                    opt.value = d.name;
                    opt.textContent = d.name;
                    opt.dataset.code = d.code;
                    districtEl.appendChild(opt);
                });
            } catch (err) {
                districtEl.innerHTML = '<option value="">Lỗi tải dữ liệu</option>';
            }
        });

        districtEl.addEventListener('change', async function () {
            const selectedOpt = districtEl.options[districtEl.selectedIndex];
            const dCode = selectedOpt?.dataset.code;

            wardEl.innerHTML = '<option value="">-- Chọn Phường / Xã --</option>';
            wardEl.disabled = true;

            if (!dCode) return;

            wardEl.disabled = false;
            wardEl.innerHTML = '<option value="">Đang tải Phường / Xã...</option>';

            try {
                const res = await fetch(apiUrl(`/api/locations/wards?district_code=${dCode}`));
                const wards = await res.json();

                wardEl.innerHTML = '<option value="">-- Chọn Phường / Xã --</option>';
                wards.forEach(w => {
                    const opt = document.createElement('option');
                    opt.value = w.name;
                    opt.textContent = w.name;
                    opt.dataset.code = w.code;
                    wardEl.appendChild(opt);
                });
            } catch (err) {
                wardEl.innerHTML = '<option value="">Lỗi tải dữ liệu</option>';
            }
        });

        loadProvinces();
    }

    // Khởi tạo cascade cho form thủ công (nếu có trên trang)
    const manualProvince = document.getElementById('cascadeProvince');
    const manualDistrict = document.getElementById('cascadeDistrict');
    const manualWard = document.getElementById('cascadeWard');
    if (manualProvince && manualDistrict && manualWard) {
        setupCascadeLocation(manualProvince, manualDistrict, manualWard);
    }

    // Khởi tạo cascade cho modal thêm địa chỉ mới
    const newProvince = document.getElementById('newProvince');
    const newDistrict = document.getElementById('newDistrict');
    const newWard = document.getElementById('newWard');
    let modalCascadeInitialized = false;

    // -------------------------------------------------------------
    // 5. Quản lý Sổ địa chỉ (Shopee / TikTok Style Modal)
    // -------------------------------------------------------------
    const addressModal = document.getElementById('addressModal');
    const btnOpenAddressModal = document.getElementById('btnOpenAddressModal');
    const btnCloseAddressModal = document.getElementById('btnCloseAddressModal');
    const btnCancelModal = document.getElementById('btnCancelModal');
    const btnApplyAddressSelection = document.getElementById('btnApplyAddressSelection');

    if (btnOpenAddressModal && addressModal) {
        btnOpenAddressModal.addEventListener('click', () => {
            addressModal.style.display = 'flex';
            addressModal.setAttribute('aria-hidden', 'false');
            document.body.style.overflow = 'hidden';
        });

        const closeModal = () => {
            addressModal.style.display = 'none';
            addressModal.setAttribute('aria-hidden', 'true');
            document.body.style.overflow = '';
        };

        btnCloseAddressModal?.addEventListener('click', closeModal);
        btnCancelModal?.addEventListener('click', closeModal);

        addressModal.addEventListener('click', (e) => {
            if (e.target === addressModal) closeModal();
        });

        // Click trên address-item để chọn radio
        const container = document.getElementById('savedAddressListContainer');
        container?.addEventListener('click', (e) => {
            const item = e.target.closest('.addr-card-item, .address-item');
            if (!item || e.target.closest('.btn-text-action')) return;
            const radio = item.querySelector('input[type="radio"]');
            if (radio) {
                radio.checked = true;
                container.querySelectorAll('.addr-card-item, .address-item').forEach(el => el.classList.remove('is-selected'));
                item.classList.add('is-selected');
            }
        });

        // Nút Xác nhận chọn địa chỉ từ Modal
        btnApplyAddressSelection?.addEventListener('click', () => {
            const checkedRadio = container?.querySelector('input[name="modal_selected_address"]:checked');
            if (!checkedRadio) {
                if (window.showToast) window.showToast('Vui lòng chọn một địa chỉ giao hàng.', 'warning');
                return;
            }

            const name = checkedRadio.dataset.name;
            const phone = checkedRadio.dataset.phone;
            const province = checkedRadio.dataset.province;
            const district = checkedRadio.dataset.district;
            const ward = checkedRadio.dataset.ward;
            const detail = checkedRadio.dataset.detail;
            const label = checkedRadio.dataset.label;
            const isDefault = checkedRadio.dataset.default === '1';
            const addressId = checkedRadio.value;

            // Cập nhật giao diện bên ngoài
            const nameEl = document.getElementById('displayRecipientName');
            const phoneEl = document.getElementById('displayPhone');
            const labelEl = document.getElementById('displayLabel');
            const fullAddrEl = document.getElementById('displayFullAddress');
            const defaultBadgeEl = document.getElementById('displayDefaultBadge');

            if (nameEl) nameEl.textContent = name;
            if (phoneEl) phoneEl.textContent = phone;
            if (labelEl) labelEl.textContent = label;
            if (fullAddrEl) fullAddrEl.textContent = `${detail}, ${ward}, ${district}, ${province}`;

            if (defaultBadgeEl) {
                defaultBadgeEl.style.display = isDefault ? 'inline-block' : 'none';
            }

            // Cập nhật hidden inputs cho checkout form
            const hName = document.getElementById('hiddenCustomerName');
            const hPhone = document.getElementById('hiddenPhone');
            const hProv = document.getElementById('hiddenProvince');
            const hDist = document.getElementById('hiddenDistrict');
            const hWard = document.getElementById('hiddenWard');
            const hDetail = document.getElementById('hiddenDetailAddress');
            const hAddress = document.getElementById('hiddenAddress');
            const hId = document.getElementById('hiddenSelectedAddressId');

            if (hName) hName.value = name;
            if (hPhone) hPhone.value = phone;
            if (hProv) hProv.value = province;
            if (hDist) hDist.value = district;
            if (hWard) hWard.value = ward;
            if (hDetail) hDetail.value = detail;
            if (hAddress) hAddress.value = `${detail}, ${ward}, ${district}, ${province}`;
            if (hId) hId.value = addressId;

            closeModal();
            if (window.showToast) window.showToast('Đã áp dụng địa chỉ giao hàng thành công.', 'success');
        });

        // Thiết lập địa chỉ mặc định
        container?.addEventListener('click', async (e) => {
            const btnSetDef = e.target.closest('.btn-set-default');
            if (!btnSetDef) return;

            e.preventDefault();
            const addressId = btnSetDef.dataset.setDefault;
            btnSetDef.disabled = true;

            try {
                const formData = new FormData();
                formData.append('_token', getCsrfToken());
                formData.append('address_id', addressId);

                const res = await fetch(apiUrl('/api/user/addresses/set-default'), {
                    method: 'POST',
                    body: formData,
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                const data = await res.json();

                if (data.success) {
                    if (window.showToast) window.showToast(data.message, 'success');
                    reloadAddressListHtml(data.addresses);
                } else {
                    if (window.showToast) window.showToast(data.message || 'Lỗi thiết lập mặc định', 'error');
                }
            } catch (err) {
                if (window.showToast) window.showToast('Không thể kết nối máy chủ.', 'error');
            }
        });

        // Xóa địa chỉ
        container?.addEventListener('click', async (e) => {
            const btnDel = e.target.closest('.btn-delete-address');
            if (!btnDel) return;

            e.preventDefault();
            if (!confirm('Bạn có chắc chắn muốn xóa địa chỉ này khỏi sổ địa chỉ?')) return;
            const addressId = btnDel.dataset.deleteAddress;

            try {
                const formData = new FormData();
                formData.append('_token', getCsrfToken());
                formData.append('address_id', addressId);

                const res = await fetch(apiUrl('/api/user/addresses/delete'), {
                    method: 'POST',
                    body: formData,
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                const data = await res.json();

                if (data.success) {
                    if (window.showToast) window.showToast(data.message, 'success');
                    reloadAddressListHtml(data.addresses);
                } else {
                    if (window.showToast) window.showToast(data.message || 'Lỗi xóa địa chỉ', 'error');
                }
            } catch (err) {
                if (window.showToast) window.showToast('Không thể kết nối máy chủ.', 'error');
            }
        });

        // Mở/đóng form thêm địa chỉ mới trong modal
        const btnShowNew = document.getElementById('btnShowNewAddressForm');
        const btnCancelNew = document.getElementById('btnCancelNewAddress');
        const formBox = document.getElementById('newAddressFormBox');
        const wrapBtnShow = document.getElementById('wrapBtnShowNewAddress');

        btnShowNew?.addEventListener('click', () => {
            formBox.style.display = 'block';
            wrapBtnShow.style.display = 'none';

            if (!modalCascadeInitialized && newProvince && newDistrict && newWard) {
                setupCascadeLocation(newProvince, newDistrict, newWard);
                modalCascadeInitialized = true;
            }
        });

        btnCancelNew?.addEventListener('click', () => {
            formBox.style.display = 'none';
            wrapBtnShow.style.display = 'block';
        });

        // Pill chọn loại nhãn (Chân công trình, Văn phòng,...)
        document.querySelectorAll('.label-choice-group .label-pill').forEach(pill => {
            pill.addEventListener('click', () => {
                document.querySelectorAll('.label-choice-group .label-pill').forEach(p => p.classList.remove('active'));
                pill.classList.add('active');
            });
        });

        // Submit thêm địa chỉ mới qua AJAX
        const btnSubmitNew = document.getElementById('btnSubmitNewAddress');
        btnSubmitNew?.addEventListener('click', async () => {
            const name = document.getElementById('newRecipientName')?.value.trim();
            const phone = document.getElementById('newPhone')?.value.trim();
            const prov = document.getElementById('newProvince')?.value.trim();
            const dist = document.getElementById('newDistrict')?.value.trim();
            const ward = document.getElementById('newWard')?.value.trim();
            const detail = document.getElementById('newDetailAddress')?.value.trim();
            const labelInput = document.querySelector('input[name="new_label"]:checked');
            const label = labelInput ? labelInput.value : 'Chân công trình';
            const isDefault = document.getElementById('newIsDefault')?.checked ? 1 : 0;

            if (!name || !phone || !prov || !dist || !ward || !detail) {
                if (window.showToast) window.showToast('Vui lòng điền đầy đủ tất cả các trường có dấu *', 'warning');
                return;
            }

            btnSubmitNew.disabled = true;
            btnSubmitNew.innerText = 'Đang lưu...';

            try {
                const formData = new FormData();
                formData.append('_token', getCsrfToken());
                formData.append('recipient_name', name);
                formData.append('phone', phone);
                formData.append('province', prov);
                formData.append('district', dist);
                formData.append('ward', ward);
                formData.append('detail_address', detail);
                formData.append('label', label);
                formData.append('is_default', isDefault);

                const res = await fetch(apiUrl('/api/user/addresses/add'), {
                    method: 'POST',
                    body: formData,
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                const data = await res.json();

                if (data.success) {
                    if (window.showToast) window.showToast('Đã thêm địa chỉ vào sổ lưu trữ.', 'success');
                    
                    // Reset form
                    document.getElementById('newRecipientName').value = '';
                    document.getElementById('newPhone').value = '';
                    document.getElementById('newDetailAddress').value = '';
                    formBox.style.display = 'none';
                    wrapBtnShow.style.display = 'block';

                    // Cập nhật lại danh sách địa chỉ trong modal
                    reloadAddressListHtml(data.addresses);
                } else {
                    if (window.showToast) window.showToast(data.message || 'Lỗi thêm địa chỉ', 'error');
                }
            } catch (err) {
                if (window.showToast) window.showToast('Lỗi kết nối máy chủ.', 'error');
            } finally {
                btnSubmitNew.disabled = false;
                btnSubmitNew.innerText = 'Lưu địa chỉ này';
            }
        });
    }

    // Helper render lại danh sách địa chỉ trong Modal
    function reloadAddressListHtml(addresses) {
        const container = document.getElementById('savedAddressListContainer');
        if (!container || !Array.isArray(addresses)) return;

        let html = '';
        addresses.forEach(addr => {
            const isDef = !!addr.is_default;
            html += `
                <div class="addr-card-item ${isDef ? 'is-selected' : ''}" data-address-id="${addr.id}">
                    <label class="addr-card-label">
                        <input type="radio" name="modal_selected_address" value="${addr.id}"
                            ${isDef ? 'checked' : ''}
                            data-name="${escapeHtml(addr.recipient_name)}"
                            data-phone="${escapeHtml(addr.phone)}"
                            data-province="${escapeHtml(addr.province)}"
                            data-district="${escapeHtml(addr.district)}"
                            data-ward="${escapeHtml(addr.ward)}"
                            data-detail="${escapeHtml(addr.detail_address)}"
                            data-label="${escapeHtml(addr.label || 'Chân công trình')}"
                            data-default="${isDef ? 1 : 0}"
                        >
                        <div class="addr-card-details">
                            <div class="addr-user-line">
                                <strong class="user-name">${escapeHtml(addr.recipient_name)}</strong>
                                <span class="user-phone">${escapeHtml(addr.phone)}</span>
                            </div>
                            <div class="addr-full-string">
                                ${escapeHtml(addr.detail_address)}, ${escapeHtml(addr.ward)}, ${escapeHtml(addr.district)}, ${escapeHtml(addr.province)}
                            </div>
                            <div class="addr-pills-row">
                                <span class="addr-label-pill">${escapeHtml(addr.label || 'Chân công trình')}</span>
                                ${isDef ? '<span class="addr-default-pill">Mặc định</span>' : ''}
                            </div>
                        </div>
                    </label>
                    <div class="addr-card-actions">
                        ${!isDef ? `<button type="button" class="btn-text-action btn-set-default" data-set-default="${addr.id}">Đặt mặc định</button>` : ''}
                        <button type="button" class="btn-text-action text-danger btn-delete-address" data-delete-address="${addr.id}">Xóa</button>
                    </div>
                </div>
            `;
        });
        container.innerHTML = html;
    }

    function escapeHtml(str) {
        if (!str) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    // -------------------------------------------------------------
    // 6. VAT Invoice Toggle
    // -------------------------------------------------------------
    const vatToggle = document.getElementById('vatRequiredToggle');
    const vatCollapse = document.getElementById('vatFormCollapse');
    const vatCompany = document.getElementById('vatCompany');
    const vatTaxCode = document.getElementById('vatTaxCode');

    if (vatToggle && vatCollapse) {
        vatToggle.addEventListener('change', () => {
            if (vatToggle.checked) {
                vatCollapse.classList.add('is-open', 'show');
                if (vatCompany) vatCompany.required = true;
                if (vatTaxCode) vatTaxCode.required = true;
            } else {
                vatCollapse.classList.remove('is-open', 'show');
                if (vatCompany) vatCompany.required = false;
                if (vatTaxCode) vatTaxCode.required = false;
            }
        });
    }

    // -------------------------------------------------------------
    // 7. Payment Radio Selection Highlighting
    // -------------------------------------------------------------
    document.querySelectorAll('.pay-option input[type="radio"], .payment-card-option input[type="radio"]').forEach(radio => {
        radio.addEventListener('change', () => {
            document.querySelectorAll('.pay-option, .payment-card-option').forEach(card => card.classList.remove('is-active', 'selected'));
            radio.closest('.pay-option, .payment-card-option')?.classList.add('is-active', 'selected');
        });
    });

    // -------------------------------------------------------------
    // 8. Checkout Form AJAX Submit with Loading State
    // -------------------------------------------------------------
    const checkoutForm = document.getElementById('checkoutForm');
    const btnPlaceOrder = document.getElementById('btnPlaceOrder');

    if (checkoutForm && btnPlaceOrder) {
        checkoutForm.addEventListener('submit', async function (e) {
            e.preventDefault();

            // Hiệu ứng nút bấm
            const textSpan = btnPlaceOrder.querySelector('.btn-text');
            const loadingSpan = btnPlaceOrder.querySelector('.btn-loading-state');
            btnPlaceOrder.disabled = true;
            if (textSpan) textSpan.style.display = 'none';
            if (loadingSpan) loadingSpan.style.display = 'inline-flex';

            const formData = new FormData(checkoutForm);

            try {
                const res = await fetch(checkoutForm.action, {
                    method: 'POST',
                    body: formData,
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                const data = await res.json();

                if (data.success) {
                    if (window.showToast) window.showToast(data.message || 'Đặt hàng thành công!', 'success');
                    setTimeout(() => {
                        window.location.href = data.redirect_url;
                    }, 400);
                } else {
                    if (window.showToast) window.showToast(data.message || 'Không thể tạo đơn hàng.', 'error');
                    btnPlaceOrder.disabled = false;
                    if (textSpan) textSpan.style.display = 'inline-flex';
                    if (loadingSpan) loadingSpan.style.display = 'none';
                }
            } catch (err) {
                if (window.showToast) window.showToast('Không thể kết nối máy chủ.', 'error');
                btnPlaceOrder.disabled = false;
                if (textSpan) textSpan.style.display = 'inline-flex';
                if (loadingSpan) loadingSpan.style.display = 'none';
            }
        });
    }

    // -------------------------------------------------------------
    // 9. Xác Nhận Chuyển Khoản Thành Công (Trang Success)
    // -------------------------------------------------------------
    const btnConfirmTransfer = document.getElementById('btnConfirmTransfer');
    if (btnConfirmTransfer) {
        btnConfirmTransfer.addEventListener('click', async function () {
            const orderCode = btnConfirmTransfer.dataset.orderCode;
            if (!orderCode) return;

            btnConfirmTransfer.disabled = true;
            btnConfirmTransfer.innerText = 'Đang kiểm tra đối soát...';

            try {
                const formData = new FormData();
                formData.append('_token', getCsrfToken());
                formData.append('order_code', orderCode);

                const res = await fetch(apiUrl('/api/don-hang/xac-nhan-chuyen-khoan'), {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-Token': getCsrfToken()
                    }
                });

                let data;
                try {
                    data = await res.json();
                } catch (jsonErr) {
                    data = null;
                }

                if (data && data.success) {
                    if (window.showToast) {
                        window.showToast(data.message || 'Xác nhận thanh toán thành công!', 'success');
                    }

                    // 1. Cập nhật banner chính đầu trang
                    const bannerIcon = document.getElementById('successBadgeIcon');
                    if (bannerIcon) {
                        bannerIcon.style.background = '#ecfdf5';
                        bannerIcon.style.color = '#10b981';
                        bannerIcon.style.border = 'none';
                        bannerIcon.innerHTML = `<svg class="icon icon-check" aria-hidden="true" width="36" height="36"><use href="${(window.PANASONIC && window.PANASONIC.assetBase) || ''}/svg/icons.svg#icon-check"></use></svg>`;
                    }
                    const mainTitle = document.getElementById('successMainTitle');
                    if (mainTitle) {
                        mainTitle.style.color = '#0f172a';
                        mainTitle.textContent = 'Đặt Hàng Thành Công!';
                    }
                    const subText = document.getElementById('successSubText');
                    if (subText) {
                        subText.textContent = 'Thanh toán hoàn tất! Đơn hàng đã được duyệt và chuyển sang bộ phận kho chuẩn bị đóng hàng.';
                    }
                    const emailNotify = document.getElementById('orderEmailNotify');
                    if (emailNotify) {
                        emailNotify.style.background = '#ecfdf5';
                        emailNotify.style.borderColor = '#a7f3d0';
                        emailNotify.style.color = '#047857';
                    }

                    // 2. Cập nhật Stepper quy trình
                    const line1 = document.getElementById('stepperLine1');
                    if (line1) line1.className = 'stepper-line completed';

                    const stepPayment = document.getElementById('stepperStepPayment');
                    if (stepPayment) stepPayment.className = 'stepper-step completed';

                    const payIcon = document.getElementById('stepperPaymentIcon');
                    if (payIcon) payIcon.innerHTML = `<svg class="icon icon-check" aria-hidden="true" width="18" height="18"><use href="${(window.PANASONIC && window.PANASONIC.assetBase) || ''}/svg/icons.svg#icon-check"></use></svg>`;

                    const payTitle = document.getElementById('stepperPaymentTitle');
                    if (payTitle) payTitle.textContent = 'Đã thanh toán thành công';

                    const paySub = document.getElementById('stepPaymentSubtext');
                    if (paySub) paySub.textContent = 'Đã khớp giao dịch VietQR Napas 24/7';

                    const line2 = document.getElementById('stepperLine2');
                    if (line2) line2.className = 'stepper-line active';

                    const stepConfirm = document.getElementById('stepperStepConfirm');
                    if (stepConfirm) stepConfirm.className = 'stepper-step active';

                    const confirmSub = document.getElementById('stepperConfirmSub');
                    if (confirmSub) confirmSub.textContent = 'Đã xác nhận & gửi email thành công';

                    // 3. Cập nhật thẻ VietQR
                    const badge = document.getElementById('paymentStatusBadge');
                    if (badge) {
                        badge.className = 'payment-status-badge status-paid';
                        badge.textContent = '✓ ĐÃ THANH TOÁN THÀNH CÔNG';
                    }

                    const actionsBox = btnConfirmTransfer.closest('.vietqr-actions');
                    if (actionsBox) {
                        actionsBox.innerHTML = `
                            <div class="alert alert--success mb-0 text-center font-weight-600" style="padding: 14px; border-radius: 8px;">
                                <svg class="icon icon-check" aria-hidden="true" width="20" height="20" style="vertical-align: middle; margin-right: 6px;"><use href="${(window.PANASONIC && window.PANASONIC.assetBase) || ''}/svg/icons.svg#icon-check"></use></svg>
                                Đơn hàng đã được xác nhận thanh toán hoàn tất! Email xác nhận đã được gửi tự động.
                            </div>
                        `;
                    }
                } else if (data) {
                    if (window.showToast) window.showToast(data.message || 'Lỗi xác nhận thanh toán', 'error');
                    btnConfirmTransfer.disabled = false;
                    btnConfirmTransfer.innerText = 'Tôi Đã Chuyển Khoản Xong';
                } else if (res.status === 403) {
                    if (window.showToast) window.showToast('Phiên làm việc hết hạn, vui lòng tải lại trang.', 'warning');
                    btnConfirmTransfer.disabled = false;
                    btnConfirmTransfer.innerText = 'Tôi Đã Chuyển Khoản Xong';
                } else {
                    if (window.showToast) window.showToast('Lỗi máy chủ (' + res.status + '). Vui lòng thử lại.', 'error');
                    btnConfirmTransfer.disabled = false;
                    btnConfirmTransfer.innerText = 'Tôi Đã Chuyển Khoản Xong';
                }
            } catch (err) {
                if (window.showToast) window.showToast('Không thể kết nối máy chủ: ' + (err.message || ''), 'error');
                btnConfirmTransfer.disabled = false;
                btnConfirmTransfer.innerText = 'Tôi Đã Chuyển Khoản Xong';
            }
        });
    }

    // =============================================================
    // 7. OFFCANVAS CART DRAWER (THANH GIỎ HÀNG BÊN PHẢI)
    // =============================================================
    const cartDrawer = document.getElementById('cartDrawer');
    const cartDrawerBackdrop = document.getElementById('cartDrawerBackdrop');
    const closeCartDrawerBtn = document.getElementById('closeCartDrawerBtn');
    const headerCartBtn = document.querySelector('.corp-action-cart');

    function openCartDrawer(cartData) {
        if (!cartDrawer) return;
        cartDrawer.classList.add('is-open');
        if (cartDrawerBackdrop) cartDrawerBackdrop.classList.add('is-open');
        document.body.style.overflow = 'hidden';

        // Luôn fetch dữ liệu mới nhất đầy đủ hình ảnh, tên, model_code từ server
        fetchCartData();
    }

    function closeCartDrawer() {
        if (!cartDrawer) return;
        cartDrawer.classList.remove('is-open');
        if (cartDrawerBackdrop) cartDrawerBackdrop.classList.remove('is-open');
        document.body.style.overflow = '';
    }

    window.openCartDrawer = openCartDrawer;
    window.closeCartDrawer = closeCartDrawer;

    if (closeCartDrawerBtn) {
        closeCartDrawerBtn.addEventListener('click', closeCartDrawer);
    }
    if (cartDrawerBackdrop) {
        cartDrawerBackdrop.addEventListener('click', closeCartDrawer);
    }

    // Khi click icon giỏ hàng trên Header -> mở Drawer bên phải (thay vì nhảy trang)
    if (headerCartBtn) {
        headerCartBtn.addEventListener('click', function (e) {
            // Nếu đang ở trang checkout /gio-hang thì để người dùng ở lại trang đó
            if (window.location.pathname.endsWith('/gio-hang')) {
                return;
            }
            e.preventDefault();
            openCartDrawer();
        });
    }

    async function fetchCartData() {
        try {
            const res = await fetch(apiUrl('/api/gio-hang'), {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const data = await res.json();
            if (data && data.success) {
                renderDrawer(data);
                updateCartBadge(data.count);
            }
        } catch (e) {
            console.error('Fetch cart error', e);
        }
    }

    function renderDrawer(data) {
        const countBadge = document.getElementById('drawerCartBadge');
        const emptyBox = document.getElementById('drawerCartEmpty');
        const itemsBox = document.getElementById('drawerCartItems');
        const footerBox = document.getElementById('drawerCartFooter');
        const freeshipBar = document.querySelector('.cart-drawer__freeship-bar');
        const freeshipText = document.getElementById('drawerFreeshipText');
        const freeshipFill = document.getElementById('drawerFreeshipFill');

        const subtotalEl = document.getElementById('drawerSubtotal');
        const discountEl = document.getElementById('drawerDiscount');
        const discountRow = document.getElementById('drawerDiscountRow');
        const totalEl = document.getElementById('drawerTotal');

        const count = data.count || 0;
        if (countBadge) countBadge.textContent = count;

        // Tiến trình miễn phí vận chuyển
        const freeShipLimit = 2000000;
        const totalAmount = data.total || 0;
        const percent = Math.min(100, Math.round((totalAmount / freeShipLimit) * 100));

        if (freeshipFill) freeshipFill.style.width = percent + '%';
        if (freeshipText) {
            if (totalAmount >= freeShipLimit) {
                freeshipText.innerHTML = '🎉 Đơn hàng của bạn <strong>được miễn phí vận chuyển</strong>!';
            } else {
                const diff = freeShipLimit - totalAmount;
                freeshipText.innerHTML = `Mua thêm <strong>${(diff).toLocaleString('vi-VN')}₫</strong> để được Miễn phí giao hàng`;
            }
        }

        if (count === 0 || !data.lines || data.lines.length === 0) {
            if (emptyBox) emptyBox.style.display = 'flex';
            if (itemsBox) itemsBox.innerHTML = '';
            if (footerBox) footerBox.style.display = 'none';
            if (freeshipBar) freeshipBar.style.display = 'none';
            return;
        }

        if (emptyBox) emptyBox.style.display = 'none';
        if (footerBox) footerBox.style.display = 'block';
        if (freeshipBar) freeshipBar.style.display = 'block';

        if (subtotalEl) subtotalEl.textContent = data.subtotal_text || (data.subtotal || 0).toLocaleString('vi-VN') + '₫';
        if (totalEl) totalEl.textContent = data.total_text || (data.total || 0).toLocaleString('vi-VN') + '₫';

        if (data.discount && data.discount > 0) {
            if (discountRow) discountRow.style.display = 'flex';
            if (discountEl) discountEl.textContent = '-' + (data.discount_text || data.discount.toLocaleString('vi-VN') + '₫');
        } else {
            if (discountRow) discountRow.style.display = 'none';
        }

        if (itemsBox) {
            itemsBox.innerHTML = data.lines.map(item => `
                <div class="drawer-item" data-drawer-item="${item.id}">
                    <img src="${item.thumbnail || ''}" alt="${item.name}" class="drawer-item__img" onerror="this.src='${apiUrl('/images/no-image.png')}'">
                    <div class="drawer-item__info">
                        <a href="${item.url || '#'}" class="drawer-item__name">${item.name}</a>
                        <div class="drawer-item__code">Model: ${item.model_code || ''}</div>
                        <div class="drawer-item__bottom">
                            <span class="drawer-item__price">${item.unit_price_text || item.unit_price.toLocaleString('vi-VN') + '₫'}</span>
                            <div class="drawer-item__qty-ctrl">
                                <button type="button" class="drawer-qty-btn" data-drawer-qty-change="${item.id}" data-change="-1">-</button>
                                <input type="text" class="drawer-qty-val" value="${item.qty}" readonly>
                                <button type="button" class="drawer-qty-btn" data-drawer-qty-change="${item.id}" data-change="1">+</button>
                            </div>
                            <button type="button" class="drawer-item__remove" data-drawer-remove="${item.id}" title="Xóa khỏi giỏ">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="3 6 5 6 21 6"></polyline>
                                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            `).join('');

            // Gắn sự kiện tăng giảm số lượng & xóa trong Drawer
            itemsBox.querySelectorAll('[data-drawer-qty-change]').forEach(btn => {
                btn.addEventListener('click', async function () {
                    const id = this.getAttribute('data-drawer-qty-change');
                    const change = parseInt(this.getAttribute('data-change'), 10);
                    const item = data.lines.find(l => l.id == id);
                    if (!item) return;

                    const newQty = item.qty + change;
                    if (newQty <= 0) {
                        removeItemFromDrawer(id);
                    } else {
                        updateItemInDrawer(id, newQty);
                    }
                });
            });

            itemsBox.querySelectorAll('[data-drawer-remove]').forEach(btn => {
                btn.addEventListener('click', function () {
                    const id = this.getAttribute('data-drawer-remove');
                    removeItemFromDrawer(id);
                });
            });
        }
    }

    async function updateItemInDrawer(id, qty) {
        try {
            const formData = new FormData();
            formData.append('_token', getCsrfToken());
            formData.append('product_id', id);
            formData.append('qty', qty);

            const res = await fetch(apiUrl('/gio-hang/cap-nhat'), {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-Token': getCsrfToken()
                }
            });
            const data = await res.json();
            if (data && data.success) {
                fetchCartData();
            }
        } catch (e) {
            console.error('Update drawer error', e);
        }
    }

    async function removeItemFromDrawer(id) {
        try {
            const formData = new FormData();
            formData.append('_token', getCsrfToken());
            formData.append('product_id', id);

            const res = await fetch(apiUrl('/gio-hang/xoa'), {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-Token': getCsrfToken()
                }
            });
            const data = await res.json();
            if (data && data.success) {
                fetchCartData();
                if (window.showToast) window.showToast('Đã xóa sản phẩm khỏi giỏ hàng', 'info');
            }
        } catch (e) {
            console.error('Remove drawer error', e);
        }
    }

})();
