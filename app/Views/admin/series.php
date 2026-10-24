<?php
/** @var array $series */
?>
<div class="admin-page-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
    <div>
        <h1 style="font-size: 1.5rem; font-weight: 700; color: #0f172a; margin: 0 0 0.25rem 0;">Quản Lý Dòng Sản Phẩm (Series)</h1>
        <p style="font-size: 0.88rem; color: #64748b; margin: 0;">Quản lý các bộ sưu tập thiết bị điện Panasonic cao cấp (Moderva, Gen-X, Halumie, Wide, Full Color...).</p>
    </div>
    <div>
        <a href="<?= url('/admin/dong-series/them') ?>" class="btn btn--primary" style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.6rem 1.25rem; font-weight: 600; border-radius: 8px;">
            <?= icon('plus') ?> Thêm Dòng Series Mới
        </a>
    </div>
</div>

<!-- Thống kê nhanh dòng series -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 1.5rem;">
    <div class="admin-card" style="padding: 1rem 1.25rem; display: flex; align-items: center; gap: 1rem;">
        <div style="width: 44px; height: 44px; border-radius: 10px; background: #eff6ff; color: #004098; display: flex; align-items: center; justify-content: center; font-size: 1.2rem;">
            <?= icon('layers') ?>
        </div>
        <div>
            <div style="font-size: 0.8rem; color: #64748b; font-weight: 600; text-transform: uppercase;">Tổng Dòng Series</div>
            <div style="font-size: 1.4rem; font-weight: 800; color: #0f172a;"><?= count($series) ?></div>
        </div>
    </div>
    <div class="admin-card" style="padding: 1rem 1.25rem; display: flex; align-items: center; gap: 1rem;">
        <div style="width: 44px; height: 44px; border-radius: 10px; background: #ecfdf5; color: #059669; display: flex; align-items: center; justify-content: center; font-size: 1.2rem;">
            <?= icon('check-circle') ?>
        </div>
        <div>
            <div style="font-size: 0.8rem; color: #64748b; font-weight: 600; text-transform: uppercase;">Đang Hiển Thị</div>
            <div style="font-size: 1.4rem; font-weight: 800; color: #059669;">
                <?= count(array_filter($series, fn($s) => !empty($s['status']))) ?>
            </div>
        </div>
    </div>
    <div class="admin-card" style="padding: 1rem 1.25rem; display: flex; align-items: center; gap: 1rem;">
        <div style="width: 44px; height: 44px; border-radius: 10px; background: #fef2f2; color: #dc2626; display: flex; align-items: center; justify-content: center; font-size: 1.2rem;">
            <?= icon('eye') ?>
        </div>
        <div>
            <div style="font-size: 0.8rem; color: #64748b; font-weight: 600; text-transform: uppercase;">Tạm Ẩn</div>
            <div style="font-size: 1.4rem; font-weight: 800; color: #dc2626;">
                <?= count(array_filter($series, fn($s) => empty($s['status']))) ?>
            </div>
        </div>
    </div>
    <div class="admin-card" style="padding: 1rem 1.25rem; display: flex; align-items: center; gap: 1rem;">
        <div style="width: 44px; height: 44px; border-radius: 10px; background: #f8fafc; color: #475569; display: flex; align-items: center; justify-content: center; font-size: 1.2rem;">
            <?= icon('package') ?>
        </div>
        <div>
            <div style="font-size: 0.8rem; color: #64748b; font-weight: 600; text-transform: uppercase;">Sản Phẩm Thuộc Series</div>
            <div style="font-size: 1.4rem; font-weight: 800; color: #004098;">
                <?= array_sum(array_column($series, 'product_count')) ?>
            </div>
        </div>
    </div>
</div>

<div class="admin-card" style="padding: 0; overflow: hidden;">
    <div style="overflow-x: auto;">
        <table class="table" style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.9rem;">
            <thead>
                <tr style="background: #f8fafc; border-bottom: 2px solid #e2e8f0; color: #475569; font-weight: 700;">
                    <th style="padding: 1rem 1.25rem; width: 60px;">ID</th>
                    <th style="padding: 1rem; width: 50px; text-align: center;">Màu</th>
                    <th style="padding: 1rem;">Tên Dòng Series & Định Vị</th>
                    <th style="padding: 1rem;">Đường dẫn (Slug)</th>
                    <th style="padding: 1rem; text-align: center;">Catalogue PDF</th>
                    <th style="padding: 1rem; text-align: center;">Sản phẩm</th>
                    <th style="padding: 1rem; text-align: center; width: 90px;">Thứ tự</th>
                    <th style="padding: 1rem; text-align: center; width: 120px;">Trạng thái</th>
                    <th style="padding: 1rem 1.25rem; text-align: right; width: 150px;">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($series)): ?>
                    <tr>
                        <td colspan="9" style="padding: 2.5rem; text-align: center; color: #94a3b8;">
                            <?= icon('layers') ?><br>Chưa có dòng series nào. Hãy tạo dòng đầu tiên!
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($series as $s): ?>
                        <tr style="border-bottom: 1px solid #f1f5f9; transition: background 0.15s;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='transparent'">
                            <td style="padding: 1rem 1.25rem; color: #64748b; font-weight: 600;">#<?= (int)$s['id'] ?></td>
                            <td style="padding: 1rem; text-align: center;">
                                <span style="display: inline-block; width: 22px; height: 22px; border-radius: 50%; background-color: <?= e($s['color'] ?: '#004098') ?>; border: 2px solid #fff; box-shadow: 0 0 0 1px #cbd5e1;" title="Mã màu đại diện: <?= e($s['color']) ?>"></span>
                            </td>
                            <td style="padding: 1rem;">
                                <div style="font-weight: 700; color: #0f172a; font-size: 0.95rem;">
                                    <?= e($s['name']) ?>
                                </div>
                                <?php if (!empty($s['tagline'])): ?>
                                    <div style="font-size: 0.8rem; color: #64748b; margin-top: 2px;">
                                        <?= e($s['tagline']) ?>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td style="padding: 1rem; color: #475569; font-family: monospace; font-size: 0.85rem;">
                                <?= e($s['slug']) ?>
                            </td>
                            <td style="padding: 1rem; text-align: center;">
                                <?php if (!empty($s['catalog_url'])): ?>
                                    <a href="<?= e($s['catalog_url']) ?>" target="_blank" class="badge" style="background: #e0f2fe; color: #0369a1; text-decoration: none; display: inline-flex; align-items: center; gap: 0.3rem;" title="Xem tài liệu Catalogue">
                                        <?= icon('download') ?> Tải file
                                    </a>
                                <?php else: ?>
                                    <span style="color: #cbd5e1; font-size: 0.8rem;">&mdash;</span>
                                <?php endif; ?>
                            </td>
                            <td style="padding: 1rem; text-align: center;">
                                <a href="<?= url('/admin/san-pham?series_id=' . $s['id']) ?>" class="badge badge--neutral" style="font-weight: 700; text-decoration: none;" title="Xem các sản phẩm thuộc dòng này">
                                    <?= (int)$s['product_count'] ?> SP
                                </a>
                            </td>
                            <td style="padding: 1rem; text-align: center; color: #64748b; font-weight: 600;">
                                <?= (int)$s['sort_order'] ?>
                            </td>
                            <td style="padding: 1rem; text-align: center;">
                                <form method="post" action="<?= url('/admin/dong-series/trang-thai/' . $s['id']) ?>" style="display: inline-block;">
                                    <?= csrf_field() ?>
                                    <button type="submit" style="background: none; border: none; cursor: pointer; padding: 0;" title="Bấm để bật/tắt trạng thái hiển thị">
                                        <?php if (!empty($s['status'])): ?>
                                            <span class="badge badge--success" style="cursor: pointer; display: inline-flex; align-items: center; gap: 0.25rem;">
                                                <span style="width: 6px; height: 6px; border-radius: 50%; background: #059669;"></span> Hoạt động
                                            </span>
                                        <?php else: ?>
                                            <span class="badge badge--danger" style="cursor: pointer; display: inline-flex; align-items: center; gap: 0.25rem;">
                                                <span style="width: 6px; height: 6px; border-radius: 50%; background: #dc2626;"></span> Đang ẩn
                                            </span>
                                        <?php endif; ?>
                                    </button>
                                </form>
                            </td>
                            <td style="padding: 1rem 1.25rem; text-align: right;">
                                <div style="display: flex; gap: 0.5rem; justify-content: flex-end;">
                                    <a href="<?= url('/admin/dong-series/sua/' . $s['id']) ?>" class="btn btn--outline btn--sm" style="padding: 0.35rem 0.65rem;" title="Chỉnh sửa">
                                        <?= icon('edit') ?> Sửa
                                    </a>
                                    <form method="post" action="<?= url('/admin/dong-series/xoa/' . $s['id']) ?>" style="display: inline-block;" onsubmit="return confirm('Bạn có chắc chắn muốn xóa dòng series \'<?= addslashes(e($s['name'])) ?>\'? Các sản phẩm thuộc series sẽ được chuyển về không phân dòng.')">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn btn--sm" style="background: #fef2f2; color: #dc2626; border: 1px solid #fee2e2; padding: 0.35rem 0.65rem;" title="Xóa">
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
</div>
