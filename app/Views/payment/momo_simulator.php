<?php
/** @var array $order */
$orderCode = e($order['order_code']);
$totalFormatted = money($order['total']);
$totalRaw = (int)$order['total'];
$transId = 'MOMO_' . time() . '_' . mt_rand(100, 999);
$qrUrl = "https://api.qrserver.com/v1/create-qr-code/?size=240x240&data=2|99|" . $orderCode . "|||0|0|" . $totalRaw . "|Thanh%20toan%20don%20hang%20Panasonic";
?>

<div class="container section" style="max-width: 680px; margin: 40px auto;">
    <div class="enterprise-card" style="box-shadow: 0 10px 30px rgba(216, 45, 139, 0.12); border: 1px solid #fbcfe8; border-radius: 12px; overflow: hidden;">
        
        <!-- MoMo Header -->
        <div style="background: linear-gradient(135deg, #d82d8b 0%, #a50064 100%); color: #ffffff; padding: 22px 24px; text-align: center;">
            <div style="display: inline-flex; align-items: center; justify-content: center; width: 48px; height: 48px; background: #ffffff; border-radius: 12px; margin-bottom: 10px; font-weight: 990; font-size: 24px; color: #d82d8b; box-shadow: 0 4px 10px rgba(0,0,0,0.15);">
                M
            </div>
            <h2 style="margin: 0; font-size: 20px; font-weight: 800; letter-spacing: 0.3px;">CỔNG THANH TOÁN VÍ MOMO (SANDBOX TEST)</h2>
            <p style="margin: 4px 0 0 0; font-size: 13px; opacity: 0.9;">Môi trường Doanh Nghiệp Thử Nghiệm Kiểm Tra Số Tiền Đơn Hàng</p>
        </div>

        <div style="padding: 24px;">
            <!-- Chi tiết số tiền & đơn hàng -->
            <div style="background: #fdf2f8; border: 1px solid #fbcfe8; border-radius: 8px; padding: 16px; margin-bottom: 24px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; border-bottom: 1px dashed #f472b6; padding-bottom: 10px;">
                    <span style="color: #64748b; font-size: 13.5px;">Mã đơn hàng:</span>
                    <strong style="color: #0f172a; font-size: 15px; font-family: monospace;">#<?= $orderCode ?></strong>
                </div>
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; border-bottom: 1px dashed #f472b6; padding-bottom: 10px;">
                    <span style="color: #64748b; font-size: 13.5px;">Đơn vị nhận:</span>
                    <strong style="color: #0f172a; font-size: 14px;">Thiết Bị Điện Panasonic Việt Nam</strong>
                </div>
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <span style="color: #831843; font-size: 14px; font-weight: 600;">Số tiền thanh toán:</span>
                    <strong style="color: #d82d8b; font-size: 22px; font-weight: 800;"><?= $totalFormatted ?></strong>
                </div>
            </div>

            <!-- Mã QR Giả Lập MoMo -->
            <div style="text-align: center; margin-bottom: 24px;">
                <div style="display: inline-block; padding: 12px; background: #ffffff; border: 2px solid #f472b6; border-radius: 12px; box-shadow: 0 4px 14px rgba(0,0,0,0.06);">
                    <img src="<?= e($qrUrl) ?>" alt="Momo QR Code #<?= $orderCode ?>" width="220" height="220" style="display: block;">
                </div>
                <div style="margin-top: 10px; font-size: 13px; color: #9d174d; font-weight: 600;">
                    Quét mã QR bằng ứng dụng MoMo hoặc bấm nút giả lập bên dưới:
                </div>
            </div>

            <!-- Nút Hành Động Sandbox Simulator -->
            <div style="display: flex; flex-direction: column; gap: 10px;">
                <a href="<?= url('/thanh-toan/momo-return?orderId=' . urlencode($orderCode) . '&amount=' . $totalRaw . '&resultCode=0&message=Success&is_sandbox_simulator=1&transId=' . $transId) ?>" 
                   class="btn btn--block" 
                   style="background: #d82d8b; color: #ffffff; padding: 13px; border-radius: 8px; font-weight: 700; font-size: 15px; text-align: center; text-decoration: none; box-shadow: 0 4px 12px rgba(216, 45, 139, 0.3);">
                    <?= icon('check') ?> Xác Nhận Giả Lập Thanh Toán MoMo Thành Công
                </a>

                <a href="<?= url('/thanh-toan/momo-return?orderId=' . urlencode($orderCode) . '&amount=' . $totalRaw . '&resultCode=1006&message=User_Cancel&is_sandbox_simulator=1') ?>" 
                   class="btn btn--block" 
                   style="background: #f1f5f9; color: #64748b; padding: 11px; border-radius: 8px; font-weight: 600; font-size: 14px; text-align: center; text-decoration: none; border: 1px solid #cbd5e1;">
                    Hủy Giao Dịch & Quay Lại
                </a>
            </div>

            <p style="text-align: center; font-size: 12px; color: #94a3b8; margin: 16px 0 0 0;">
                * Đây là giao diện kiểm thử API Doanh nghiệp kết nối số tiền thực tế của đơn hàng.
            </p>
        </div>
    </div>
</div>
