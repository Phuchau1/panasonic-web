<!-- Drawer Giỏ Hàng Trượt Bên Phải (Right Offcanvas Cart Drawer) -->
<div class="cart-drawer-backdrop" id="cartDrawerBackdrop" aria-hidden="true"></div>

<aside class="cart-drawer" id="cartDrawer" aria-hidden="true" role="dialog" aria-modal="true" aria-label="Giỏ hàng của bạn">
    <div class="cart-drawer__header">
        <div class="cart-drawer__title-wrap">
            <svg class="cart-drawer__icon" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="9" cy="21" r="1"></circle>
                <circle cx="20" cy="21" r="1"></circle>
                <path d="M1 1h4l2.7 13.4a2 2 0 0 0 2 1.6h9.7a2 2 0 0 0 2-1.6L23 6H6"></path>
            </svg>
            <h2 class="cart-drawer__title">GIỎ HÀNG VẬT TƯ</h2>
            <span class="cart-drawer__badge" id="drawerCartBadge">0</span>
        </div>
        <button type="button" class="cart-drawer__close" id="closeCartDrawerBtn" aria-label="Đóng giỏ hàng">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="18" y1="6" x2="6" y2="18"></line>
                <line x1="6" y1="6" x2="18" y2="18"></line>
            </svg>
        </button>
    </div>

    <!-- Thanh tiến trình miễn phí vận chuyển -->
    <div class="cart-drawer__freeship-bar">
        <div class="freeship-text" id="drawerFreeshipText">
            Miễn phí vận chuyển cho đơn hàng từ <strong>2.000.000₫</strong>
        </div>
        <div class="freeship-progress">
            <div class="freeship-progress-fill" id="drawerFreeshipFill" style="width: 0%;"></div>
        </div>
    </div>

    <!-- Danh sách sản phẩm trong drawer -->
    <div class="cart-drawer__body" id="drawerCartBody">
        <!-- Khi giỏ rỗng -->
        <div class="cart-drawer__empty" id="drawerCartEmpty">
            <svg class="cart-drawer__empty-icon" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                <circle cx="9" cy="21" r="1"></circle>
                <circle cx="20" cy="21" r="1"></circle>
                <path d="M1 1h4l2.7 13.4a2 2 0 0 0 2 1.6h9.7a2 2 0 0 0 2-1.6L23 6H6"></path>
            </svg>
            <p class="empty-title">Giỏ hàng của bạn đang trống</p>
            <p class="empty-desc">Chưa có thiết bị điện Panasonic nào được thêm vào dự toán.</p>
            <a href="<?= url('/san-pham') ?>" class="btn-drawer-shop">KHÁM PHÁ SẢN PHẨM »</a>
        </div>

        <!-- Danh sách items -->
        <div class="cart-drawer__items" id="drawerCartItems"></div>
    </div>

    <!-- Footer Drawer Tổng tiền & Nút Đặt Hàng -->
    <div class="cart-drawer__footer" id="drawerCartFooter">
        <div class="drawer-summary-row">
            <span>Tạm tính thiết bị:</span>
            <strong id="drawerSubtotal">0₫</strong>
        </div>
        <div class="drawer-summary-row drawer-discount-row" id="drawerDiscountRow" style="display: none;">
            <span>Chiết khấu dự án:</span>
            <strong class="text-success" id="drawerDiscount">-0₫</strong>
        </div>
        <div class="drawer-total-row">
            <span>Tổng thanh toán:</span>
            <strong class="drawer-total-price" id="drawerTotal">0₫</strong>
        </div>
        <div class="drawer-vat-note">(Đã bao gồm thuế VAT 10% & Đầy đủ CO/CQ)</div>

        <div class="drawer-actions">
            <a href="<?= url('/gio-hang') ?>" class="btn-drawer-checkout">
                <span>TIẾN HÀNH ĐẶT HÀNG NGAY</span>
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="9 18 15 12 9 6"></polyline>
                </svg>
            </a>
            <a href="<?= url('/bao-gia-cong-trinh') ?>" class="btn-drawer-quote">
                Yêu cầu xuất bảng báo giá dự án (Excel / PDF) »
            </a>
        </div>
    </div>
</aside>
