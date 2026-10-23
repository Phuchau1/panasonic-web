<?php
/** @var string $status */
/** @var array $result */
/** @var array $statuses */
$items = $result['items'];
$page  = $result['page'];
$pages = $result['pages'];
?>

<div class="admin-card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
        <div style="display: flex; gap: 0.5rem; align-items: center;">
            <span>Trạng thái đơn:</span>
            <a href="<?= url('/admin/don-hang') ?>" class="btn btn--sm <?= empty($status) ? 'btn--primary' : 'btn--outline' ?>">Tất cả</a>
            <?php foreach ($statuses as $stKey => $stName): ?>
                <a href="<?= url('/admin/don-hang?status=' . $stKey) ?>" class="btn btn--sm <?= $status === $stKey ? 'btn--primary' : 'btn--outline' ?>"><?= e($stName) ?></a>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="table-scroll">
        <table class="spec-table">
            <thead>
                <tr>
                    <th>Mã đơn</th>
                    <th>Khách hàng & SĐT</th>
                    <th>Loại đơn</th>
                    <th>Địa chỉ giao hàng</th>
                    <th>Tổng tiền</th>
                    <th>Phương thức</th>
                    <th>Trạng thái</th>
                    <th>Thời gian</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($items)): ?>
                    <tr><td colspan="9" class="text-center text-muted">Chưa có đơn hàng nào</td></tr>
                <?php else: ?>
                    <?php foreach ($items as $o): ?>
                        <tr>
                            <td>
                                <a href="<?= url('/admin/don-hang/' . $o['id']) ?>"><strong>#<?= e($o['order_code']) ?></strong></a>
                            </td>
                            <td>
                                <strong><?= e($o['customer_name']) ?></strong><br>
                                <small><?= e($o['phone']) ?></small>
                            </td>
                            <td>
                                <span class="badge badge--<?= $o['order_type'] === 'project' ? 'primary' : 'outline' ?> font-xs">
                                    <?= $o['order_type'] === 'project' ? 'Công trình (Sỉ)' : 'Bán lẻ' ?>
                                </span>
                            </td>
                            <td>
                                <small><?= e($o['address']) ?></small>
                            </td>
                            <td>
                                <strong class="price"><?= money($o['total']) ?></strong>
                            </td>
                            <td>
                                <small><?= $o['payment_method'] === 'bank_transfer' ? 'Chuyển khoản' : 'COD' ?></small>
                            </td>
                            <td>
                                <span class="badge badge--<?= $o['status'] === 'completed' ? 'success' : ($o['status'] === 'cancelled' ? 'danger' : 'warning') ?> font-xs">
                                    <?= e($statuses[$o['status']] ?? $o['status']) ?>
                                </span>
                            </td>
                            <td>
                                <small><?= date('d/m/Y H:i', strtotime($o['created_at'])) ?></small>
                            </td>
                            <td>
                                <a href="<?= url('/admin/don-hang/' . $o['id']) ?>" class="btn btn--outline btn--sm">Chi tiết</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php if ($pages > 1): ?>
        <nav class="pagination mt-4" style="justify-content: flex-end;">
            <?php for ($i = 1; $i <= $pages; $i++): ?>
                <a href="<?= url('/admin/don-hang') . query_with(['page' => $i]) ?>" class="pagination__btn <?= $i === $page ? 'is-active' : '' ?>"><?= $i ?></a>
            <?php endfor; ?>
        </nav>
    <?php endif; ?>
</div>
