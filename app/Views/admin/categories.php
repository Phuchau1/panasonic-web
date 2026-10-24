<?php
/** @var array $categories */
?>
<div class="admin-page-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
    <div>
        <h1 style="font-size: 1.5rem; font-weight: 700; color: #0f172a; margin: 0 0 0.25rem 0;">Quản Lý Danh Mục Thiết Bị</h1>
        <p style="font-size: 0.88rem; color: #64748b; margin: 0;">Quản lý cây danh mục sản phẩm thiết bị điện Panasonic chính hãng trên toàn hệ thống website.</p>
    </div>
    <div>
        <a href="<?= url('/admin/danh-muc/them') ?>" class="btn btn--primary" style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.6rem 1.25rem; font-weight: 600; border-radius: 8px;">
            <?= icon('plus') ?> Thêm Danh Mục Mới
        </a>
    </div>
</div>

<!-- Thống kê nhanh danh mục -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 1.5rem;">
    <div class="admin-card" style="padding: 1rem 1.25rem; display: flex; align-items: center; gap: 1rem;">
        <div style="width: 44px; height: 44px; border-radius: 10px; background: #eff6ff; color: #004098; display: flex; align-items: center; justify-content: center; font-size: 1.2rem;">
            <?= icon('grid') ?>
        </div>
        <div>
            <div style="font-size: 0.8rem; color: #64748b; font-weight: 600; text-transform: uppercase;">Tổng Danh Mục</div>
            <div style="font-size: 1.4rem; font-weight: 800; color: #0f172a;"><?= count($categories) ?></div>
        </div>
    </div>
    <div class="admin-card" style="padding: 1rem 1.25rem; display: flex; align-items: center; gap: 1rem;">
        <div style="width: 44px; height: 44px; border-radius: 10px; background: #ecfdf5; color: #059669; display: flex; align-items: center; justify-content: center; font-size: 1.2rem;">
            <?= icon('check-circle') ?>
        </div>
        <div>
            <div style="font-size: 0.8rem; color: #64748b; font-weight: 600; text-transform: uppercase;">Đang Hoạt Động</div>
            <div style="font-size: 1.4rem; font-weight: 800; color: #059669;">
                <?= count(array_filter($categories, fn($c) => !empty($c['status']))) ?>
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
                <?= count(array_filter($categories, fn($c) => empty($c['status']))) ?>
            </div>
        </div>
    </div>
    <div class="admin-card" style="padding: 1rem 1.25rem; display: flex; align-items: center; gap: 1rem;">
        <div style="width: 44px; height: 44px; border-radius: 10px; background: #f8fafc; color: #475569; display: flex; align-items: center; justify-content: center; font-size: 1.2rem;">
            <?= icon('package') ?>
        </div>
        <div>
            <div style="font-size: 0.8rem; color: #64748b; font-weight: 600; text-transform: uppercase;">Tổng Sản Phẩm</div>
            <div style="font-size: 1.4rem; font-weight: 800; color: #004098;">
                <?= array_sum(array_column($categories, 'product_count')) ?>
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
                    <th style="padding: 1rem; width: 60px;">Icon</th>
                    <th style="padding: 1rem;">Tên Danh Mục</th>
                    <th style="padding: 1rem;">Đường dẫn (Slug)</th>
                    <th style="padding: 1rem; text-align: center;">Sản phẩm</th>
                    <th style="padding: 1rem; text-align: center; width: 90px;">Thứ tự</th>
                    <th style="padding: 1rem; text-align: center; width: 120px;">Trạng thái</th>
                    <th style="padding: 1rem 1.25rem; text-align: right; width: 150px;">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($categories)): ?>
                    <tr>
                        <td colspan="8" style="padding: 2.5rem; text-align: center; color: #94a3b8;">
                            <?= icon('grid') ?><br>Chưa có danh mục nào. Hãy tạo danh mục đầu tiên!
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($categories as $cat): ?>
                        <tr style="border-bottom: 1px solid #f1f5f9; transition: background 0.15s;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='transparent'">
                            <td style="padding: 1rem 1.25rem; color: #64748b; font-weight: 600;">#<?= (int)$cat['id'] ?></td>
                            <td style="padding: 1rem;">
                                <div style="width: 36px; height: 36px; border-radius: 8px; background: #e0f2fe; color: #0284c7; display: flex; align-items: center; justify-content: center;">
                                    <?= icon($cat['icon'] ?: 'grid') ?>
                                </div>
                            </td>
                            <td style="padding: 1rem;">
                                <a href="<?= url('/danh-muc/' . $cat['slug']) ?>" target="_blank" style="font-weight: 700; color: #0f172a; text-decoration: none; display: flex; align-items: center; gap: 0.35rem;">
                                    <?= e($cat['name']) ?>
                                    <span style="font-size: 0.75rem; color: #94a3b8;"><?= icon('external-link') ?></span>
                                </a>
                                <?php if (!empty($cat['description'])): ?>
                                    <div style="font-size: 0.78rem; color: #64748b; margin-top: 2px; max-width: 320px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                        <?= e($cat['description']) ?>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td style="padding: 1rem; font-family: monospace; font-size: 0.82rem; color: #475569;">
                                /danh-muc/<?= e($cat['slug']) ?>
                            </td>
                            <td style="padding: 1rem; text-align: center;">
                                <a href="<?= url('/admin/san-pham?category_id=' . $cat['id']) ?>" style="display: inline-block; padding: 0.2rem 0.6rem; border-radius: 20px; background: #eff6ff; color: #004098; font-weight: 700; font-size: 0.82rem; text-decoration: none;">
                                    <?= (int)$cat['product_count'] ?> sp
                                </a>
                            </td>
                            <td style="padding: 1rem; text-align: center; font-weight: 600; color: #64748b;">
                                <?= (int)$cat['sort_order'] ?>
                            </td>
                            <td style="padding: 1rem; text-align: center;">
                                <form action="<?= url('/admin/danh-muc/trang-thai/' . $cat['id']) ?>" method="POST" style="display: inline;">
                                    <input type="hidden" name="_token" value="<?= csrf_token() ?>">
                                    <?php if (!empty($cat['status'])): ?>
                                        <button type="submit" class="badge badge--success" style="cursor: pointer; border: none; padding: 0.35rem 0.75rem; border-radius: 20px; font-weight: 600; font-size: 0.78rem;" title="Bấm để tạm ẩn">
                                            ✓ Hoạt động
                                        </button>
                                    <?php else: ?>
                                        <button type="submit" class="badge badge--secondary" style="cursor: pointer; border: none; padding: 0.35rem 0.75rem; border-radius: 20px; font-weight: 600; font-size: 0.78rem; background: #e2e8f0; color: #64748b;" title="Bấm để kích hoạt">
                                            ✕ Tạm ẩn
                                        </button>
                                    <?php endif; ?>
                                </form>
                            </td>
                            <td style="padding: 1rem 1.25rem; text-align: right;">
                                <div style="display: inline-flex; align-items: center; gap: 0.4rem;">
                                    <a href="<?= url('/admin/danh-muc/sua/' . $cat['id']) ?>" class="btn-xs btn--outline" style="text-decoration: none; padding: 0.35rem 0.65rem; border-radius: 6px; font-size: 0.8rem; display: inline-flex; align-items: center; gap: 0.25rem;">
                                        <?= icon('edit') ?> Sửa
                                    </a>
                                    <form action="<?= url('/admin/danh-muc/xoa/' . $cat['id']) ?>" method="POST" style="display: inline;" onsubmit="return confirm('Bạn có chắc chắn muốn xóa danh mục [<?= e($cat['name']) ?>]?');">
                                        <input type="hidden" name="_token" value="<?= csrf_token() ?>">
                                        <button type="submit" class="btn-xs btn--danger-outline" style="padding: 0.35rem 0.65rem; border-radius: 6px; font-size: 0.8rem; background: none; border: 1px solid #fecaca; color: #dc2626; cursor: pointer; display: inline-flex; align-items: center; gap: 0.25rem;">
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
