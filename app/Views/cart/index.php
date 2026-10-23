<?php
/** @var array $summary Kết quả từ Cart::summarize() */
/** @var array|null $user */
/** @var array $savedAddresses */
/** @var array|null $defaultAddress */
$lines = $summary['lines'] ?? [];
$isLoggedIn = !empty($user);
?>

<div class="cart-page-wrap">
    <!-- Header đường dẫn trang -->
    <div class="cart-topbar-nav">
        <div class="container">
            <nav class="breadcrumb-minimal" aria-label="Đường dẫn trang">
                <a href="<?= url('/') ?>"><?= icon('home') ?> Trang chủ</a>
                <span class="sep">/</span>
                <span class="current">Giỏ hàng & Dự toán</span>
            </nav>
            <div class="cart-title-row">
                <h1 class="cart-headline">Giỏ Hàng & Bảng Dự Toán Vật Tư</h1>
                <span class="cart-badge-count"><?= (int)$summary['count'] ?> sản phẩm</span>
            </div>
        </div>
    </div>

    <div class="container section-cart-body">
        <?php if (empty($lines)): ?>
            <div class="empty-state-card">
                <div class="empty-state-card__icon"><?= icon('cart') ?></div>
                <h2 class="empty-state-card__title">Giỏ hàng của bạn đang trống</h2>
                <p class="empty-state-card__desc">Hãy thêm các thiết bị điện Panasonic chính hãng vào giỏ để nhận chiết khấu khối lượng công trình.</p>
                <div class="empty-state-card__actions">
                    <a href="<?= url('/san-pham') ?>" class="btn-prime"><?= icon('grid') ?> Xem danh mục thiết bị</a>
                    <a href="<?= url('/bao-gia-cong-trinh') ?>" class="btn-subtle"><?= icon('upload') ?> Gửi bảng bóc tách file Excel</a>
                </div>
            </div>
        <?php else: ?>
            <form id="checkoutForm" action="<?= url('/gio-hang/dat-hang') ?>" method="post" class="cart-checkout-form">
                <?= csrf_field() ?>

                <div class="cart-grid-container">
                    
                    <!-- CỘT TRÁI: ĐỊA CHỈ -> SẢN PHẨM -> THANH TOÁN -> VAT -->
                    <div class="cart-left-col">

                        <!-- 1. ĐỊA CHỈ NHẬN HÀNG (ĐẶT Ở TRÊN ĐẦU CHUẨN SHOPEE MALL / TIKTOK SHOP) -->
                        <div class="p-card p-card--address" id="shippingAddressCard">
                            <div class="p-card-accent-line"></div>
                            <div class="p-card__body">
                                <div class="address-top-bar">
                                    <div class="address-top-label">
                                        <span class="loc-pin-icon"><?= icon('map-pin') ?></span>
                                        <span class="loc-pin-text">Địa chỉ nhận hàng / Chân công trình</span>
                                    </div>
                                    <?php if ($isLoggedIn && !empty($savedAddresses)): ?>
                                        <button type="button" class="btn-change-addr" id="btnOpenAddressModal">
                                            <?= icon('edit') ?> Thay đổi
                                        </button>
                                    <?php endif; ?>
                                </div>

                                <?php if ($isLoggedIn && !empty($savedAddresses)): ?>
                                    <?php $activeAddr = $defaultAddress ?: $savedAddresses[0]; ?>
                                    <!-- Địa chỉ đang chọn -->
                                    <div class="selected-addr-box" id="selectedAddressView">
                                        <div class="selected-addr-meta">
                                            <span class="addr-name" id="displayRecipientName"><?= e($activeAddr['recipient_name']) ?></span>
                                            <span class="addr-divider">•</span>
                                            <span class="addr-phone" id="displayPhone"><?= e($activeAddr['phone']) ?></span>
                                            <span class="addr-label-pill" id="displayLabel"><?= e($activeAddr['label'] ?: 'Chân công trình') ?></span>
                                            <span class="addr-default-pill" id="displayDefaultBadge" style="<?= empty($activeAddr['is_default']) ? 'display:none;' : '' ?>">Mặc định</span>
                                        </div>
                                        <div class="selected-addr-text" id="displayFullAddress">
                                            <?= e($activeAddr['detail_address']) ?>, <?= e($activeAddr['ward']) ?>, <?= e($activeAddr['district']) ?>, <?= e($activeAddr['province']) ?>
                                        </div>
                                        <div class="selected-addr-email mt-1 font-xs text-muted" style="display: flex; align-items: center; gap: 4px; font-size: 12px; color: #64748b; margin-top: 5px;">
                                            <?= icon('mail') ?> Email nhận thông báo: <strong style="color: #0f172a;"><?= e($user['email'] ?? 'Chưa cập nhật') ?></strong>
                                        </div>
                                    </div>

                                    <!-- Hidden Inputs cho checkout -->
                                    <input type="hidden" name="customer_name" id="hiddenCustomerName" value="<?= e($activeAddr['recipient_name']) ?>">
                                    <input type="hidden" name="phone" id="hiddenPhone" value="<?= e($activeAddr['phone']) ?>">
                                    <input type="hidden" name="email" id="hiddenCustomerEmail" value="<?= e($user['email'] ?? '') ?>">
                                    <input type="hidden" name="province" id="hiddenProvince" value="<?= e($activeAddr['province']) ?>">
                                    <input type="hidden" name="district" id="hiddenDistrict" value="<?= e($activeAddr['district']) ?>">
                                    <input type="hidden" name="ward" id="hiddenWard" value="<?= e($activeAddr['ward']) ?>">
                                    <input type="hidden" name="detail_address" id="hiddenDetailAddress" value="<?= e($activeAddr['detail_address']) ?>">
                                    <input type="hidden" name="address" id="hiddenAddress" value="<?= e($activeAddr['detail_address'] . ', ' . $activeAddr['ward'] . ', ' . $activeAddr['district'] . ', ' . $activeAddr['province']) ?>">
                                    <input type="hidden" name="selected_address_id" id="hiddenSelectedAddressId" value="<?= (int)$activeAddr['id'] ?>">

                                <?php else: ?>
                                    <!-- Khách vãng lai hoặc chưa có địa chỉ: Form Cascade 3 cấp tinh gọn -->
                                    <?php if (!$isLoggedIn): ?>
                                        <div class="login-tip-strip">
                                            <span><?= icon('user') ?> Đã có tài khoản đối tác Panasonic?</span>
                                            <a href="javascript:void(0)" onclick="window.openLoginModal ? window.openLoginModal() : location.href='<?= url('/dang-nhap') ?>'" class="login-tip-link">Đăng nhập để chọn sổ địa chỉ lưu sẵn</a>
                                        </div>
                                    <?php endif; ?>

                                    <div class="addr-form-grid">
                                        <div class="form-field">
                                            <label class="form-field__label">Họ và tên người nhận <span class="req">*</span></label>
                                            <input type="text" name="customer_name" id="manualCustomerName" class="form-field__input" placeholder="Họ tên người nhận" required value="<?= old('customer_name', $user['name'] ?? '') ?>">
                                        </div>
                                        <div class="form-field">
                                            <label class="form-field__label">Số điện thoại nhận hàng <span class="req">*</span></label>
                                            <input type="tel" name="phone" id="manualPhone" class="form-field__input" placeholder="Số điện thoại di động" required value="<?= old('phone', $user['phone'] ?? '') ?>">
                                        </div>
                                    </div>

                                    <div class="form-field mt-2">
                                        <label class="form-field__label">Email nhận thông báo đơn hàng &amp; bảo hành (Gmail) <span class="req">*</span></label>
                                        <input type="email" name="email" id="manualEmail" class="form-field__input" placeholder="VD: nguyenvanan@gmail.com" required value="<?= old('email', $user['email'] ?? '') ?>">
                                    </div>

                                    <div class="addr-location-grid">
                                        <div class="form-field">
                                            <label class="form-field__label">Tỉnh / Thành phố <span class="req">*</span></label>
                                            <select name="province" id="cascadeProvince" class="form-field__select" required>
                                                <option value="">-- Chọn Tỉnh / Thành --</option>
                                            </select>
                                        </div>
                                        <div class="form-field">
                                            <label class="form-field__label">Quận / Huyện <span class="req">*</span></label>
                                            <select name="district" id="cascadeDistrict" class="form-field__select" required disabled>
                                                <option value="">-- Chọn Quận / Huyện --</option>
                                            </select>
                                        </div>
                                        <div class="form-field">
                                            <label class="form-field__label">Phường / Xã <span class="req">*</span></label>
                                            <select name="ward" id="cascadeWard" class="form-field__select" required disabled>
                                                <option value="">-- Chọn Phường / Xã --</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-field mt-2">
                                        <label class="form-field__label">Địa chỉ chi tiết (Số nhà, tên đường, tên dự án/tòa nhà) <span class="req">*</span></label>
                                        <input type="text" name="detail_address" id="manualDetailAddress" class="form-field__input" placeholder="Ví dụ: Lô B2 KCN Tân Thuận, P. Tân Thuận Đông" required value="<?= old('detail_address') ?>">
                                    </div>

                                    <?php if ($isLoggedIn): ?>
                                        <label class="save-addr-checkbox">
                                            <input type="checkbox" name="save_as_address" value="1" checked>
                                            <span>Lưu vào Sổ địa chỉ để dùng cho các dự án sau</span>
                                        </label>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- 2. DANH SÁCH THIẾT BỊ TRONG GIỎ (BẢNG TINH GỌN, CHUYÊN NGHIỆP) -->
                        <div class="p-card">
                            <div class="p-card__header">
                                <div class="p-card__title">
                                    <span class="icon-wrap"><?= icon('cart') ?></span>
                                    <span>Thiết bị đặt mua (<span data-cart-count><?= (int)$summary['count'] ?></span>)</span>
                                </div>
                                <a href="<?= url('/san-pham') ?>" class="p-card__action-link">
                                    <?= icon('plus') ?> Thêm thiết bị khác
                                </a>
                            </div>

                            <div class="p-table-responsive">
                                <table class="cart-items-table">
                                    <thead>
                                        <tr>
                                            <th class="col-product">Sản phẩm</th>
                                            <th class="col-price">Đơn giá</th>
                                            <th class="col-qty">Số lượng</th>
                                            <th class="col-total">Thành tiền</th>
                                            <th class="col-del"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($lines as $line): ?>
                                            <tr data-cart-row="<?= (int)$line['id'] ?>">
                                                <td class="col-product">
                                                    <div class="item-cell">
                                                        <a href="<?= url('/san-pham/' . $line['slug']) ?>" class="item-thumb">
                                                            <img src="<?= media($line['thumbnail']) ?>" alt="<?= e($line['name']) ?>" width="56" height="56" loading="lazy">
                                                        </a>
                                                        <div class="item-info">
                                                            <span class="item-code"><?= e($line['model_code']) ?></span>
                                                            <a href="<?= url('/san-pham/' . $line['slug']) ?>" class="item-name"><?= e($line['name']) ?></a>
                                                            <?php if ($line['price_type'] === 'wholesale'): ?>
                                                                <span class="item-price-tag tag-success"><?= icon('tag') ?> Giá công trình (≥ <?= (int)$line['wholesale_min_qty'] ?> <?= e($line['unit']) ?>)</span>
                                                            <?php elseif ($line['need_more'] > 0): ?>
                                                                <span class="item-price-tag tag-muted">Mua thêm <strong><?= (int)$line['need_more'] ?></strong> để nhận giá sỉ: <?= money($line['wholesale_price']) ?></span>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="col-price">
                                                    <div class="price-cell">
                                                        <span class="price-val" data-unit-price><?= money($line['unit_price']) ?></span>
                                                        <?php if ($line['price_type'] === 'wholesale'): ?>
                                                            <del class="price-retail-del"><?= money($line['retail_price']) ?></del>
                                                        <?php endif; ?>
                                                    </div>
                                                </td>
                                                <td class="col-qty">
                                                    <div class="qty-stepper" data-cart-qty="<?= (int)$line['id'] ?>">
                                                        <button type="button" class="qty-btn" data-action="dec" aria-label="Giảm"><?= icon('minus') ?></button>
                                                        <input type="number" value="<?= (int)$line['qty'] ?>" min="1" max="<?= (int)$line['stock'] ?>" class="qty-input" aria-label="Số lượng">
                                                        <button type="button" class="qty-btn" data-action="inc" aria-label="Tăng"><?= icon('plus') ?></button>
                                                    </div>
                                                </td>
                                                <td class="col-total">
                                                    <span class="total-val" data-line-total><?= money($line['line_total']) ?></span>
                                                </td>
                                                <td class="col-del">
                                                    <button type="button" class="btn-del-item" data-cart-remove="<?= (int)$line['id'] ?>" title="Xóa thiết bị này"><?= icon('trash') ?></button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- 3. PHƯƠNG THỨC THANH TOÁN (THIẾT KẾ TINH XẢO, GỌN GÀNG) -->
                        <div class="p-card">
                            <div class="p-card__header">
                                <div class="p-card__title">
                                    <span class="icon-wrap"><?= icon('shield') ?></span>
                                    <span>Phương thức thanh toán</span>
                                </div>
                                <span class="p-card__badge-secure">Bảo mật SSL 256-bit</span>
                            </div>

                            <div class="p-card__body">
                                <div class="payment-method-list">
                                    
                                    <!-- 1. VietQR Napas 24/7 (ACB 37051817) -->
                                    <label class="pay-option is-active">
                                        <input type="radio" name="payment_method" value="vietqr" checked>
                                        <div class="pay-option__body">
                                            <div class="pay-option__title-row">
                                                <div class="pay-option__name">
                                                    <strong>Chuyển khoản VietQR Siêu Tốc (Napas 24/7)</strong>
                                                    <span class="tag-recommend">Khuyên dùng</span>
                                                </div>
                                                <span class="bank-pill">ACB • 37051817</span>
                                            </div>
                                            <p class="pay-option__desc">Quét mã VietQR Napas 24/7 tự động điền STK ACB 37051817, đúng số tiền và nội dung đơn hàng để khớp lệnh ngay.</p>
                                        </div>
                                    </label>

                                    <!-- 2. COD -->
                                    <label class="pay-option">
                                        <input type="radio" name="payment_method" value="cod">
                                        <div class="pay-option__body">
                                            <div class="pay-option__title-row">
                                                <div class="pay-option__name">
                                                    <strong>Thanh toán tiền mặt khi nhận hàng (COD)</strong>
                                                </div>
                                                <span class="bank-pill pill-neutral">GIAO TẬN NƠI</span>
                                            </div>
                                            <p class="pay-option__desc">Kiểm tra niêm phong, kiểm đếm số lượng và nghiệm thu CO/CQ tại chân công trình trước khi thanh toán.</p>
                                        </div>
                                    </label>

                                    <!-- 3. Ví MoMo Test -->
                                    <label class="pay-option">
                                        <input type="radio" name="payment_method" value="momo">
                                        <div class="pay-option__body">
                                            <div class="pay-option__title-row">
                                                <div class="pay-option__name">
                                                    <strong>Ví Điện Tử MoMo (Sandbox Test)</strong>
                                                    <span class="tag-test">Test Sandbox</span>
                                                </div>
                                                <span class="bank-pill pill-momo">MOMO QR • TEST</span>
                                            </div>
                                            <p class="pay-option__desc">Thử nghiệm thanh toán quét mã MoMo QR hoặc ứng dụng MoMo (Môi trường Sandbox thử nghiệm).</p>
                                        </div>
                                    </label>

                                    <!-- 4. VNPAY Test -->
                                    <label class="pay-option">
                                        <input type="radio" name="payment_method" value="vnpay">
                                        <div class="pay-option__body">
                                            <div class="pay-option__title-row">
                                                <div class="pay-option__name">
                                                    <strong>Cổng Thanh Toán VNPAY (Sandbox Test)</strong>
                                                    <span class="tag-test">Test Sandbox</span>
                                                </div>
                                                <span class="bank-pill pill-vnpay">VNPAY-QR • TEST</span>
                                            </div>
                                            <p class="pay-option__desc">Thử nghiệm cổng thanh toán VNPAY Test – Quét mã VNPAY-QR hoặc thẻ ATM ngân hàng nội địa test.</p>
                                        </div>
                                    </label>

                                    <!-- 5. B2B Debt -->
                                    <label class="pay-option">
                                        <input type="radio" name="payment_method" value="b2b_debt">
                                        <div class="pay-option__body">
                                            <div class="pay-option__title-row">
                                                <div class="pay-option__name">
                                                    <strong>Bảo lãnh công nợ B2B / Đại lý nhà thầu</strong>
                                                </div>
                                                <span class="bank-pill pill-b2b">HỢP ĐỒNG KHUNG</span>
                                            </div>
                                            <p class="pay-option__desc">Dành cho Nhà thầu & Doanh nghiệp đã ký Hợp đồng nguyên tắc, bảo lãnh đối soát công nợ 30-45 ngày.</p>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- 4. HÓA ĐƠN VAT & GHI CHÚ GIAO HÀNG -->
                        <div class="p-card">
                            <div class="p-card__body">
                                <label class="vat-switch-label">
                                    <input type="checkbox" name="vat_required" id="vatRequiredToggle" value="1" <?= old('vat_required') ? 'checked' : '' ?>>
                                    <span class="vat-custom-check"></span>
                                    <div class="vat-switch-text">
                                        <strong><?= icon('file-text') ?> Yêu cầu xuất Hóa đơn Giá trị gia tăng (VAT 10%)</strong>
                                        <span class="vat-hint">Hóa đơn điện tử hợp lệ theo quy định Tổng Cục Thuế sẽ gửi qua email khi bàn giao hàng.</span>
                                    </div>
                                </label>

                                <div class="vat-collapse-box <?= old('vat_required') ? 'is-open' : '' ?>" id="vatFormCollapse">
                                    <div class="vat-input-grid">
                                        <div class="form-field field-span-2">
                                            <label class="form-field__label">Tên công ty / Đơn vị thụ hưởng (theo ĐKKD) <span class="req">*</span></label>
                                            <input type="text" name="company" id="vatCompany" class="form-field__input" placeholder="VD: Công ty Cổ phần Xây dựng & Cơ điện ABC" value="<?= old('company', $user['company'] ?? '') ?>">
                                        </div>
                                        <div class="form-field">
                                            <label class="form-field__label">Mã số thuế (MST) <span class="req">*</span></label>
                                            <input type="text" name="tax_code" id="vatTaxCode" class="form-field__input" placeholder="VD: 0312345678" value="<?= old('tax_code', $user['tax_code'] ?? '') ?>">
                                        </div>
                                        <div class="form-field">
                                            <label class="form-field__label">Email nhận hóa đơn điện tử <span class="req">*</span></label>
                                            <input type="email" name="vat_email" id="vatEmail" class="form-field__input" placeholder="ketoan@doanhnghiep.vn" value="<?= old('vat_email', $user['email'] ?? '') ?>">
                                        </div>
                                    </div>
                                </div>

                                <div class="order-note-block">
                                    <label class="form-field__label"><?= icon('edit') ?> Ghi chú giao hàng hoặc yêu cầu đặc thù</label>
                                    <input type="text" name="note" class="form-field__input" placeholder="VD: Giao giờ hành chính, gọi thủ kho trước 30 phút, xe tải 2.5T vào được...">
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- CỘT PHẢI: TỔNG KẾT DỰ TOÁN THANH TOÁN (STICKY SIDEBAR) -->
                    <div class="cart-right-col">
                        <div class="summary-card">
                            <div class="summary-card__head">
                                <h3><?= icon('calculator') ?> Chi tiết thanh toán</h3>
                            </div>

                            <div class="summary-card__body">
                                <div class="fee-row">
                                    <span class="fee-label">Tiền hàng niêm yết:</span>
                                    <span class="fee-val" data-cart-subtotal><?= money($summary['subtotal']) ?></span>
                                </div>

                                <div class="fee-row text-success">
                                    <span class="fee-label">Chiết khấu công trình:</span>
                                    <span class="fee-val" data-cart-discount><?= $summary['discount'] > 0 ? '-' . money($summary['discount']) : '0 đ' ?></span>
                                </div>

                                <div class="fee-row">
                                    <span class="fee-label">Phí vận chuyển:</span>
                                    <span class="fee-val fee-free">Miễn phí giao hàng</span>
                                </div>

                                <div class="fee-row text-muted">
                                    <span class="fee-label">Thuế VAT (10%):</span>
                                    <span class="fee-val">Đã bao gồm trong giá</span>
                                </div>

                                <div class="fee-divider"></div>

                                <div class="total-pay-row">
                                    <div class="total-pay-label">
                                        <strong>Tổng thanh toán:</strong>
                                        <small>(Đã gồm VAT & vận chuyển)</small>
                                    </div>
                                    <div class="total-pay-val" data-cart-total><?= money($summary['total']) ?></div>
                                </div>

                                <button type="submit" id="btnPlaceOrder" class="btn-checkout-submit">
                                    <span class="btn-text"><?= icon('check') ?> Xác Nhận Đặt Hàng & Thanh Toán</span>
                                    <span class="btn-loading-state" style="display:none;">
                                        <span class="btn-spinner"></span>
                                        <span>Đang tạo đơn hàng...</span>
                                    </span>
                                </button>

                                <div class="commitments-list">
                                    <div class="commit-item">
                                        <span class="commit-icon"><?= icon('shield') ?></span>
                                        <span>100% Phân phối chính hãng Panasonic Việt Nam</span>
                                    </div>
                                    <div class="commit-item">
                                        <span class="commit-icon"><?= icon('file-text') ?></span>
                                        <span>Có đầy đủ chứng nhận CO/CQ & hóa đơn VAT</span>
                                    </div>
                                    <div class="commit-item">
                                        <span class="commit-icon"><?= icon('truck') ?></span>
                                        <span>Giao nhanh tận chân công trình toàn quốc</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </form>

            <!-- MODAL QUẢN LÝ SỔ ĐỊA CHỈ (SHOPEE / TIKTOK STYLE TINH TẾ) -->
            <?php if ($isLoggedIn): ?>
                <div class="modal-backdrop" id="addressModal" style="display: none;" aria-hidden="true">
                    <div class="modal-dialog-box">
                        <div class="modal-dialog-box__content">
                            <div class="modal-head">
                                <h3 class="modal-head__title"><?= icon('map-pin') ?> Địa chỉ nhận hàng của tôi</h3>
                                <button type="button" class="modal-head__close" id="btnCloseAddressModal" aria-label="Đóng"><?= icon('x') ?></button>
                            </div>

                            <div class="modal-content-body">
                                <!-- Danh sách địa chỉ đã lưu -->
                                <div class="addr-select-list" id="savedAddressListContainer">
                                    <?php foreach ($savedAddresses as $addr): ?>
                                        <div class="addr-card-item <?= (!empty($defaultAddress['id']) && $defaultAddress['id'] === $addr['id']) ? 'is-selected' : '' ?>" data-address-id="<?= (int)$addr['id'] ?>">
                                            <label class="addr-card-label">
                                                <input type="radio" name="modal_selected_address" value="<?= (int)$addr['id'] ?>" 
                                                    <?= (!empty($defaultAddress['id']) && $defaultAddress['id'] === $addr['id']) ? 'checked' : '' ?>
                                                    data-name="<?= e($addr['recipient_name']) ?>"
                                                    data-phone="<?= e($addr['phone']) ?>"
                                                    data-province="<?= e($addr['province']) ?>"
                                                    data-district="<?= e($addr['district']) ?>"
                                                    data-ward="<?= e($addr['ward']) ?>"
                                                    data-detail="<?= e($addr['detail_address']) ?>"
                                                    data-label="<?= e($addr['label'] ?: 'Chân công trình') ?>"
                                                    data-default="<?= (int)$addr['is_default'] ?>"
                                                >
                                                <div class="addr-card-details">
                                                    <div class="addr-user-line">
                                                        <strong class="user-name"><?= e($addr['recipient_name']) ?></strong>
                                                        <span class="user-phone"><?= e($addr['phone']) ?></span>
                                                    </div>
                                                    <div class="addr-full-string">
                                                        <?= e($addr['detail_address']) ?>, <?= e($addr['ward']) ?>, <?= e($addr['district']) ?>, <?= e($addr['province']) ?>
                                                    </div>
                                                    <div class="addr-pills-row">
                                                        <span class="addr-label-pill"><?= e($addr['label'] ?: 'Chân công trình') ?></span>
                                                        <?php if (!empty($addr['is_default'])): ?>
                                                            <span class="addr-default-pill">Mặc định</span>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </label>
                                            <div class="addr-card-actions">
                                                <?php if (empty($addr['is_default'])): ?>
                                                    <button type="button" class="btn-text-action btn-set-default" data-set-default="<?= (int)$addr['id'] ?>">Đặt mặc định</button>
                                                <?php endif; ?>
                                                <button type="button" class="btn-text-action text-danger btn-delete-address" data-delete-address="<?= (int)$addr['id'] ?>">Xóa</button>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>

                                <!-- Nút mở form thêm mới -->
                                <div class="modal-add-row" id="wrapBtnShowNewAddress">
                                    <button type="button" class="btn-add-new-addr" id="btnShowNewAddressForm">
                                        <?= icon('plus') ?> Thêm Địa Chỉ Nhận Hàng Mới
                                    </button>
                                </div>

                                <!-- Form thêm địa chỉ mới inline -->
                                <div class="new-addr-form-drawer" id="newAddressFormBox" style="display: none;">
                                    <div class="drawer-header">
                                        <h4>Thêm địa chỉ giao nhận mới</h4>
                                        <button type="button" class="btn-close-drawer" id="btnCancelNewAddress">Hủy</button>
                                    </div>

                                    <div class="addr-form-grid">
                                        <div class="form-field">
                                            <label class="form-field__label">Họ tên người nhận *</label>
                                            <input type="text" id="newRecipientName" class="form-field__input" placeholder="Họ và tên">
                                        </div>
                                        <div class="form-field">
                                            <label class="form-field__label">Số điện thoại *</label>
                                            <input type="tel" id="newPhone" class="form-field__input" placeholder="Số điện thoại nhận hàng">
                                        </div>
                                    </div>

                                    <div class="addr-location-grid mt-2">
                                        <div class="form-field">
                                            <label class="form-field__label">Tỉnh / Thành phố *</label>
                                            <select id="newProvince" class="form-field__select">
                                                <option value="">-- Tỉnh / Thành --</option>
                                            </select>
                                        </div>
                                        <div class="form-field">
                                            <label class="form-field__label">Quận / Huyện *</label>
                                            <select id="newDistrict" class="form-field__select" disabled>
                                                <option value="">-- Quận / Huyện --</option>
                                            </select>
                                        </div>
                                        <div class="form-field">
                                            <label class="form-field__label">Phường / Xã *</label>
                                            <select id="newWard" class="form-field__select" disabled>
                                                <option value="">-- Phường / Xã --</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-field mt-2">
                                        <label class="form-field__label">Địa chỉ chi tiết (Số nhà, tên đường, tòa nhà) *</label>
                                        <input type="text" id="newDetailAddress" class="form-field__input" placeholder="Số nhà, tên đường, tên dự án...">
                                    </div>

                                    <div class="form-field mt-2">
                                        <label class="form-field__label">Loại địa chỉ</label>
                                        <div class="label-choice-group">
                                            <label class="label-pill active"><input type="radio" name="new_label" value="Chân công trình" checked> Chân công trình</label>
                                            <label class="label-pill"><input type="radio" name="new_label" value="Văn phòng Cty"> Văn phòng Cty</label>
                                            <label class="label-pill"><input type="radio" name="new_label" value="Kho hàng"> Kho hàng</label>
                                            <label class="label-pill"><input type="radio" name="new_label" value="Nhà riêng"> Nhà riêng</label>
                                        </div>
                                    </div>

                                    <div class="mt-2">
                                        <label class="save-addr-checkbox">
                                            <input type="checkbox" id="newIsDefault" value="1">
                                            <span>Đặt làm địa chỉ mặc định</span>
                                        </label>
                                    </div>

                                    <div class="drawer-actions mt-3">
                                        <button type="button" class="btn-prime btn-sm" id="btnSubmitNewAddress">
                                            <?= icon('check') ?> Lưu địa chỉ này
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="modal-foot">
                                <button type="button" class="btn-cancel-modal" id="btnCancelModal">Hủy bỏ</button>
                                <button type="button" class="btn-prime" id="btnApplyAddressSelection"><?= icon('check') ?> Xác nhận chọn</button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

        <?php endif; ?>
    </div>
</div>
