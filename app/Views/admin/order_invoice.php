<?php
/** @var array $order */
/** @var array $items */

$paymentLabels = [
    'cod'   => 'Thanh toán tiền mặt khi giao hàng (COD)',
    'bacs'  => 'Chuyển khoản ngân hàng đại lý',
    'vnpay' => 'Thanh toán qua cổng VNPAY / QR Pay',
    'momo'  => 'Thanh toán qua Ví điện tử MoMo',
];
$paymentName = $paymentLabels[$order['payment_method'] ?? 'cod'] ?? strtoupper($order['payment_method'] ?? 'COD');
$carrierInfo = \App\Models\Order::getCarrierInfo($order['shipping_carrier'] ?? null);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Phiếu Xuất Kho & Giao Hàng #<?= e($order['order_code']) ?> - Panasonic</title>
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: 'Be Vietnam Pro', -apple-system, BlinkMacSystemFont, sans-serif;
            background: #f1f5f9;
            color: #0f172a;
            margin: 0;
            padding: 2rem 1rem;
            font-size: 13px;
            line-height: 1.5;
        }
        .invoice-actions {
            max-width: 820px;
            margin: 0 auto 1.5rem auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.6rem 1.25rem;
            font-size: 14px;
            font-weight: 600;
            border-radius: 6px;
            cursor: pointer;
            text-decoration: none;
            border: 1px solid transparent;
        }
        .btn-primary { background: #004098; color: #fff; }
        .btn-primary:hover { background: #00337c; }
        .btn-outline { background: #fff; border-color: #cbd5e1; color: #334155; }
        .btn-outline:hover { background: #f8fafc; }

        .invoice-box {
            max-width: 820px;
            margin: 0 auto;
            background: #fff;
            padding: 2.5rem;
            border-radius: 8px;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.07), 0 2px 4px -2px rgba(0,0,0,0.05);
            border: 1px solid #e2e8f0;
        }
        .invoice-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 2px solid #004098;
            padding-bottom: 1.25rem;
            margin-bottom: 1.5rem;
        }
        .brand-title {
            font-size: 18px;
            font-weight: 800;
            color: #004098;
            letter-spacing: -0.3px;
            margin: 0 0 0.25rem 0;
            text-transform: uppercase;
        }
        .brand-sub {
            font-size: 12px;
            color: #64748b;
            margin: 0;
            line-height: 1.4;
        }
        .invoice-title-block {
            text-align: right;
        }
        .doc-title {
            font-size: 17px;
            font-weight: 800;
            color: #0f172a;
            margin: 0 0 0.25rem 0;
            text-transform: uppercase;
        }
        .doc-code {
            font-size: 15px;
            font-weight: 700;
            color: #004098;
            font-family: monospace;
        }
        .grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
            margin-bottom: 1.5rem;
            background: #f8fafc;
            padding: 1rem 1.25rem;
            border-radius: 6px;
            border: 1px solid #e2e8f0;
        }
        .info-group h4 {
            margin: 0 0 0.5rem 0;
            font-size: 12px;
            font-weight: 700;
            color: #004098;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .info-row {
            display: flex;
            margin-bottom: 0.3rem;
            font-size: 12.5px;
        }
        .info-label {
            width: 110px;
            color: #64748b;
            flex-shrink: 0;
        }
        .info-val {
            color: #0f172a;
            font-weight: 600;
            flex: 1;
        }

        table.items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 1.5rem;
        }
        table.items-table th {
            background: #f1f5f9;
            border: 1px solid #cbd5e1;
            padding: 8px 10px;
            font-size: 12px;
            font-weight: 700;
            color: #334155;
            text-align: left;
        }
        table.items-table td {
            border: 1px solid #e2e8f0;
            padding: 8px 10px;
            font-size: 12.5px;
            color: #1e293b;
        }

        .summary-block {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 2rem;
        }
        .summary-table {
            width: 320px;
            border-collapse: collapse;
        }
        .summary-table td {
            padding: 4px 8px;
            font-size: 13px;
        }
        .summary-table tr.total-row td {
            border-top: 2px solid #004098;
            padding-top: 8px;
            font-size: 15px;
            font-weight: 800;
            color: #004098;
        }

        .signatures {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1rem;
            text-align: center;
            margin-top: 2rem;
            padding-top: 1.5rem;
            border-top: 1px dashed #cbd5e1;
        }
        .sig-role {
            font-weight: 700;
            color: #0f172a;
            font-size: 12px;
            text-transform: uppercase;
            margin-bottom: 0.25rem;
        }
        .sig-sub {
            font-size: 11px;
            color: #94a3b8;
            font-style: italic;
            margin-bottom: 4rem;
        }
        .sig-name {
            font-weight: 600;
            color: #475569;
            font-size: 12px;
        }

        @media print {
            body {
                background: #fff;
                padding: 0;
            }
            .invoice-actions {
                display: none !important;
            }
            .invoice-box {
                box-shadow: none;
                border: none;
                padding: 0;
                max-width: 100%;
            }
            @page {
                size: A4 portrait;
                margin: 12mm 15mm;
            }
        }
    </style>
</head>
<body>

<div class="invoice-actions">
    <div>
        <a href="<?= url('/admin/don-hang/chi-tiet/' . $order['id']) ?>" class="btn btn-outline">
            &larr; Quay lại chi tiết đơn hàng
        </a>
    </div>
    <div style="display: flex; gap: 0.5rem;">
        <button onclick="window.print()" class="btn btn-primary">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 9V2h12v7M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><path d="M6 14h12v8H6z"/></svg>
            In Phiếu Xuất Kho (Ctrl + P)
        </button>
    </div>
</div>

<div class="invoice-box">
    <!-- Header -->
    <div class="invoice-header">
        <div>
            <h1 class="brand-title">TỔNG KHO THIẾT BỊ ĐIỆN PANASONIC</h1>
            <p class="brand-sub">
                Đơn vị phân phối ủy quyền thiết bị điện dân dụng & công nghiệp chính hãng<br>
                Hotline hỗ trợ kỹ thuật & giao nhận: <strong>0798 284 321</strong><br>
                Website: <strong>panasonic-dien.vn</strong> &bull; Email: lienhe@panasonic-dien.vn
            </p>
        </div>
        <div class="invoice-title-block">
            <div class="doc-title">PHIẾU XUẤT KHO & GIAO HÀNG</div>
            <div class="doc-code">#<?= e($order['order_code']) ?></div>
            <div style="font-size: 12px; color: #64748b; margin-top: 4px;">
                Ngày lập: <strong><?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></strong>
            </div>
        </div>
    </div>

    <!-- Thông tin khách hàng & Giao hàng -->
    <div class="grid-2">
        <div class="info-group">
            <h4>Thông tin khách hàng / Chủ đầu tư</h4>
            <div class="info-row">
                <span class="info-label">Khách hàng:</span>
                <span class="info-val"><?= e($order['customer_name']) ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Điện thoại:</span>
                <span class="info-val"><?= e($order['phone'] ?? $order['customer_phone'] ?? '') ?></span>
            </div>
            <?php $custEmail = $order['email'] ?? $order['customer_email'] ?? ''; if (!empty($custEmail)): ?>
                <div class="info-row">
                    <span class="info-label">Email:</span>
                    <span class="info-val"><?= e($custEmail) ?></span>
                </div>
            <?php endif; ?>
            <?php $custCompany = $order['company'] ?? $order['company_name'] ?? ''; if (!empty($custCompany)): ?>
                <div class="info-row">
                    <span class="info-label">Đơn vị:</span>
                    <span class="info-val"><?= e($custCompany) ?></span>
                </div>
            <?php endif; ?>
            <?php $custTax = $order['tax_code'] ?? $order['tax_id'] ?? ''; if (!empty($custTax)): ?>
                <div class="info-row">
                    <span class="info-label">Mã số thuế:</span>
                    <span class="info-val"><?= e($custTax) ?></span>
                </div>
            <?php endif; ?>
        </div>

        <div class="info-group">
            <h4>Địa điểm giao hàng & Thanh toán</h4>
            <div class="info-row">
                <span class="info-label">Địa chỉ nhận:</span>
                <span class="info-val"><?= e($order['address'] ?? $order['customer_address'] ?? '') ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Thanh toán:</span>
                <span class="info-val"><?= e($paymentName) ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Loại đơn hàng:</span>
                <span class="info-val" style="color: <?= ($order['order_type'] ?? '') === 'project' ? '#004098' : '#334155' ?>;">
                    <?= ($order['order_type'] ?? '') === 'project' ? 'Dự án / Giá công trình' : 'Bán lẻ tiêu chuẩn' ?>
                </span>
            </div>
            <?php if (!empty($order['shipping_carrier']) || !empty($order['tracking_code'])): ?>
                <div class="info-row">
                    <span class="info-label">Vận chuyển:</span>
                    <span class="info-val" style="color: #059669; font-weight: 700;">
                        <?= e($carrierInfo['name'] ?? strtoupper($order['shipping_carrier'] ?? 'Xe Giao Hàng')) ?>
                    </span>
                </div>
                <?php if (!empty($order['tracking_code'])): ?>
                    <div class="info-row">
                        <span class="info-label">Mã vận đơn:</span>
                        <span class="info-val" style="font-family: monospace; font-weight: 700; color: #004098;">
                            <?= e($order['tracking_code']) ?>
                        </span>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
            <?php if (!empty($order['note']) || !empty($order['shipping_note'])): ?>
                <div class="info-row">
                    <span class="info-label">Ghi chú giao:</span>
                    <span class="info-val" style="color: #b45309;">
                        <?= e($order['shipping_note'] ?: $order['note']) ?>
                    </span>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Bảng danh mục sản phẩm xuất kho -->
    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 35px; text-align: center;">STT</th>
                <th style="width: 110px;">Mã Model</th>
                <th>Tên quy cách thiết bị điện Panasonic</th>
                <th style="width: 45px; text-align: center;">ĐVT</th>
                <th style="width: 50px; text-align: center;">SL</th>
                <th style="width: 95px; text-align: right;">Đơn giá</th>
                <th style="width: 110px; text-align: right;">Thành tiền</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($items)): ?>
                <tr>
                    <td colspan="7" style="text-align: center; color: #94a3b8; padding: 1.5rem;">Không có sản phẩm trong đơn.</td>
                </tr>
            <?php else: ?>
                <?php $i = 1; foreach ($items as $it): ?>
                    <tr>
                        <td style="text-align: center; font-weight: 600; color: #64748b;"><?= $i++ ?></td>
                        <td><code style="font-size: 12px; font-weight: 700; color: #004098;"><?= e($it['model_code'] ?? $it['sku'] ?? 'N/A') ?></code></td>
                        <td>
                            <strong><?= e($it['product_name']) ?></strong>
                            <?php if (($it['price_type'] ?? '') === 'wholesale'): ?>
                                <span style="font-size: 10px; background: #e0f2fe; color: #0284c7; padding: 1px 4px; border-radius: 3px; font-weight: 600;">Giá CT</span>
                            <?php endif; ?>
                        </td>
                        <td style="text-align: center; color: #64748b;">Cái</td>
                        <td style="text-align: center; font-weight: 700; color: #0f172a;"><?= (int)$it['quantity'] ?></td>
                        <td style="text-align: right;"><?= money($it['unit_price'] ?? $it['price'] ?? 0) ?></td>
                        <td style="text-align: right; font-weight: 700; color: #0f172a;"><?= money($it['line_total'] ?? $it['subtotal'] ?? 0) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- Tổng kết thanh toán -->
    <div class="summary-block">
        <table class="summary-table">
            <tr>
                <td style="color: #64748b;">Tổng tiền hàng:</td>
                <td style="text-align: right; font-weight: 600;"><?= money($order['subtotal'] ?? 0) ?></td>
            </tr>
            <?php $disc = (float)($order['discount'] ?? $order['discount_amount'] ?? 0); if ($disc > 0): ?>
                <tr>
                    <td style="color: #059669;">Chiết khấu dự án:</td>
                    <td style="text-align: right; font-weight: 600; color: #059669;">-<?= money($disc) ?></td>
                </tr>
            <?php endif; ?>
            <?php if (isset($order['shipping_fee'])): ?>
                <tr>
                    <td style="color: #64748b;">Cước vận chuyển:</td>
                    <td style="text-align: right; font-weight: 600;">
                        <?= (float)$order['shipping_fee'] > 0 ? money($order['shipping_fee']) : 'Miễn phí' ?>
                    </td>
                </tr>
            <?php endif; ?>
            <tr class="total-row">
                <td>TỔNG THANH TOÁN:</td>
                <td style="text-align: right;"><?= money($order['total'] ?? 0) ?></td>
            </tr>
        </table>
    </div>

    <!-- Chữ ký xác nhận bàn giao 4 bên -->
    <div class="signatures">
        <div>
            <div class="sig-role">Người Lập Phiếu</div>
            <div class="sig-sub">(Ký, ghi rõ họ tên)</div>
            <div class="sig-name">Ban Quản Trị</div>
        </div>
        <div>
            <div class="sig-role">Thủ Kho Xuất Hàng</div>
            <div class="sig-sub">(Ký, kiểm đủ số lượng)</div>
            <div class="sig-name">Bộ Phận Kho</div>
        </div>
        <div>
            <div class="sig-role">Đơn Vị Giao Hàng</div>
            <div class="sig-sub">(Ký, giao nguyên seal)</div>
            <div class="sig-name"><?= e($carrierInfo['short_name'] ?? 'Đội Vận Chuyển') ?></div>
        </div>
        <div>
            <div class="sig-role">Người Nhận Hàng</div>
            <div class="sig-sub">(Ký, kiểm tra CO/CQ)</div>
            <div class="sig-name"><?= e($order['customer_name']) ?></div>
        </div>
    </div>
</div>

</body>
</html>
