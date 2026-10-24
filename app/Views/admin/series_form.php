<?php
/** @var array $series */
$isEdit = !empty($series['id']);
$currentColor = $series['color'] ?? '#004098';
$presets = [
    '#004098' => 'Panasonic Blue',
    '#0f172a' => 'Black Matte',
    '#475569' => 'Titanium Grey',
    '#d97706' => 'Champagne Gold',
    '#059669' => 'Emerald Green',
    '#2563eb' => 'Electric Blue',
    '#dc2626' => 'Ruby Red'
];
?>

<div class="admin-page-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
    <div>
        <div style="font-size: 0.85rem; color: #64748b; margin-bottom: 0.25rem;">
            <a href="<?= url('/admin/dong-series') ?>" style="color: #64748b; text-decoration: none;">Dòng Series</a> &rsaquo;
            <span><?= $isEdit ? 'Chỉnh sửa' : 'Thêm mới' ?></span>
        </div>
        <h1 style="font-size: 1.5rem; font-weight: 700; color: #0f172a; margin: 0;">
            <?= $isEdit ? 'Chỉnh Sửa Dòng Series: ' . e($series['name']) : 'Thêm Dòng Series Thiết Bị Mới' ?>
        </h1>
    </div>
    <div>
        <a href="<?= url('/admin/dong-series') ?>" class="btn btn--outline" style="padding: 0.55rem 1rem;">
            &larr; Quay lại danh sách
        </a>
    </div>
</div>

<form method="post" action="<?= $isEdit ? url('/admin/dong-series/sua/' . $series['id']) : url('/admin/dong-series/them') ?>">
    <?= csrf_field() ?>

    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem; align-items: start;">
        <!-- Cột trái: Thông tin dòng sản phẩm -->
        <div class="admin-card" style="display: flex; flex-direction: column; gap: 1.25rem;">
            <div>
                <label class="field__label">Tên dòng Series thiết bị <span style="color: #ef4444;">*</span></label>
                <input type="text" id="series_name" name="name" value="<?= e($series['name'] ?? '') ?>" placeholder="Ví dụ: Moderva, Gen-X, Halumie, Wide Series..." required style="width: 100%;">
                <div style="font-size: 0.8rem; color: #64748b; margin-top: 0.35rem;">Tên định danh dòng thiết bị cao cấp Panasonic.</div>
            </div>

            <div>
                <label class="field__label">Đường dẫn thân thiện (Slug) <span style="color: #ef4444;">*</span></label>
                <input type="text" id="series_slug" name="slug" value="<?= e($series['slug'] ?? '') ?>" placeholder="moderva-series" required style="width: 100%;">
                <div style="font-size: 0.8rem; color: #64748b; margin-top: 0.35rem;">URL phân loại sản phẩm theo dòng. Tự động sinh từ tên dòng.</div>
            </div>

            <div>
                <label class="field__label">Khẩu hiệu / Định vị sản phẩm (Tagline)</label>
                <input type="text" name="tagline" value="<?= e($series['tagline'] ?? '') ?>" placeholder="Ví dụ: Thiết kế phẳng tràn viền sang trọng, bề mặt chống bám vân tay đỉnh cao..." style="width: 100%;">
                <div style="font-size: 0.8rem; color: #64748b; margin-top: 0.35rem;">Slogan / USP xuất hiện trên các banner quảng bá bộ sưu tập.</div>
            </div>

            <div>
                <label class="field__label">Màu sắc chủ đạo nhận diện thương hiệu</label>
                <div style="display: flex; gap: 1rem; align-items: center; margin-bottom: 0.75rem;">
                    <input type="color" id="series_color_picker" value="<?= e($currentColor) ?>" style="width: 50px; height: 42px; border: 1px solid #cbd5e1; border-radius: 8px; cursor: pointer; padding: 2px; background: #fff;">
                    <input type="text" id="series_color_text" name="color" value="<?= e($currentColor) ?>" placeholder="#004098" style="width: 120px; font-family: monospace;">
                    <div id="color_badge_preview" style="padding: 0.4rem 0.85rem; border-radius: 6px; font-size: 0.85rem; font-weight: 600; color: #fff; background-color: <?= e($currentColor) ?>;">
                        Màu hiển thị
                    </div>
                </div>
                <!-- Màu gợi ý nhanh -->
                <div style="display: flex; flex-wrap: wrap; gap: 0.5rem; align-items: center;">
                    <span style="font-size: 0.8rem; color: #64748b;">Màu nhanh:</span>
                    <?php foreach ($presets as $hex => $lbl): ?>
                        <button type="button" class="btn-preset-color" data-color="<?= $hex ?>" title="<?= $lbl ?>" style="width: 24px; height: 24px; border-radius: 50%; background: <?= $hex ?>; border: 2px solid #fff; box-shadow: 0 0 0 1px #cbd5e1; cursor: pointer;"></button>
                    <?php endforeach; ?>
                </div>
            </div>

            <div>
                <label class="field__label">Đường dẫn tài liệu Catalogue (PDF / Link Drive)</label>
                <input type="url" name="catalog_url" value="<?= e($series['catalog_url'] ?? '') ?>" placeholder="https://example.com/catalogue/panasonic-moderva.pdf" style="width: 100%;">
                <div style="font-size: 0.8rem; color: #64748b; margin-top: 0.35rem;">Khách hàng và đại lý có thể bấm tải tài liệu kỹ thuật của dòng này.</div>
            </div>
        </div>

        <!-- Cột phải: Xuất bản & Trạng thái -->
        <div style="display: flex; flex-direction: column; gap: 1.5rem;">
            <div class="admin-card">
                <h3 style="font-size: 1.05rem; font-weight: 700; color: #0f172a; margin: 0 0 1rem 0; padding-bottom: 0.75rem; border-bottom: 1px solid #f1f5f9;">
                    Cấu Hình Xuất Bản
                </h3>

                <div style="margin-bottom: 1.25rem;">
                    <label class="field__label">Thứ tự ưu tiên hiển thị</label>
                    <input type="number" name="sort_order" value="<?= (int)($series['sort_order'] ?? 0) ?>" min="0" step="1" style="width: 100%;">
                    <div style="font-size: 0.78rem; color: #64748b; margin-top: 0.25rem;">Số nhỏ hơn hiển thị lên đầu (0, 1, 2...).</div>
                </div>

                <div style="margin-bottom: 1.25rem;">
                    <label class="field__label" style="margin-bottom: 0.5rem;">Trạng thái hiển thị</label>
                    <label style="display: flex; align-items: center; gap: 0.6rem; cursor: pointer; font-size: 0.9rem; color: #1e293b; font-weight: 500;">
                        <input type="checkbox" name="status" value="1" <?= (!isset($series['status']) || !empty($series['status'])) ? 'checked' : '' ?> style="width: 18px; height: 18px; accent-color: #004098;">
                        <span>Kích hoạt hiển thị website</span>
                    </label>
                    <div style="font-size: 0.78rem; color: #64748b; margin-top: 0.35rem; margin-left: 1.7rem;">
                        Nếu tắt, dòng này sẽ ẩn khỏi menu lọc và trang danh sách sản phẩm.
                    </div>
                </div>

                <div style="padding-top: 1rem; border-top: 1px solid #f1f5f9; display: flex; flex-direction: column; gap: 0.5rem;">
                    <button type="submit" class="btn btn--primary" style="width: 100%; padding: 0.75rem; font-weight: 700; justify-content: center; display: flex; align-items: center; gap: 0.5rem;">
                        <?= icon('check') ?> <?= $isEdit ? 'Lưu Thay Đổi' : 'Tạo Dòng Series Mới' ?>
                    </button>
                    <a href="<?= url('/admin/dong-series') ?>" class="btn btn--outline" style="width: 100%; padding: 0.6rem; text-align: center; justify-content: center;">
                        Hủy Bỏ
                    </a>
                </div>
            </div>

            <?php if ($isEdit): ?>
                <div class="admin-card" style="background: #f8fafc;">
                    <h4 style="font-size: 0.9rem; font-weight: 700; color: #475569; margin: 0 0 0.5rem 0;">Thông tin dòng</h4>
                    <div style="font-size: 0.82rem; color: #64748b; line-height: 1.6;">
                        <div>Mã Series: <strong>#<?= (int)$series['id'] ?></strong></div>
                        <div>Lọc sản phẩm: <a href="<?= url('/admin/san-pham?series_id=' . $series['id']) ?>" style="color: #004098; font-weight: 600;">Xem danh sách sản phẩm</a></div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const nameInput = document.getElementById('series_name');
    const slugInput = document.getElementById('series_slug');
    const colorPicker = document.getElementById('series_color_picker');
    const colorText = document.getElementById('series_color_text');
    const colorBadge = document.getElementById('color_badge_preview');
    const isEdit = <?= $isEdit ? 'true' : 'false' ?>;

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

    function syncColor(val) {
        colorPicker.value = val;
        colorText.value = val;
        if (colorBadge) {
            colorBadge.style.backgroundColor = val;
        }
    }

    if (colorPicker && colorText) {
        colorPicker.addEventListener('input', function() {
            syncColor(this.value);
        });
        colorText.addEventListener('input', function() {
            if (/^#[0-9A-Fa-f]{6}$/.test(this.value)) {
                syncColor(this.value);
            }
        });
    }

    document.querySelectorAll('.btn-preset-color').forEach(btn => {
        btn.addEventListener('click', function() {
            syncColor(this.dataset.color);
        });
    });
});
</script>
