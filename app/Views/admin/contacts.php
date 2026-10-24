<?php
/** @var string $status */
/** @var string $q */
/** @var array $result */
/** @var array $statuses */

$items = $result['items'] ?? [];
$total = $result['total'] ?? 0;
$page  = $result['page'] ?? 1;
$pages = $result['pages'] ?? 1;

$statusBadgeMap = [
    'new'       => ['Mới tiếp nhận', '#dc2626', '#fef2f2'],
    'contacted' => ['Đã liên hệ tư vấn', '#0284c7', '#e0f2fe'],
    'closed'    => ['Đã xử lý xong', '#059669', '#ecfdf5'],
];
?>

<div class="admin-page-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
    <div>
        <h1 style="font-size: 1.5rem; font-weight: 700; color: #0f172a; margin: 0 0 0.25rem 0;">Hộp Thư Liên Hệ & Yêu Cầu Tư Vấn</h1>
        <p style="font-size: 0.88rem; color: #64748b; margin: 0;">Quản lý và phản hồi nhanh các yêu cầu tư vấn kỹ thuật, báo giá từ khách hàng gửi qua form website.</p>
    </div>
    <div style="font-size: 0.88rem; color: #64748b;">
        Tổng số: <strong style="color: #004098;"><?= number_format($total) ?></strong> yêu cầu
    </div>
</div>

<!-- Bộ lọc tab trạng thái & Tìm kiếm -->
<div class="admin-card mb-3" style="padding: 1rem 1.25rem; margin-bottom: 1.25rem;">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
        <!-- Tabs trạng thái -->
        <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
            <a href="<?= url('/admin/lien-he') . ($q ? '?q=' . urlencode($q) : '') ?>" class="btn <?= empty($status) ? 'btn--primary' : 'btn--outline' ?>" style="padding: 0.45rem 0.85rem; font-size: 0.85rem;">
                Tất cả (<?= number_format($total) ?>)
            </a>
            <?php foreach ($statuses as $stKey => $stName): ?>
                <a href="<?= url('/admin/lien-he?status=' . $stKey) . ($q ? '&q=' . urlencode($q) : '') ?>" class="btn <?= $status === $stKey ? 'btn--primary' : 'btn--outline' ?>" style="padding: 0.45rem 0.85rem; font-size: 0.85rem;">
                    <?= e($stName) ?>
                </a>
            <?php endforeach; ?>
        </div>

        <!-- Form tìm kiếm -->
        <form method="get" action="<?= url('/admin/lien-he') ?>" style="display: flex; gap: 0.5rem; min-width: 280px;">
            <?php if ($status): ?>
                <input type="hidden" name="status" value="<?= e($status) ?>">
            <?php endif; ?>
            <input type="text" name="q" value="<?= e($q) ?>" placeholder="Tìm tên, SĐT, email, nội dung..." style="flex: 1; padding: 0.45rem 0.75rem; font-size: 0.88rem;">
            <button type="submit" class="btn btn--primary" style="padding: 0.45rem 0.85rem;">
                <?= icon('search') ?>
            </button>
            <?php if ($q): ?>
                <a href="<?= url('/admin/lien-he' . ($status ? '?status=' . $status : '')) ?>" class="btn btn--outline" style="padding: 0.45rem 0.65rem;" title="Bỏ lọc">
                    <?= icon('x') ?>
                </a>
            <?php endif; ?>
        </form>
    </div>
</div>

<!-- Bảng danh sách liên hệ -->
<div class="admin-card" style="padding: 0; overflow: hidden;">
    <div style="overflow-x: auto;">
        <table class="table" style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.88rem;">
            <thead>
                <tr style="background: #f8fafc; border-bottom: 2px solid #e2e8f0; color: #475569; font-weight: 700;">
                    <th style="padding: 1rem 1.25rem; width: 60px;">ID</th>
                    <th style="padding: 1rem; width: 130px;">Thời gian</th>
                    <th style="padding: 1rem; width: 220px;">Khách hàng / Liên hệ</th>
                    <th style="padding: 1rem;">Nội dung tư vấn & Ghi chú</th>
                    <th style="padding: 1rem; width: 230px;">Xử lý trạng thái</th>
                    <th style="padding: 1rem 1.25rem; text-align: right; width: 80px;">Xóa</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($items)): ?>
                    <tr>
                        <td colspan="6" style="padding: 3rem; text-align: center; color: #94a3b8;">
                            <?= icon('mail') ?><br>Không tìm thấy tin nhắn liên hệ nào phù hợp.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($items as $c): 
                        $badge = $statusBadgeMap[$c['status']] ?? [$c['status'], '#64748b', '#f1f5f9'];
                        $cleanPhone = preg_replace('/[^0-9]/', '', $c['phone']);
                    ?>
                        <tr style="border-bottom: 1px solid #f1f5f9; vertical-align: top; transition: background 0.15s;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='transparent'">
                            <td style="padding: 1rem 1.25rem; color: #64748b; font-weight: 600;">
                                #<?= (int)$c['id'] ?>
                            </td>

                            <td style="padding: 1rem; color: #64748b; font-size: 0.82rem;">
                                <div><?= date('H:i', strtotime($c['created_at'])) ?></div>
                                <div style="font-weight: 600; color: #334155;"><?= date('d/m/Y', strtotime($c['created_at'])) ?></div>
                            </td>

                            <td style="padding: 1rem;">
                                <div style="font-weight: 700; color: #0f172a; font-size: 0.95rem; margin-bottom: 0.25rem;">
                                    <?= e($c['name']) ?>
                                </div>
                                <div style="display: flex; flex-direction: column; gap: 0.25rem; font-size: 0.82rem;">
                                    <?php if (!empty($c['phone'])): ?>
                                        <div style="display: flex; align-items: center; gap: 0.35rem;">
                                            <a href="tel:<?= e($cleanPhone) ?>" style="color: #004098; font-weight: 700; text-decoration: none; display: inline-flex; align-items: center; gap: 0.25rem;">
                                                <?= icon('phone') ?> <?= e($c['phone']) ?>
                                            </a>
                                            <a href="https://zalo.me/<?= e($cleanPhone) ?>" target="_blank" class="badge" style="background: #e0f2fe; color: #0284c7; text-decoration: none; font-size: 0.72rem; padding: 2px 6px;">
                                                Zalo
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                    <?php if (!empty($c['email'])): ?>
                                        <div style="color: #475569; display: flex; align-items: center; gap: 0.25rem;">
                                            <?= icon('mail') ?> <a href="mailto:<?= e($c['email']) ?>" style="color: #475569; text-decoration: none;"><?= e($c['email']) ?></a>
                                        </div>
                                    <?php endif; ?>
                                    <?php if (!empty($c['address'])): ?>
                                        <div style="color: #64748b; font-size: 0.78rem;">
                                            <?= icon('map-pin') ?> <?= e($c['address']) ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </td>

                            <td style="padding: 1rem;">
                                <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 0.75rem; color: #1e293b; line-height: 1.5; font-size: 0.88rem; margin-bottom: 0.5rem;">
                                    <?= nl2br(e($c['message'])) ?>
                                </div>
                                <?php if (!empty($c['note'])): ?>
                                    <div style="font-size: 0.8rem; color: #475569; background: #fffbeb; border: 1px dashed #fde68a; padding: 0.4rem 0.6rem; border-radius: 4px;">
                                        <strong>Ghi chú nội bộ:</strong> <?= e($c['note']) ?>
                                    </div>
                                <?php endif; ?>
                            </td>

                            <td style="padding: 1rem;">
                                <form method="post" action="<?= url('/admin/lien-he/trang-thai/' . $c['id']) ?>" style="display: flex; flex-direction: column; gap: 0.4rem;">
                                    <?= csrf_field() ?>
                                    <div style="display: flex; gap: 0.35rem;">
                                        <select name="status" style="padding: 0.35rem 0.5rem; font-size: 0.82rem; border-radius: 6px; flex: 1;">
                                            <?php foreach ($statuses as $stKey => $stName): ?>
                                                <option value="<?= $stKey ?>" <?= $c['status'] === $stKey ? 'selected' : '' ?>>
                                                    <?= e($stName) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <button type="submit" class="btn btn--primary btn--sm" style="padding: 0.35rem 0.65rem;" title="Cập nhật trạng thái">
                                            Lưu
                                        </button>
                                    </div>
                                    <input type="text" name="note" value="<?= e($c['note'] ?? '') ?>" placeholder="Thêm ghi chú nội bộ..." style="padding: 0.3rem 0.5rem; font-size: 0.78rem; border-radius: 5px;">
                                </form>
                            </td>

                            <td style="padding: 1rem 1.25rem; text-align: right;">
                                <form method="post" action="<?= url('/admin/lien-he/xoa/' . $c['id']) ?>" onsubmit="return confirm('Bạn có chắc chắn muốn xóa thư liên hệ từ <?= addslashes(e($c['name'])) ?>?')">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="btn btn--sm" style="background: #fef2f2; color: #dc2626; border: 1px solid #fee2e2; padding: 0.35rem 0.65rem;" title="Xóa thư">
                                        <?= icon('trash') ?>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Phân trang -->
<?php if ($pages > 1): ?>
    <div style="display: flex; justify-content: center; gap: 0.4rem; margin-top: 1.5rem;">
        <?php for ($p = 1; $p <= $pages; $p++): ?>
            <a href="<?= url('/admin/lien-he?page=' . $p . ($status ? '&status=' . $status : '') . ($q ? '&q=' . urlencode($q) : '')) ?>" class="btn <?= $p === $page ? 'btn--primary' : 'btn--outline' ?>" style="padding: 0.4rem 0.75rem; font-size: 0.85rem; font-weight: 600;">
                <?= $p ?>
            </a>
        <?php endfor; ?>
    </div>
<?php endif; ?>
