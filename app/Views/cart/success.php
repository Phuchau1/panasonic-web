<?php
/** @var array $order */
/** @var array $items */
/** @var string $vietqrUrl */
$paymentMethod = $order['payment_method'] ?? 'cod';
$paymentStatus = $order['payment_status'] ?? 'unpaid';
$isPaid = ($paymentStatus === 'paid');
?>

<div class="page-header page-header--success">
    <div class="container">
        <nav class="breadcrumb" aria-label="Đường dẫn trang">
            <a href="<?= url('/') ?>"><?= icon('home') ?> Trang chủ</a>
            <span class="breadcrumb__sep"><?= icon('chevron-right') ?></span>
            <a href="<?= url('/gio-hang') ?>">Giỏ hàng</a>
            <span class="breadcrumb__sep"><?= icon('chevron-right') ?></span>
            <span class="breadcrumb__current">Đặt hàng thành công</span>
        </nav>
    </div>
</div>

<div class="container section">
    <div class="order-success-wrapper">
        
        <!-- Header thông báo quy trình thanh toán -->
        <div class="success-banner-card" id="successBannerCard">
            <?php if ($isPaid || $paymentMethod === 'cod'): ?>
                <div class="success-badge-icon" id="successBadgeIcon" style="background: #ecfdf5; color: #10b981;">
                    <?= icon('check') ?>
                </div>
                <h1 class="success-main-title" id="successMainTitle">Đặt Hàng Thành Công!</h1>
                <p class="success-sub-text" id="successSubText">
                    <?= $isPaid ? 'Thanh toán hoàn tất! Đơn hàng đã được duyệt và chuyển sang bộ phận kho chuẩn bị đóng hàng.' : 'Đơn hàng COD đã được tiếp nhận. Bộ phận chăm sóc khách hàng sẽ liên hệ xác nhận trước khi giao.' ?>
                </p>
            <?php else: ?>
                <div class="success-badge-icon" id="successBadgeIcon" style="background: #fffbeb; color: #d97706; border: 2px solid #fde68a;">
                    <?= icon('clock') ?>
                </div>
                <h1 class="success-main-title" id="successMainTitle" style="color: #b45309;">Vui Lòng Hoàn Tất Thanh Toán Đơn Hàng</h1>
                <p class="success-sub-text" id="successSubText">
                    Đơn hàng của Quý khách đang ở trạng thái <strong>CHỜ CHUYỂN KHOẢN</strong>. Vui lòng quét mã VietQR bên dưới để hoàn tất đặt hàng.
                </p>
            <?php endif; ?>

            <div class="order-code-highlight">
                <span>Mã dự toán / Đơn hàng:</span>
                <strong class="code-number">#<?= e($order['order_code']) ?></strong>
            </div>

            <?php if (!empty($order['email'])): ?>
                <div class="order-email-notify mt-3" id="orderEmailNotify" style="display: inline-flex; align-items: center; gap: 6px; background: <?= $isPaid || $paymentMethod === 'cod' ? '#ecfdf5' : '#fffbeb' ?>; border: 1px solid <?= $isPaid || $paymentMethod === 'cod' ? '#a7f3d0' : '#fde68a' ?>; color: <?= $isPaid || $paymentMethod === 'cod' ? '#047857' : '#b45309' ?>; padding: 6px 16px; border-radius: 20px; font-size: 13.5px; font-weight: 600;">
                    <?= icon('mail') ?> Email nhận biên nhận & thông báo: <strong style="color: inherit;"><?= e($order['email']) ?></strong>
                </div>
            <?php endif; ?>
        </div>

        <!-- Quy trình xử lý đơn hàng (Order Tracking Stepper) -->
        <div class="enterprise-card mb-4 mt-4">
            <div class="enterprise-card__body">
                <div class="order-timeline-stepper">
                    <div class="stepper-step <?= ($isPaid || $paymentMethod === 'cod') ? 'completed' : 'active' ?>">
                        <div class="step-icon"><?= ($isPaid || $paymentMethod === 'cod') ? icon('check') : icon('file-text') ?></div>
                        <div class="step-label">
                            <strong>Khởi tạo đơn hàng</strong>
                            <span><?= date('H:i d/m/Y', strtotime($order['created_at'] ?? 'now')) ?></span>
                        </div>
                    </div>
                    <div class="stepper-line <?= $isPaid ? 'completed' : 'active' ?>" id="stepperLine1"></div>
                    <div class="stepper-step <?= $isPaid ? 'completed' : ($paymentMethod === 'cod' ? 'completed' : 'pending') ?>" id="stepperStepPayment">
                        <div class="step-icon" id="stepperPaymentIcon"><?= ($isPaid || $paymentMethod === 'cod') ? icon('check') : icon('clock') ?></div>
                        <div class="step-label">
                            <strong id="stepperPaymentTitle"><?= $isPaid ? 'Đã thanh toán thành công' : ($paymentMethod === 'cod' ? 'Thanh toán COD' : 'Chờ chuyển khoản') ?></strong>
                            <span id="stepPaymentSubtext"><?= $isPaid ? 'Đã khớp giao dịch VietQR' : ($paymentMethod === 'cod' ? 'Thanh toán khi nhận hàng' : 'Quét mã ACB bên dưới') ?></span>
                        </div>
                    </div>
                    <div class="stepper-line <?= $isPaid ? 'active' : '' ?>" id="stepperLine2"></div>
                    <div class="stepper-step <?= $isPaid ? 'active' : '' ?>" id="stepperStepConfirm">
                        <div class="step-icon" id="stepperConfirmIcon"><?= icon('box') ?></div>
                        <div class="step-label">
                            <strong>Xác nhận đặt hàng thành công</strong>
                            <span id="stepperConfirmSub"><?= $isPaid ? 'Đã xác nhận & gửi email' : 'Chuẩn bị đóng thùng & CO/CQ' ?></span>
                        </div>
                    </div>
                    <div class="stepper-line"></div>
                    <div class="stepper-step">
                        <div class="step-icon"><?= icon('truck') ?></div>
                        <div class="step-label">
                            <strong>Giao chân công trình</strong>
                            <span>Tài xế bàn giao & nghiệm thu</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Khối Thanh toán: Đặc biệt VietQR Napas 24/7 tương tác tức thì -->
        <?php if ($paymentMethod === 'vietqr' || $paymentMethod === 'bank_transfer'): ?>
            <div class="enterprise-card vietqr-payment-card mb-4" id="vietqrSection">
                <div class="vietqr-header">
                    <div class="vietqr-header-title">
                        <span class="vietqr-icon-wrap"><?= icon('zap') ?></span>
                        <div>
                            <h3>Thanh Toán Chuyển Khoản Siêu Tốc VietQR Napas 24/7</h3>
                            <p class="font-xs text-muted mb-0">Quét mã QR qua ứng dụng ngân hàng để được tự động ghi nhận thanh toán.</p>
                        </div>
                    </div>
                    <div class="payment-status-badge <?= $isPaid ? 'status-paid' : 'status-pending' ?>" id="paymentStatusBadge">
                        <?= $isPaid ? '✓ ĐÃ THANH TOÁN THÀNH CÔNG' : '⏳ ĐANG CHỜ CHUYỂN KHOẢN' ?>
                    </div>
                </div>

                <div class="vietqr-body-grid">
                    <!-- Ảnh QR Code -->
                    <div class="vietqr-image-box">
                        <img src="<?= e($vietqrUrl) ?>" onerror="this.src='<?= url('/uploads/acb-qr-37051817.png') ?>'" alt="VietQR ACB Thanh toán đơn #<?= e($order['order_code']) ?>" class="vietqr-img" width="260" height="260">
                        <span class="vietqr-caption">Quét bằng App ACB hoặc mọi App Ngân Hàng khác</span>
                    </div>

                    <!-- Bảng thông tin thanh toán & Nút Copy -->
                    <div class="vietqr-info-box">
                        <div class="vietqr-row">
                            <span class="vietqr-label">Ngân hàng thụ hưởng:</span>
                            <strong class="vietqr-val">Ngân hàng TMCP Á Châu (ACB)</strong>
                        </div>
                        <div class="vietqr-row highlight-row">
                            <span class="vietqr-label">Số tài khoản nhận:</span>
                            <div class="vietqr-copy-group">
                                <strong class="vietqr-val stk-text" id="bankStk">37051817</strong>
                                <button type="button" class="btn-copy" onclick="copyToClipboard('37051817', this)">Sao chép</button>
                            </div>
                        </div>
                        <div class="vietqr-row highlight-row">
                            <span class="vietqr-label">Số tiền chính xác:</span>
                            <div class="vietqr-copy-group">
                                <strong class="vietqr-val price font-lg text-accent" id="bankAmount"><?= money($order['total']) ?></strong>
                                <button type="button" class="btn-copy" onclick="copyToClipboard('<?= (int)$order['total'] ?>', this)">Sao chép</button>
                            </div>
                        </div>
                        <div class="vietqr-row highlight-row">
                            <span class="vietqr-label">Nội dung chuyển khoản (Memo):</span>
                            <div class="vietqr-copy-group">
                                <code class="vietqr-val memo-code" id="bankMemo"><?= e($order['order_code']) ?></code>
                                <button type="button" class="btn-copy" onclick="copyToClipboard('<?= e($order['order_code']) ?>', this)">Sao chép</button>
                            </div>
                        </div>

                        <!-- Nút bấm tương tác xác nhận chuyển khoản xong -->
                        <div class="vietqr-actions mt-3">
                            <?php if (!$isPaid): ?>
                                <button type="button" class="btn btn--primary btn--block btn-confirm-paid" id="btnConfirmTransfer" data-order-code="<?= e($order['order_code']) ?>">
                                    <?= icon('check') ?> Tôi Đã Chuyển Khoản Xong
                                </button>
                                <p class="font-xs text-muted text-center mt-2 mb-0">Hệ thống sẽ cập nhật trạng thái đơn ngay lập tức.</p>
                            <?php else: ?>
                                <div class="alert alert--success mb-0 text-center font-weight-600">
                                    <?= icon('check-circle') ?> Đơn hàng đã được thanh toán hoàn tất. Chúng tôi đang chuẩn bị hàng xuất kho!
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php elseif ($paymentMethod === 'cod'): ?>
            <div class="enterprise-card payment-info-card mb-4">
                <div class="enterprise-card__body">
                    <div class="d-flex align-items-center gap-3">
                        <span class="payment-card-icon text-primary"><?= icon('truck') ?></span>
                        <div>
                            <h4 class="mb-1">Phương thức: Thanh toán khi nhận hàng (COD chân công trình)</h4>
                            <p class="font-sm text-muted mb-0">
                                Quý khách vui lòng chuẩn bị số tiền <strong><?= money($order['total']) ?></strong> khi nhân viên giao hàng liên hệ. Quý khách được quyền kiểm tra niêm phong, kiểm đếm số lượng vật tư trước khi thanh toán.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        <?php elseif ($paymentMethod === 'momo'): ?>
            <div class="enterprise-card payment-info-card mb-4" style="border-left: 4px solid #d82d8b;">
                <div class="enterprise-card__body">
                    <div class="d-flex align-items-center gap-3">
                        <span class="payment-card-icon" style="background:#fdf2f8;color:#be185d;width:44px;height:44px;border-radius:8px;display:flex;align-items:center;justify-content:center;"><?= icon('zap') ?></span>
                        <div style="flex:1;">
                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                                <h4 class="mb-1" style="color: #9d174d;">Ví Điện Tử MoMo (Môi trường Sandbox Thử Nghiệm)</h4>
                                <span class="bank-pill pill-momo">MOMO QR • TEST</span>
                            </div>
                            <p class="font-sm text-muted mb-2">
                                Đơn hàng đang kết nối cổng thử nghiệm MoMo Test. Quý khách có thể giả lập quét mã hoặc bấm nút xác nhận thanh toán thử nghiệm bên dưới.
                            </p>
                            <?php if (!$isPaid): ?>
                                <div class="d-flex align-items-center gap-2 flex-wrap">
                                    <a href="<?= (new \App\Services\MomoService())->createPaymentUrl($order) ?>" class="btn btn--sm" style="background:#d82d8b;border-color:#d82d8b;color:#fff;text-decoration:none;">
                                        <?= icon('external-link') ?> Mở Cổng Thanh Toán MoMo
                                    </a>
                                    <button type="button" class="btn btn--sm btn--outline btn-confirm-paid" id="btnConfirmTransfer" data-order-code="<?= e($order['order_code']) ?>">
                                        <?= icon('check') ?> Giả Lập Đã Quét MoMo Thành Công
                                    </button>
                                </div>
                            <?php else: ?>
                                <span class="badge badge--success font-sm">✓ Đã khớp giao dịch Ví MoMo Sandbox</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php elseif ($paymentMethod === 'vnpay'): ?>
            <div class="enterprise-card payment-info-card mb-4" style="border-left: 4px solid #005baa;">
                <div class="enterprise-card__body">
                    <div class="d-flex align-items-center gap-3">
                        <span class="payment-card-icon" style="background:#eff6ff;color:#1d4ed8;width:44px;height:44px;border-radius:8px;display:flex;align-items:center;justify-content:center;"><?= icon('shield') ?></span>
                        <div style="flex:1;">
                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                                <h4 class="mb-1" style="color: #1e40af;">Cổng Thanh Toán VNPAY (Môi trường Sandbox Thử Nghiệm)</h4>
                                <span class="bank-pill pill-vnpay">VNPAY-QR • TEST</span>
                            </div>
                            <p class="font-sm text-muted mb-2">
                                Đơn hàng đang kết nối cổng thử nghiệm VNPAY Sandbox chính thức (chuyển tiếp đúng số tiền <strong><?= money($order['total']) ?></strong>).
                            </p>
                            <?php if (!$isPaid): ?>
                                <div class="d-flex align-items-center gap-2 flex-wrap">
                                    <a href="<?= (new \App\Services\VnPayService())->createPaymentUrl($order) ?>" class="btn btn--sm" style="background:#005baa;border-color:#005baa;color:#fff;text-decoration:none;">
                                        <?= icon('external-link') ?> Mở Cổng Thanh Toán VNPAY
                                    </a>
                                    <button type="button" class="btn btn--sm btn--outline btn-confirm-paid" id="btnConfirmTransfer" data-order-code="<?= e($order['order_code']) ?>">
                                        <?= icon('check') ?> Giả Lập Đã Quét VNPAY Thành Công
                                    </button>
                                </div>
                            <?php else: ?>
                                <span class="badge badge--success font-sm">✓ Đã khớp giao dịch VNPAY Sandbox</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php elseif ($paymentMethod === 'b2b_debt'): ?>
            <div class="enterprise-card payment-info-card mb-4">
                <div class="enterprise-card__body">
                    <div class="d-flex align-items-center gap-3">
                        <span class="payment-card-icon text-primary"><?= icon('building') ?></span>
                        <div>
                            <h4 class="mb-1">Phương thức: Bảo lãnh công nợ B2B / Đại lý nhà thầu</h4>
                            <p class="font-sm text-muted mb-0">
                                Đơn hàng đã được lưu vào sổ theo dõi công nợ dự toán của Quý công ty. Biên bản đối soát tháng sẽ được phòng Kế toán gửi vào ngày 25 hàng tháng.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Chi tiết đơn hàng & Người nhận -->
        <div class="order-details-grid mb-4">
            <!-- Thông tin giao hàng -->
            <div class="enterprise-card">
                <div class="enterprise-card__header">
                    <div class="enterprise-card__title"><?= icon('map-pin') ?> Địa chỉ nhận hàng</div>
                </div>
                <div class="enterprise-card__body">
                    <p class="mb-2"><strong>Người nhận:</strong> <?= e($order['customer_name']) ?></p>
                    <p class="mb-2"><strong>Số điện thoại:</strong> <?= e($order['phone']) ?></p>
                    <p class="mb-2"><strong>Địa chỉ công trình:</strong> <?= e($order['address']) ?></p>
                    <?php if (!empty($order['note'])): ?>
                        <p class="mb-0 text-muted font-sm"><strong>Ghi chú:</strong> <?= e($order['note']) ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Thông tin hóa đơn VAT -->
            <div class="enterprise-card">
                <div class="enterprise-card__header">
                    <div class="enterprise-card__title"><?= icon('file-text') ?> Thông tin Hóa đơn GTGT</div>
                </div>
                <div class="enterprise-card__body">
                    <?php if (!empty($order['company'])): ?>
                        <p class="mb-2"><strong>Đơn vị thụ hưởng:</strong> <?= e($order['company']) ?></p>
                        <p class="mb-2"><strong>Mã số thuế:</strong> <?= e($order['tax_code'] ?: 'Đang cập nhật') ?></p>
                        <p class="mb-0"><strong>Email nhận hóa đơn:</strong> <?= e($order['email'] ?: 'Theo số điện thoại') ?></p>
                    <?php else: ?>
                        <p class="text-muted mb-0">Không yêu cầu xuất hóa đơn VAT tên Công ty (Hóa đơn bán lẻ điện tử kèm theo kiện hàng).</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Danh sách thiết bị đã đặt -->
        <div class="enterprise-card mb-4">
            <div class="enterprise-card__header">
                <div class="enterprise-card__title"><?= icon('box') ?> Danh sách thiết bị điện trong đơn hàng</div>
                <span class="badge badge--neutral font-xs"><?= count($items) ?> mã sản phẩm</span>
            </div>
            <div class="table-scroll">
                <table class="cart-table enterprise-table">
                    <thead>
                        <tr>
                            <th>Thiết bị & Model</th>
                            <th class="text-center">Số lượng</th>
                            <th class="text-right">Đơn giá</th>
                            <th class="text-right">Thành tiền</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($items as $it): ?>
                            <tr>
                                <td>
                                    <div class="order-item-info">
                                        <span class="model-code"><?= e($it['model_code']) ?></span>
                                        <div class="product-name font-weight-600"><?= e($it['product_name']) ?></div>
                                        <?php if (!empty($it['product_slug'])): ?>
                                            <div class="mt-1">
                                                <a href="<?= url('/san-pham/' . $it['product_slug']) ?>#tab-reviews" class="link-review font-xs">
                                                    ★ Đánh giá chất lượng thực tế &rarr;
                                                </a>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td class="text-center font-weight-600">
                                    <?= (int)$it['quantity'] ?>
                                </td>
                                <td class="text-right">
                                    <?= money($it['unit_price'] ?? $it['price'] ?? 0) ?>
                                </td>
                                <td class="text-right">
                                    <strong class="price price--accent"><?= money($it['line_total']) ?></strong>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" class="text-right font-weight-600">Tổng thanh toán dự toán:</td>
                            <td class="text-right">
                                <strong class="price price--xl text-primary"><?= money($order['total']) ?></strong>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- Nút thao tác sau khi đặt hàng -->
        <div class="success-footer-actions">
            <?php if (!empty($hasEmailArchive)): ?>
                <a href="<?= url('/don-hang/xem-email/' . $order['order_code']) ?>" target="_blank" class="btn btn--outline" style="border-color: #004098; color: #004098; font-weight: 600;">
                    <?= icon('mail') ?> Xem Biên Nhận Email
                </a>
            <?php endif; ?>
            <button type="button" class="btn btn--outline" onclick="window.print()">
                <?= icon('file-text') ?> In Bảng Dự Toán Đơn Hàng
            </button>
            <a href="<?= url('/') ?>" class="btn btn--primary">
                <?= icon('home') ?> Tiếp Tục Xem Danh Mục Sản Phẩm
            </a>
            <a href="<?= url('/tai-catalogue') ?>" class="btn btn--ghost">
                <?= icon('download') ?> Tải Catalogue Kỹ Thuật 2026
            </a>
        </div>

    </div>
</div>

<script>
function copyToClipboard(text, btn) {
    if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(text).then(() => showCopied(btn));
    } else {
        const textarea = document.createElement('textarea');
        textarea.value = text;
        document.body.appendChild(textarea);
        textarea.select();
        document.execCommand('copy');
        document.body.removeChild(textarea);
        showCopied(btn);
    }
}

function showCopied(btn) {
    const orig = btn.innerText;
    btn.innerText = 'Đã chép!';
    btn.classList.add('copied');
    setTimeout(() => {
        btn.innerText = orig;
        btn.classList.remove('copied');
    }, 1800);
}
</script>
