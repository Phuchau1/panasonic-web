<?php
/** @var string $status */
/** @var array $result */
/** @var array $statuses */
/** @var array $types */
$items = $result['items'];
$page  = $result['page'];
$pages = $result['pages'];
?>

<div class="admin-card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
        <div style="display: flex; gap: 0.5rem; align-items: center;">
            <span>Lọc theo trạng thái:</span>
            <a href="<?= url('/admin/bao-gia') ?>" class="btn btn--sm <?= empty($status) ? 'btn--primary' : 'btn--outline' ?>">Tất cả</a>
            <?php foreach ($statuses as $stKey => $stName): ?>
                <a href="<?= url('/admin/bao-gia?status=' . $stKey) ?>" class="btn btn--sm <?= $status === $stKey ? 'btn--primary' : 'btn--outline' ?>"><?= e($stName) ?></a>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="table-scroll">
        <table class="spec-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Khách hàng & Doanh nghiệp</th>
                    <th>Liên hệ</th>
                    <th>Dự án & Địa chỉ</th>
                    <th>File BOM</th>
                    <th>Ghi chú</th>
                    <th>Trạng thái</th>
                    <th>Thời gian</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($items)): ?>
                    <tr><td colspan="8" class="text-center text-muted">Không có yêu cầu báo giá nào</td></tr>
                <?php else: ?>
                    <?php foreach ($items as $q): ?>
                        <tr>
                            <td>#<?= (int)$q['id'] ?></td>
                            <td>
                                <strong><?= e($q['customer_name']) ?></strong><br>
                                <span class="badge badge--outline font-xs"><?= e($types[$q['customer_type']] ?? $q['customer_type']) ?></span><br>
                                <small class="text-muted"><?= e($q['company'] ?: '–') ?></small>
                            </td>
                            <td>
                                <strong><a href="tel:<?= e($q['phone']) ?>"><?= e($q['phone']) ?></a></strong><br>
                                <small><?= e($q['email'] ?: '–') ?></small>
                            </td>
                            <td>
                                <strong><?= e($q['project_name'] ?: 'Nhà dân dụng / Chưa rõ') ?></strong><br>
                                <small class="text-muted"><?= e($q['address'] ?: '–') ?></small>
                            </td>
                            <td>
                                <?php if ($q['file_bom_path']): ?>
                                    <a href="<?= url('/admin/bao-gia/tai-file/' . $q['id']) ?>" class="btn btn--primary btn--sm" title="<?= e($q['file_original']) ?>">
                                        <?= icon('download') ?> Tải BOM
                                    </a>
                                <?php else: ?>
                                    <span class="text-muted">Không đính kèm file</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div style="max-width: 250px; font-size: 0.8rem; white-space: pre-wrap;"><?= e($q['note'] ?: '–') ?></div>
                            </td>
                            <td>
                                <form action="<?= url('/admin/bao-gia/trang-thai/' . $q['id']) ?>" method="post">
                                    <?= csrf_field() ?>
                                    <select name="status" class="field__input field__input--sm" onchange="this.form.submit()">
                                        <?php foreach ($statuses as $k => $v): ?>
                                            <option value="<?= e($k) ?>" <?= $q['status'] === $k ? 'selected' : '' ?>><?= e($v) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </form>
                            </td>
                            <td>
                                <small><?= date('d/m/Y H:i', strtotime($q['created_at'])) ?></small>
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
                <a href="<?= url('/admin/bao-gia') . query_with(['page' => $i]) ?>" class="pagination__btn <?= $i === $page ? 'is-active' : '' ?>"><?= $i ?></a>
            <?php endfor; ?>
        </nav>
    <?php endif; ?>
</div>
