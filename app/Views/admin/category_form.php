<?php
/** @var array $category */
$isEdit = !empty($category['id']);
$curIcon = $category['icon'] ?? 'grid';
$commonIcons = ['grid', 'zap', 'switch', 'breaker', 'bulb', 'fan', 'plug', 'shield', 'building', 'box', 'layers', 'leaf', 'truck', 'award', 'settings', 'calculator'];
?>

<div class="admin-page-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
    <div>
        <div style="font-size: 0.85rem; color: #64748b; margin-bottom: 0.25rem;">
            <a href="<?= url('/admin/danh-muc') ?>" style="color: #64748b; text-decoration: none;">Danh mục</a> &rsaquo;
            <span><?= $isEdit ? 'Chỉnh sửa' : 'Thêm mới' ?></span>
        </div>
        <h1 style="font-size: 1.5rem; font-weight: 700; color: #0f172a; margin: 0;">
            <?= $isEdit ? 'Chỉnh Sửa Danh Mục: ' . e($category['name']) : 'Thêm Danh Mục Thiết Bị Mới' ?>
        </h1>
    </div>
    <div>
        <a href="<?= url('/admin/danh-muc') ?>" class="btn btn--outline" style="padding: 0.55rem 1rem;">
            &larr; Quay lại danh sách
        </a>
    </div>
</div>

<form method="post" action="<?= $isEdit ? url('/admin/danh-muc/sua/' . $category['id']) : url('/admin/danh-muc/them') ?>">
    <?= csrf_field() ?>

    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem; align-items: start;">
        <!-- Cột trái: Thông tin chính -->
        <div class="admin-card" style="display: flex; flex-direction: column; gap: 1.25rem;">
            <div>
                <label class="field__label">Tên danh mục thiết bị <span style="color: #ef4444;">*</span></label>
                <input type="text" id="cat_name" name="name" value="<?= e($category['name'] ?? '') ?>" placeholder="Ví dụ: Công tắc - Ổ cắm, Thiết bị đóng cắt MCB..." required style="width: 100%;">
                <div style="font-size: 0.8rem; color: #64748b; margin-top: 0.35rem;">Tên hiển thị trên menu chính và bộ lọc danh mục toàn website.</div>
            </div>

            <div>
                <label class="field__label">Đường dẫn thân thiện (Slug) <span style="color: #ef4444;">*</span></label>
                <div style="display: flex; gap: 0.5rem; align-items: center;">
                    <span style="font-size: 0.88rem; color: #64748b; white-space: nowrap;">/danh-muc/</span>
                    <input type="text" id="cat_slug" name="slug" value="<?= e($category['slug'] ?? '') ?>" placeholder="cong-tac-o-cam" required style="flex: 1;">
                </div>
                <div style="font-size: 0.8rem; color: #64748b; margin-top: 0.35rem;">URL SEO thân thiện. Tự động chuyển đổi từ tên danh mục.</div>
            </div>

            <div>
                <label class="field__label">Biểu tượng nhận diện (Icon)</label>
                <div style="display: flex; gap: 0.75rem; align-items: center; margin-bottom: 0.75rem;">
                    <div id="icon-preview" style="width: 44px; height: 44px; border-radius: 8px; background: #e0f2fe; color: #004098; display: flex; align-items: center; justify-content: center; font-size: 1.3rem;">
                        <?= icon($curIcon) ?>
                    </div>
                    <input type="text" id="cat_icon" name="icon" value="<?= e($curIcon) ?>" placeholder="grid" style="width: 140px;">
                    <span style="font-size: 0.82rem; color: #64748b;">(Chọn nhanh từ danh sách bên dưới hoặc nhập mã icon)</span>
                </div>
                <!-- Bảng chọn nhanh icon -->
                <div style="display: flex; flex-wrap: wrap; gap: 0.5rem; background: #f8fafc; padding: 0.75rem; border-radius: 8px; border: 1px dashed #cbd5e1;">
                    <?php foreach ($commonIcons as $ic): ?>
                        <button type="button" class="btn-select-icon" data-icon="<?= $ic ?>" style="border: 1px solid #e2e8f0; background: #fff; border-radius: 6px; padding: 0.4rem 0.6rem; cursor: pointer; display: inline-flex; align-items: center; gap: 0.35rem; font-size: 0.8rem; color: #334155;">
                            <?= icon($ic) ?> <span><?= $ic ?></span>
                        </button>
                    <?php endforeach; ?>
                </div>
            </div>

            <div>
                <label class="field__label">Mô tả tóm tắt danh mục</label>
                <textarea name="description" rows="4" placeholder="Mô tả tiêu chuẩn kỹ thuật, tính năng và ứng dụng tiêu biểu của danh mục thiết bị Panasonic này..." style="width: 100%;"><?= e($category['description'] ?? '') ?></textarea>
                <div style="font-size: 0.8rem; color: #64748b; margin-top: 0.35rem;">Hỗ trợ tối ưu SEO cho trang chuyên mục thiết bị.</div>
            </div>
        </div>

        <!-- Cột phải: Cài đặt hiển thị & Đăng -->
        <div style="display: flex; flex-direction: column; gap: 1.5rem;">
            <div class="admin-card">
                <h3 style="font-size: 1.05rem; font-weight: 700; color: #0f172a; margin: 0 0 1rem 0; padding-bottom: 0.75rem; border-bottom: 1px solid #f1f5f9;">
                    Cấu Hình Xuất Bản
                </h3>

                <div style="margin-bottom: 1.25rem;">
                    <label class="field__label">Thứ tự hiển thị (Ưu tiên)</label>
                    <input type="number" name="sort_order" value="<?= (int)($category['sort_order'] ?? 0) ?>" min="0" step="1" style="width: 100%;">
                    <div style="font-size: 0.78rem; color: #64748b; margin-top: 0.25rem;">Số nhỏ hơn hiển thị lên đầu tiên (0, 1, 2...).</div>
                </div>

                <div style="margin-bottom: 1.25rem;">
                    <label class="field__label" style="margin-bottom: 0.5rem;">Trạng thái phát hành</label>
                    <label style="display: flex; align-items: center; gap: 0.6rem; cursor: pointer; font-size: 0.9rem; color: #1e293b; font-weight: 500;">
                        <input type="checkbox" name="status" value="1" <?= (!isset($category['status']) || !empty($category['status'])) ? 'checked' : '' ?> style="width: 18px; height: 18px; accent-color: #004098;">
                        <span>Kích hoạt hiển thị website</span>
                    </label>
                    <div style="font-size: 0.78rem; color: #64748b; margin-top: 0.35rem; margin-left: 1.7rem;">
                        Nếu bỏ chọn, danh mục và link truy cập sẽ tạm ẩn với khách hàng.
                    </div>
                </div>

                <div style="padding-top: 1rem; border-top: 1px solid #f1f5f9; display: flex; flex-direction: column; gap: 0.5rem;">
                    <button type="submit" class="btn btn--primary" style="width: 100%; padding: 0.75rem; font-weight: 700; justify-content: center; display: flex; align-items: center; gap: 0.5rem;">
                        <?= icon('check') ?> <?= $isEdit ? 'Lưu Thay Đổi' : 'Tạo Danh Mục Mới' ?>
                    </button>
                    <a href="<?= url('/admin/danh-muc') ?>" class="btn btn--outline" style="width: 100%; padding: 0.6rem; text-align: center; justify-content: center;">
                        Hủy Bỏ
                    </a>
                </div>
            </div>

            <?php if ($isEdit): ?>
                <div class="admin-card" style="background: #f8fafc;">
                    <h4 style="font-size: 0.9rem; font-weight: 700; color: #475569; margin: 0 0 0.5rem 0;">Thông tin lưu trữ</h4>
                    <div style="font-size: 0.82rem; color: #64748b; line-height: 1.6;">
                        <div>ID: <strong>#<?= (int)$category['id'] ?></strong></div>
                        <div>Xem trực tiếp: <a href="<?= url('/danh-muc/' . $category['slug']) ?>" target="_blank" style="color: #004098; font-weight: 600;">/danh-muc/<?= e($category['slug']) ?></a></div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const nameInput = document.getElementById('cat_name');
    const slugInput = document.getElementById('cat_slug');
    const iconInput = document.getElementById('cat_icon');
    const iconPreview = document.getElementById('icon-preview');
    const isEdit = <?= $isEdit ? 'true' : 'false' ?>;

    // Helper tạo slug tiếng Việt chuẩn
    function slugify(text) {
        return text.toString().toLowerCase()
            .normalize('NFD').replace(/[\u0300-\u036f]/g, '')
            .replace(/[đĐ]/g, 'd')
            .replace(/[^a-z0-9 -]/g, '')
            .replace(/\s+/g, '-')
            .replace(/-+/g, '-')
            .replace(/^-+/, '')
            .replace(/-+$/, '');
    }

    if (!isEdit && nameInput && slugInput) {
        nameInput.addEventListener('input', function() {
            if (!slugInput.dataset.touched) {
                slugInput.value = slugify(this.value);
            }
        });
        slugInput.addEventListener('input', function() {
            this.dataset.touched = 'true';
        });
    }

    // Chọn nhanh icon
    document.querySelectorAll('.btn-select-icon').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const iconName = this.dataset.icon;
            iconInput.value = iconName;
            const svg = this.querySelector('svg');
            if (svg && iconPreview) {
                iconPreview.innerHTML = svg.outerHTML;
            }
        });
    });
});
</script>
