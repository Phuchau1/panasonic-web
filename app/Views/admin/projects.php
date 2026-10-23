<div class="admin-card mb-4">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h2 style="font-size: 1.3rem; margin: 0; color: #0f172a;">Quản Lý Dự Án Công Trình (TP.HCM & Long An)</h2>
            <p style="font-size: 0.85rem; color: #64748b; margin: 0.25rem 0 0 0;">
                Tổng cộng <strong><?= number_format($result['total']) ?></strong> dự án đang được cấp vật tư thiết bị điện Panasonic chính hãng.
            </p>
        </div>

        <div style="display: flex; gap: 0.75rem; align-items: center; flex-wrap: wrap;">
            <form method="get" action="<?= url('/admin/du-an') ?>" style="display: flex; gap: 0.5rem;">
                <input type="search" name="q" value="<?= e($keyword ?? '') ?>" placeholder="Tìm tên, vị trí, chủ đầu tư..." class="form-control" style="width: 240px; padding: 0.45rem 0.75rem;">
                <button type="submit" class="btn btn--outline" style="padding: 0.45rem 0.85rem;">Tìm</button>
            </form>
            <a href="<?= url('/admin/du-an/them') ?>" class="btn btn--primary" style="padding: 0.5rem 1rem; display: inline-flex; align-items: center; gap: 0.4rem;">
                <?= icon('plus') ?> Thêm dự án mới
            </a>
        </div>
    </div>
</div>

<div class="admin-card">
    <div class="table-scroll">
        <table class="spec-table">
            <thead>
                <tr>
                    <th style="width: 70px;">Hình ảnh</th>
                    <th>Tên dự án công trình</th>
                    <th>Chủ đầu tư / Vị trí</th>
                    <th>Phân loại / Quy mô</th>
                    <th>Hạng mục thiết bị cấp</th>
                    <th>Năm HT</th>
                    <th>Thứ tự</th>
                    <th>Trạng thái</th>
                    <th style="text-align: right; width: 140px;">Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($result['items'])): ?>
                    <tr>
                        <td colspan="9" class="text-center text-muted" style="padding: 2.5rem 1rem;">
                            Chưa có dự án nào hoặc không tìm thấy kết quả phù hợp.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($result['items'] as $pj): ?>
                        <tr>
                            <td>
                                <img src="<?= media($pj['thumbnail']) ?>" alt="<?= e($pj['title']) ?>" style="width: 60px; height: 42px; object-fit: cover; border-radius: 4px; border: 1px solid #e2e8f0;">
                            </td>
                            <td>
                                <a href="<?= url('/du-an') ?>" target="_blank" style="font-weight: 600; color: #004098;">
                                    <?= e($pj['title']) ?>
                                </a>
                                <?php if (!empty($pj['is_featured'])): ?>
                                    <span class="badge" style="background:#fef3c7; color:#b45309; font-size: 0.7rem; margin-left: 0.3rem;">Nổi bật</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <strong><?= e($pj['client_name']) ?></strong><br>
                                <small class="text-muted"><?= e($pj['location']) ?></small>
                            </td>
                            <td>
                                <span class="badge badge--outline"><?= e($pj['category']) ?></span><br>
                                <small class="text-muted"><?= e($pj['scale']) ?></small>
                            </td>
                            <td>
                                <small style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; max-width: 260px;">
                                    <?= e($pj['supplied_items']) ?>
                                </small>
                            </td>
                            <td><?= e((string)$pj['completion_year']) ?></td>
                            <td><?= (int)$pj['sort_order'] ?></td>
                            <td>
                                <?php if ((int)$pj['status'] === 1): ?>
                                    <span class="badge badge--success">Hiển thị</span>
                                <?php else: ?>
                                    <span class="badge badge--danger">Ẩn</span>
                                <?php endif; ?>
                            </td>
                            <td style="text-align: right; white-space: nowrap;">
                                <a href="<?= url('/admin/du-an/sua/' . $pj['id']) ?>" class="btn btn--sm btn--outline" style="margin-right: 0.25rem;">Sửa</a>
                                <form method="post" action="<?= url('/admin/du-an/xoa/' . $pj['id']) ?>" style="display: inline-block;" onsubmit="return confirm('Bạn có chắc chắn muốn xóa dự án này?')">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="btn btn--sm" style="background:#fee2e2; color:#b91c1c; border:1px solid #fca5a5;">Xóa</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Phân trang -->
    <?php if ($result['pages'] > 1): ?>
        <div style="display: flex; justify-content: center; gap: 0.35rem; margin-top: 1.5rem;">
            <?php for ($p = 1; $p <= $result['pages']; $p++): ?>
                <a href="<?= query_with(['page' => $p]) ?>" class="btn btn--sm <?= $p === $result['page'] ? 'btn--primary' : 'btn--outline' ?>">
                    <?= $p ?>
                </a>
            <?php endfor; ?>
        </div>
    <?php endif; ?>
</div>
