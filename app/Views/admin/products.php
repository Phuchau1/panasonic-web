<?php
/** @var string $keyword */
/** @var int $categoryId */
/** @var int $seriesId */
/** @var array $categories */
/** @var array $seriesList */
/** @var array $result */
$items = $result['items'];
$total = $result['total'];
$page  = $result['page'];
$pages = $result['pages'];
?>

<div class="admin-card mb-4">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h2 style="font-size: 1.3rem; margin: 0; color: #0f172a;">Quản Lý Toàn Bộ Sản Phẩm Panasonic</h2>
            <p style="font-size: 0.85rem; color: #64748b; margin: 0.25rem 0 0 0;">
                Hệ thống đồng bộ <strong><?= number_format($total) ?></strong> sản phẩm chính hãng (Công tắc - Ổ cắm, MCB/RCBO, Đèn LED, Quạt thông gió, Ổ cắm công nghiệp...).
            </p>
        </div>

        <a href="<?= url('/admin/san-pham/them') ?>" class="btn btn--primary" style="padding: 0.5rem 1rem; display: inline-flex; align-items: center; gap: 0.4rem;">
            <?= icon('plus') ?> Thêm sản phẩm mới
        </a>
    </div>

    <!-- Thanh lọc & tìm kiếm nâng cao -->
    <form action="<?= url('/admin/san-pham') ?>" method="get" style="display: flex; gap: 0.5rem; align-items: center; flex-wrap: wrap; margin-top: 1.25rem; padding-top: 1rem; border-top: 1px solid #e2e8f0;">
        <select name="category_id" class="form-control" style="width: auto; padding: 0.45rem 0.75rem;" onchange="this.form.submit()">
            <option value="">Tất cả danh mục (8 nhóm)</option>
            <?php foreach ($categories as $cat): ?>
                <option value="<?= (int)$cat['id'] ?>" <?= ($categoryId ?? 0) === (int)$cat['id'] ? 'selected' : '' ?>>
                    <?= e($cat['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <select name="series_id" class="form-control" style="width: auto; padding: 0.45rem 0.75rem;" onchange="this.form.submit()">
            <option value="">Tất cả dòng Series</option>
            <?php foreach ($seriesList as $ser): ?>
                <option value="<?= (int)$ser['id'] ?>" <?= ($seriesId ?? 0) === (int)$ser['id'] ? 'selected' : '' ?>>
                    <?= e($ser['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <input type="search" name="q" class="form-control" style="width: 250px; padding: 0.45rem 0.75rem;" placeholder="Tìm theo tên, SKU, Model Code..." value="<?= e($keyword) ?>">
        <button type="submit" class="btn btn--primary" style="padding: 0.45rem 1rem;">Lọc sản phẩm</button>
        <?php if (!empty($keyword) || !empty($categoryId) || !empty($seriesId)): ?>
            <a href="<?= url('/admin/san-pham') ?>" class="btn btn--outline" style="padding: 0.45rem 0.75rem;">Xóa bộ lọc</a>
        <?php endif; ?>
    </form>
</div>

<div class="admin-card">
    <div class="table-scroll">
        <table class="spec-table">
            <thead>
                <tr>
                    <th style="width: 60px;">Hình ảnh</th>
                    <th>Model / Tên sản phẩm</th>
                    <th>Chủng loại & Series</th>
                    <th>Thông số kỹ thuật</th>
                    <th>Giá bán lẻ / Giá sỉ</th>
                    <th>Tồn kho</th>
                    <th style="text-align: right; width: 110px;">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($items)): ?>
                    <tr><td colspan="7" class="text-center text-muted" style="padding: 2.5rem 1rem;">Không tìm thấy sản phẩm nào phù hợp với bộ lọc</td></tr>
                <?php else: ?>
                    <?php foreach ($items as $p): ?>
                        <tr>
                            <td>
                                <img src="<?= media($p['thumbnail']) ?>" alt="" width="52" height="52" style="object-fit: contain; background: #fff; border: 1px solid #e2e8f0; border-radius: 4px; padding: 2px;">
                            </td>
                            <td>
                                <code style="font-size: 0.85rem; font-weight: 700; color: #004098;"><?= e($p['model_code']) ?></code><br>
                                <a href="<?= url('/san-pham/' . $p['slug']) ?>" target="_blank" style="font-weight: 600; color: #0f172a;">
                                    <?= e($p['name']) ?>
                                </a><br>
                                <small class="text-muted">SKU: <?= e($p['sku']) ?></small>
                            </td>
                            <td>
                                <strong><?= e($p['category_name']) ?></strong><br>
                                <span class="badge badge--outline font-xs" style="margin-top: 0.2rem;"><?= e($p['series_name'] ?: 'Tiêu chuẩn') ?></span>
                            </td>
                            <td>
                                <?php if (!empty($p['rated_current'])): ?>
                                    <span class="badge badge--dark font-xs"><?= (float)$p['rated_current'] ?>A</span>
                                <?php endif; ?>
                                <?php if (!empty($p['breaking_capacity'])): ?>
                                    <span class="badge badge--warning font-xs"><?= (float)$p['breaking_capacity'] ?>kA</span>
                                <?php endif; ?>
                                <?php if (!empty($p['poles'])): ?>
                                    <span class="badge badge--outline font-xs"><?= (int)$p['poles'] ?>P</span>
                                <?php endif; ?>
                                <?php if (empty($p['rated_current']) && empty($p['breaking_capacity']) && empty($p['poles'])): ?>
                                    <small class="text-muted">–</small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <strong class="price" style="color: #004098; font-size: 0.95rem;"><?= money($p['price']) ?></strong><br>
                                <?php if ($p['wholesale_price'] > 0): ?>
                                    <small style="color: #059669; font-weight: 600;">Sỉ: <?= money($p['wholesale_price']) ?> (≥<?= (int)$p['wholesale_min_qty'] ?>)</small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="<?= $p['stock'] < 20 ? 'text-danger font-bold' : '' ?>" style="font-weight: 600;">
                                    <?= (int)$p['stock'] ?> <?= e($p['unit']) ?>
                                </span>
                            </td>
                            <td style="text-align: right; white-space: nowrap;">
                                <div style="display: flex; gap: 0.35rem; justify-content: flex-end;">
                                    <a href="<?= url('/admin/san-pham/sua/' . $p['id']) ?>" class="btn btn--outline btn--sm" title="Chỉnh sửa sản phẩm">
                                        <?= icon('edit') ?>
                                    </a>
                                    <form action="<?= url('/admin/san-pham/xoa/' . $p['id']) ?>" method="post" onsubmit="return confirm('Bạn có chắc chắn muốn xóa sản phẩm này?')">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn btn--sm" style="background:#fee2e2; color:#b91c1c; border:1px solid #fca5a5;" title="Xóa">
                                            <?= icon('trash') ?>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Phân trang admin -->
    <?php if ($pages > 1): ?>
        <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 1.5rem; flex-wrap: wrap; gap: 1rem;">
            <div style="font-size: 0.85rem; color: #64748b;">
                Hiển thị trang <strong><?= $page ?></strong> / <strong><?= $pages ?></strong> (Tổng <?= number_format($total) ?> sản phẩm)
            </div>
            <div style="display: flex; gap: 0.35rem;">
                <?php
                $startPage = max(1, $page - 3);
                $endPage   = min($pages, $page + 3);
                if ($page > 1): ?>
                    <a href="<?= url('/admin/san-pham') . query_with(['page' => 1]) ?>" class="btn btn--sm btn--outline">« Đầu</a>
                    <a href="<?= url('/admin/san-pham') . query_with(['page' => $page - 1]) ?>" class="btn btn--sm btn--outline">‹ Trước</a>
                <?php endif; ?>

                <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
                    <a href="<?= url('/admin/san-pham') . query_with(['page' => $i]) ?>" class="btn btn--sm <?= $i === $page ? 'btn--primary' : 'btn--outline' ?>">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>

                <?php if ($page < $pages): ?>
                    <a href="<?= url('/admin/san-pham') . query_with(['page' => $page + 1]) ?>" class="btn btn--sm btn--outline">Sau ›</a>
                    <a href="<?= url('/admin/san-pham') . query_with(['page' => $pages]) ?>" class="btn btn--sm btn--outline">Cuối »</a>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</div>
