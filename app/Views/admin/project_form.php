<?php
/** @var array|null $project */
$isEdit = !empty($project['id']);
?>

<div class="admin-card mb-4">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h2 style="font-size: 1.3rem; margin: 0; color: #0f172a;"><?= $isEdit ? 'Sửa Dự Án: ' . e($project['title']) : 'Thêm Dự Án Công Trình Mới' ?></h2>
            <p style="font-size: 0.85rem; color: #64748b; margin: 0.25rem 0 0 0;">
                Dự án sẽ được đồng bộ hiển thị lên trang <strong>/du-an</strong> trên toàn hệ thống website.
            </p>
        </div>
        <a href="<?= url('/admin/du-an') ?>" class="btn btn--outline">&larr; Quay lại danh sách</a>
    </div>
</div>

<form method="post" action="<?= $isEdit ? url('/admin/du-an/sua/' . $project['id']) : url('/admin/du-an/them') ?>" enctype="multipart/form-data" class="admin-card">
    <?= csrf_field() ?>

    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 2rem;">
        <div>
            <div class="form-group mb-3">
                <label class="form-label">Tên dự án công trình <span class="text-danger">*</span></label>
                <input type="text" name="title" value="<?= e($project['title'] ?? '') ?>" class="form-control" placeholder="Ví dụ: Chung cư Masteri Centre Point (TP. Thủ Đức, TP.HCM)" required>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;" class="mb-3">
                <div class="form-group">
                    <label class="form-label">Chủ đầu tư / Nhà thầu chính</label>
                    <input type="text" name="client_name" value="<?= e($project['client_name'] ?? '') ?>" class="form-control" placeholder="Tập đoàn Masterise Homes">
                </div>

                <div class="form-group">
                    <label class="form-label">Vị trí dự án (TP.HCM / Long An) <span class="text-danger">*</span></label>
                    <input type="text" name="location" value="<?= e($project['location'] ?? '') ?>" class="form-control" placeholder="KĐT Grand Park, TP. Thủ Đức, TP.HCM" required>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;" class="mb-3">
                <div class="form-group">
                    <label class="form-label">Phân loại công trình</label>
                    <select name="category" class="form-control">
                        <?php
                        $cats = ['Căn hộ cao cấp', 'Khu đô thị sinh thái', 'Nhà xưởng & KCN', 'Tòa nhà văn phòng', 'Bệnh viện & Y tế', 'Biệt thự & Dinh thự'];
                        $currentCat = $project['category'] ?? 'Căn hộ cao cấp';
                        foreach ($cats as $c):
                        ?>
                            <option value="<?= e($c) ?>" <?= $currentCat === $c ? 'selected' : '' ?>><?= e($c) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Quy mô công trình</label>
                    <input type="text" name="scale" value="<?= e($project['scale'] ?? '') ?>" class="form-control" placeholder="10 block, 5.000 căn hộ">
                </div>
            </div>

            <div class="form-group mb-3">
                <label class="form-label">Hạng mục thiết bị Panasonic cung cấp <span class="text-danger">*</span></label>
                <textarea name="supplied_items" class="form-control" rows="3" placeholder="100% MCB/RCBO Din-rail, Tủ điện âm tường Panasonic, Công tắc Moderva chống bám vân tay..." required><?= e($project['supplied_items'] ?? '') ?></textarea>
            </div>

            <div class="form-group mb-3">
                <label class="form-label">Mô tả chi tiết / Ghi chú kỹ thuật</label>
                <textarea name="description" class="form-control" rows="4" placeholder="Mô tả quá trình cấp hàng hỏa tốc, chứng nhận CO/CQ xuất xưởng..."><?= e($project['description'] ?? '') ?></textarea>
            </div>
        </div>

        <div>
            <div class="form-group mb-3">
                <label class="form-label">Hình ảnh thực tế dự án</label>
                <?php if (!empty($project['thumbnail'])): ?>
                    <div style="margin-bottom: 0.75rem;">
                        <img src="<?= media($project['thumbnail']) ?>" alt="Thumbnail" style="width: 100%; height: 160px; object-fit: cover; border-radius: 6px; border: 1px solid #e2e8f0;">
                    </div>
                <?php endif; ?>
                <input type="file" name="thumbnail" accept="image/*" class="form-control">
                <small class="text-muted" style="display:block; margin-top:0.25rem;">Hỗ trợ: JPG, PNG, WEBP. Dung lượng tối đa 5MB.</small>
            </div>

            <div class="form-group mb-3">
                <label class="form-label">Năm hoàn thành / bàn giao</label>
                <input type="number" name="completion_year" value="<?= e((string)($project['completion_year'] ?? date('Y'))) ?>" class="form-control" min="2015" max="2030">
            </div>

            <div class="form-group mb-3">
                <label class="form-label">Thứ tự hiển thị (ưu tiên số nhỏ)</label>
                <input type="number" name="sort_order" value="<?= e((string)($project['sort_order'] ?? 10)) ?>" class="form-control" min="0">
            </div>

            <div class="form-group mb-3">
                <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                    <input type="checkbox" name="is_featured" value="1" <?= !empty($project['is_featured']) ? 'checked' : '' ?>>
                    <span>Đặt làm Dự Án Tiêu Biểu Nổi Bật</span>
                </label>
            </div>

            <div class="form-group mb-4">
                <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                    <input type="checkbox" name="status" value="1" <?= (!isset($project['status']) || (int)$project['status'] === 1) ? 'checked' : '' ?>>
                    <span>Hiển thị dự án lên Website</span>
                </label>
            </div>

            <div style="display: flex; gap: 0.5rem;">
                <button type="submit" class="btn btn--primary" style="flex: 1; padding: 0.65rem 1rem;">
                    <?= $isEdit ? 'Lưu cập nhật' : 'Thêm dự án' ?>
                </button>
            </div>
        </div>
    </div>
</form>
