<?php
/** @var array $order */
/** @var array $items */
/** @var array $statuses */
$allowedStatuses = $allowedStatuses ?? \App\Models\Order::allowedNextStatuses($order['status'] ?? 'pending');
?>

<div class="admin-card" style="max-width: 900px; margin: 0 auto;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; border-bottom: 1px solid #e2e8f0; padding-bottom: 1rem;">
        <div>
            <h2 style="font-size: 1.3rem; margin: 0;">Đơn hàng: #<?= e($order['order_code']) ?></h2>
            <small class="text-muted">Ngày đặt: <?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></small>
        </div>
        <div style="display: flex; gap: 0.5rem;">
            <a href="<?= url('/admin/don-hang/in-hoa-don/' . $order['id']) ?>" target="_blank" class="btn btn--primary btn--sm" style="display: inline-flex; align-items: center; gap: 0.35rem;">
                <?= icon('download') ?> In Phiếu Xuất Kho
            </a>
            <a href="<?= url('/admin/don-hang') ?>" class="btn btn--outline btn--sm">&larr; Quay lại danh sách</a>
        </div>
    </div>

    <!-- Quy trình đơn hàng 4 bước -->
    <div style="background: #fff; border: 1px solid #e2e8f0; border-radius: 8px; padding: 1.25rem 1.5rem; margin-bottom: 1.5rem;">
        <div style="margin-bottom: 1.25rem;">
            <div style="font-size: 0.85rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: #334155;">
                Tiến trình xử lý đơn hàng
            </div>
        </div>

        <?php if ($order['status'] === 'cancelled'): ?>
            <div style="padding: 0.85rem 1rem; background: #fef2f2; border: 1px dashed #fca5a5; border-radius: 6px; color: #b91c1c; font-size: 0.88rem; display: flex; align-items: center; gap: 0.5rem;">
                <?= icon('alert') ?> <strong>Đơn hàng đã huỷ:</strong> Toàn bộ tồn kho đã được hoàn trả, quy trình đã dừng và khóa lại.
            </div>
        <?php else: ?>
            <div id="orderStepper" data-current="<?= e($order['status']) ?>" style="display: flex; align-items: center; justify-content: space-between; position: relative;">
                <?php 
                $steps = [
                    'pending'   => '1. Chờ xác nhận',
                    'confirmed' => '2. Đã xác nhận',
                    'shipping'  => '3. Đang giao hàng',
                    'completed' => '4. Hoàn thành',
                ];
                $stepKeys = array_keys($steps);
                $curIdx = array_search($order['status'], $stepKeys, true);
                if ($curIdx === false) $curIdx = 0;
                ?>
                <?php foreach ($stepKeys as $idx => $sKey): 
                    $isPassed = $idx < $curIdx;
                    $isCurrent = $idx === $curIdx;
                    $isDone = $isPassed || ($isCurrent && $order['status'] === 'completed');
                ?>
                    <div class="stepper-item" data-step="<?= e($sKey) ?>" data-index="<?= $idx ?>" style="display: flex; flex-direction: column; align-items: center; flex: 1; position: relative; z-index: 2;">
                        <div class="stepper-circle" style="width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.92rem; font-weight: 700; transition: all 0.25s;
                            <?= $isDone ? 'background: #059669; color: #fff; box-shadow: 0 2px 6px rgba(5,150,105,0.25);' : ($isCurrent ? 'background: #004098; color: #fff; box-shadow: 0 0 0 4px rgba(0,64,152,0.18);' : 'background: #f8fafc; color: #94a3b8; border: 1.5px solid #cbd5e1;') ?>">
                            <?php if ($isDone): ?>
                                <?= icon('check') ?>
                            <?php else: ?>
                                <?= $idx + 1 ?>
                            <?php endif; ?>
                        </div>
                        <span class="stepper-label" style="font-size: 0.8rem; font-weight: <?= ($isCurrent || $isDone) ? '700' : '500' ?>; color: <?= $isDone ? '#059669' : ($isCurrent ? '#004098' : '#94a3b8') ?>; margin-top: 0.45rem; text-align: center;">
                            <?= $steps[$sKey] ?>
                        </span>
                    </div>
                    <?php if ($idx < count($stepKeys) - 1): ?>
                        <div class="stepper-line" data-line="<?= $idx ?>" style="flex: 1; height: 3px; background: <?= ($idx < $curIdx || ($isCurrent && $order['status'] === 'completed')) ? '#059669' : '#e2e8f0' ?>; margin-top: -20px; position: relative; z-index: 1; transition: background 0.25s;"></div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Cập nhật trạng thái đơn hàng -->
    <?php if ($order['status'] === 'completed'): ?>
        <!-- Khi đơn hàng đã Hoàn thành: Khóa hoàn toàn, không hiển thị dropdown -->
        <div style="background: #ecfdf5; border: 1.5px solid #a7f3d0; padding: 1rem 1.25rem; border-radius: 8px; margin-bottom: 1.5rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
            <div style="display: flex; align-items: center; gap: 0.75rem;">
                <div style="width: 38px; height: 38px; border-radius: 50%; background: #059669; color: #fff; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; flex-shrink: 0;">
                    <?= icon('check') ?>
                </div>
                <div>
                    <div style="font-weight: 700; color: #065f46; font-size: 1rem;">Đơn hàng đã hoàn thành thành công</div>
                    <div style="font-size: 0.82rem; color: #047857; margin-top: 2px;">
                        Đơn hàng đã được giao nhận và hoàn tất quy trình.
                    </div>
                </div>
            </div>
            <span class="badge badge--success" style="padding: 0.45rem 0.9rem; font-size: 0.85rem; font-weight: 700; display: inline-flex; align-items: center; gap: 0.35rem;">
                <?= icon('check') ?> Đã Hoàn Tất
            </span>
        </div>
    <?php elseif ($order['status'] === 'cancelled'): ?>
        <!-- Khi đơn hàng đã Huỷ: Khóa hoàn toàn -->
        <div style="background: #fef2f2; border: 1.5px solid #fecaca; padding: 1rem 1.25rem; border-radius: 8px; margin-bottom: 1.5rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
            <div style="display: flex; align-items: center; gap: 0.75rem;">
                <div style="width: 38px; height: 38px; border-radius: 50%; background: #dc2626; color: #fff; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; flex-shrink: 0;">
                    <?= icon('alert') ?>
                </div>
                <div>
                    <div style="font-weight: 700; color: #991b1b; font-size: 1rem;">Đơn hàng đã bị Huỷ</div>
                    <div style="font-size: 0.82rem; color: #b91c1c; margin-top: 2px;">
                        Tồn kho đã được hoàn lại. Đơn hàng kết thúc quy trình.
                    </div>
                </div>
            </div>
            <span class="badge badge--danger" style="padding: 0.45rem 0.9rem; font-size: 0.85rem; font-weight: 700;">
                Đã Huỷ
            </span>
        </div>
    <?php else: ?>
        <!-- Đơn hàng đang xử lý (pending / confirmed / shipping) -->
        <div style="background: #f8fafc; border: 1.5px solid #e2e8f0; padding: 1rem 1.25rem; border-radius: 8px; margin-bottom: 1.5rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
            <div>
                <div style="font-size: 0.78rem; color: #64748b; margin-bottom: 0.2rem; text-transform: uppercase; font-weight: 600;">Trạng thái hiện tại:</div>
                <div style="display: flex; align-items: center; gap: 0.5rem;">
                    <span id="currentStatusBadge" class="badge badge--primary" style="font-size: 0.92rem; padding: 0.35rem 0.75rem; font-weight: 700;">
                        <?= e($statuses[$order['status']] ?? $order['status']) ?>
                    </span>
                    <span style="font-size: 0.82rem; color: #64748b;">(Đơn <?= $order['order_type'] === 'project' ? 'Giá Công Trình' : 'Giá Bán Lẻ' ?>)</span>
                </div>
            </div>

            <form action="<?= url('/admin/don-hang/trang-thai/' . $order['id']) ?>" method="post" style="display: flex; gap: 0.6rem; align-items: center; flex-wrap: wrap;">
                <?= csrf_field() ?>
                <div style="display: flex; align-items: center; gap: 0.45rem;">
                    <label style="font-size: 0.85rem; font-weight: 600; color: #334155; white-space: nowrap;">Chuyển tiến sang:</label>
                    <select name="status" required style="min-width: 220px; height: 40px !important; line-height: 1.4 !important; padding: 0.4rem 0.85rem !important; font-size: 0.88rem !important; font-weight: 600; border: 1.5px solid #004098 !important; border-radius: 7px; background: #fff !important; color: #0f172a !important; box-sizing: border-box !important;">
                        <option value="" disabled selected>-- Chọn bước tiếp theo --</option>
                        <?php foreach ($allowedStatuses as $stVal => $stText): ?>
                            <option value="<?= e($stVal) ?>" style="<?= $stVal === 'cancelled' ? 'color: #dc2626;' : 'color: #004098; font-weight: 600;' ?>">
                                <?= $stVal === 'cancelled' ? '✕ Huỷ: ' : '➔ Tiến: ' ?><?= e($stText) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" class="btn btn--primary" style="height: 40px; padding: 0 1.25rem; font-weight: 700; display: inline-flex; align-items: center; justify-content: center; border-radius: 7px;">
                    Cập nhật
                </button>
            </form>
        </div>
    <?php endif; ?>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
        <div>
            <h4 style="margin-top: 0;">Thông tin người đặt</h4>
            <p><strong>Họ tên:</strong> <?= e($order['customer_name']) ?></p>
            <p><strong>Điện thoại:</strong> <a href="tel:<?= e($order['phone']) ?>"><?= e($order['phone']) ?></a></p>
            <p><strong>Email:</strong> <?= e($order['email'] ?: '–') ?></p>
            <p><strong>Địa chỉ nhận hàng:</strong> <?= e($order['address']) ?></p>
            <p><strong>Loại tài khoản:</strong> 
                <?php if (!empty($order['user_id'])): ?>
                    <span class="badge badge--success">Thành viên đã đăng ký (#<?= (int)$order['user_id'] ?>)</span>
                <?php else: ?>
                    <span class="badge" style="background:#f1f5f9; color:#64748b;">Khách vãng lai</span>
                <?php endif; ?>
            </p>
        </div>
        <div>
            <h4 style="margin-top: 0;">Thông tin xuất hóa đơn & Ghi chú</h4>
            <p><strong>Công ty:</strong> <?= e($order['company'] ?: 'Khách hàng cá nhân') ?></p>
            <p><strong>Mã số thuế:</strong> <?= e($order['tax_code'] ?: '–') ?></p>
            <p><strong>Hình thức TT:</strong> <?= $order['payment_method'] === 'bank_transfer' ? 'Chuyển khoản công ty' : 'COD' ?></p>
            <p><strong>Ghi chú:</strong> <?= e($order['note'] ?: '–') ?></p>
        </div>
    </div>

    <h4>Chi tiết danh mục thiết bị đã đặt</h4>
    <div class="table-scroll">
        <table class="spec-table">
            <thead>
                <tr>
                    <th>STT</th>
                    <th>Tên thiết bị</th>
                    <th>Model Code</th>
                    <th>Đơn giá</th>
                    <th>Số lượng</th>
                    <th>Thành tiền</th>
                </tr>
            </thead>
            <tbody>
                <?php $i = 1; foreach ($items as $it): ?>
                    <tr>
                        <td><?= $i++ ?></td>
                        <td><strong><?= e($it['product_name']) ?></strong></td>
                        <td><code><?= e($it['model_code']) ?></code></td>
                        <td>
                            <?= money($it['unit_price']) ?>
                            <?php if ($it['price_type'] === 'wholesale'): ?>
                                <span class="badge badge--success font-xs">Giá CT</span>
                            <?php endif; ?>
                        </td>
                        <td><?= (int)$it['quantity'] ?></td>
                        <td><strong><?= money($it['line_total']) ?></strong></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="5" style="text-align: right;">Tổng theo giá niêm yết:</td>
                    <td><?= money($order['subtotal']) ?></td>
                </tr>
                <?php if ($order['discount'] > 0): ?>
                    <tr class="text-success">
                        <td colspan="5" style="text-align: right;">Chiết khấu công trình:</td>
                        <td>-<?= money($order['discount']) ?></td>
                    </tr>
                <?php endif; ?>
                <tr>
                    <td colspan="5" style="text-align: right; font-weight: 700; font-size: 1.1rem;">Tổng thanh toán cuối cùng:</td>
                    <td><strong class="price price--lg"><?= money($order['total']) ?></strong></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
