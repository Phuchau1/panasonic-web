<?php
/** @var array $order */
/** @var array $items */
/** @var array $statuses */
?>

<div class="admin-card" style="max-width: 900px; margin: 0 auto;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; border-bottom: 1px solid #e2e8f0; padding-bottom: 1rem;">
        <div>
            <h2 style="font-size: 1.3rem; margin: 0;">Đơn hàng: #<?= e($order['order_code']) ?></h2>
            <small class="text-muted">Ngày đặt: <?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></small>
        </div>
        <a href="<?= url('/admin/don-hang') ?>" class="btn btn--outline btn--sm">&larr; Quay lại danh sách</a>
    </div>

    <!-- Cập nhật trạng thái đơn hàng -->
    <div style="background: #f8fafc; border: 1px solid #e2e8f0; padding: 1rem; border-radius: 6px; margin-bottom: 1.5rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
        <div>
            <strong>Trạng thái hiện tại:</strong> 
            <span class="badge badge--primary"><?= e($statuses[$order['status']] ?? $order['status']) ?></span>
            (Đơn <?= $order['order_type'] === 'project' ? 'Giá Công Trình' : 'Giá Bán Lẻ' ?>)
        </div>
        <form action="<?= url('/admin/don-hang/trang-thai/' . $order['id']) ?>" method="post" style="display: flex; gap: 0.5rem;">
            <?= csrf_field() ?>
            <select name="status" class="field__input field__input--sm">
                <?php foreach ($statuses as $stVal => $stText): ?>
                    <option value="<?= e($stVal) ?>" <?= $order['status'] === $stVal ? 'selected' : '' ?>><?= e($stText) ?></option>
                <?php endforeach; ?>
            </select>
            <button type="submit" class="btn btn--primary btn--sm">Cập nhật</button>
        </form>
    </div>

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
