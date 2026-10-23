<?php
/** @var array|null $product */
/** @var array $categories */
/** @var array $seriesList */
$isEdit = !empty($product);

// Giải mã an toàn specs (Chống Fatal Error PHP 8+)
$specs = [];
if ($isEdit && !empty($product['specs'])) {
    if (is_array($product['specs'])) {
        $specs = $product['specs'];
    } else {
        $decoded = json_decode((string)$product['specs'], true);
        $specs = is_array($decoded) ? $decoded : [];
    }
}

// Giải mã an toàn gallery
$gallery = [];
if ($isEdit && !empty($product['gallery'])) {
    if (is_array($product['gallery'])) {
        $gallery = $product['gallery'];
    } else {
        $decoded = json_decode((string)$product['gallery'], true);
        $gallery = is_array($decoded) ? $decoded : [];
    }
}
?>

<div style="max-width: 1280px; margin: 0 auto;">
    <!-- THANH TIÊU ĐỀ ĐẦU TRANG -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem; background: #ffffff; padding: 1.25rem 1.5rem; border-radius: 10px; border: 1px solid #e2e8f0; box-shadow: 0 1px 3px rgba(0,0,0,0.03);">
        <div>
            <div style="font-size: 0.8rem; color: #64748b; margin-bottom: 0.25rem;">
                <a href="<?= url('/admin/san-pham') ?>" style="color: #64748b; text-decoration: none;">Quản lý sản phẩm</a>
                <span> &rsaquo; </span>
                <span style="color: #004098; font-weight: 600;"><?= $isEdit ? 'Chỉnh sửa' : 'Thêm mới' ?></span>
            </div>
            <h1 style="font-size: 1.35rem; font-weight: 700; margin: 0; color: #0f172a; display: flex; align-items: center; gap: 0.65rem;">
                <?= $isEdit ? 'Chỉnh Sửa Sản Phẩm: <span style="color:#004098;">' . e($product['model_code']) . '</span>' : 'Thêm Sản Phẩm Mới' ?>
                <?php if ($isEdit): ?>
                    <span style="font-size: 0.75rem; background: #f1f5f9; color: #475569; padding: 2px 8px; border-radius: 4px; font-weight: 600;">ID #<?= (int)$product['id'] ?></span>
                <?php endif; ?>
            </h1>
        </div>

        <div style="display: flex; gap: 0.5rem; align-items: center;">
            <?php if ($isEdit && !empty($product['slug'])): ?>
                <a href="<?= url('/san-pham/' . $product['slug']) ?>" target="_blank" class="btn btn--sm" style="background: #eff6ff; color: #004098; border: 1px solid #bfdbfe; font-weight: 600; display: flex; align-items: center; gap: 0.35rem;">
                    👁 Xem trên web
                </a>
            <?php endif; ?>
            <a href="<?= url('/admin/san-pham') ?>" class="btn btn--outline btn--sm" style="color: #475569; border-color: #cbd5e1;">
                &larr; Quay lại danh sách
            </a>
        </div>
    </div>

    <form action="<?= $isEdit ? url('/admin/san-pham/sua/' . $product['id']) : url('/admin/san-pham/them') ?>" method="post" enctype="multipart/form-data">
        <?= csrf_field() ?>

        <!-- BỐ CỤC 2 CỘT HIỆN ĐẠI (CHÍNH - SIDEBAR) -->
        <div class="product-form-grid" style="display: grid; grid-template-columns: minmax(0, 1fr) 380px; gap: 1.5rem; align-items: start;">

            <!-- ============================================== -->
            <!-- CỘT TRÁI: NỘI DUNG CHÍNH, BÀI VIẾT & THÔNG SỐ -->
            <!-- ============================================== -->
            <div style="display: flex; flex-direction: column; gap: 1.5rem;">

                <!-- 1. THÔNG TIN ĐỊNH DANH -->
                <div class="admin-card" style="padding: 1.5rem;">
                    <div style="border-bottom: 1.5px solid #f1f5f9; padding-bottom: 0.75rem; margin-bottom: 1.25rem;">
                        <h2 style="font-size: 1.05rem; font-weight: 700; color: #004098; margin: 0; display: flex; align-items: center; gap: 0.5rem;">
                            📌 1. Thông Tin Cơ Bản & Định Danh Sản Phẩm
                        </h2>
                    </div>

                    <div style="display: flex; flex-direction: column; gap: 1rem;">
                        <div>
                            <label class="field__label">Tên sản phẩm đầy đủ <span style="color:#ef4444;">*</span></label>
                            <input type="text" name="name" required value="<?= old('name', $product['name'] ?? '') ?>" placeholder="VD: Aptomat MCB 4P 32A 6kA Panasonic BBD4324CHHV" style="width: 100%; height: 42px; font-weight: 600;">
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                            <div>
                                <label class="field__label">Mã Model Panasonic <span style="color:#ef4444;">*</span></label>
                                <input type="text" name="model_code" required value="<?= old('model_code', $product['model_code'] ?? '') ?>" placeholder="VD: BBD4324CHHV" style="width: 100%; text-transform: uppercase;">
                            </div>
                            <div>
                                <label class="field__label">Mã SKU Quản Lý <span style="color:#ef4444;">*</span></label>
                                <input type="text" name="sku" required value="<?= old('sku', $product['sku'] ?? '') ?>" placeholder="VD: PN-MCB-4P-32A" style="width: 100%; text-transform: uppercase;">
                            </div>
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                            <div>
                                <label class="field__label">Danh mục thiết bị <span style="color:#ef4444;">*</span></label>
                                <select name="category_id" required style="width: 100%; height: 42px;">
                                    <option value="">-- Chọn danh mục --</option>
                                    <?php foreach ($categories as $cat): ?>
                                        <option value="<?= $cat['id'] ?>" <?= (int)old('category_id', $product['category_id'] ?? 0) === (int)$cat['id'] ? 'selected' : '' ?>><?= e($cat['name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div>
                                <label class="field__label">Dòng Series (Công tắc / Ổ cắm)</label>
                                <select name="series_id" style="width: 100%; height: 42px;">
                                    <option value="">-- Thiết bị đóng cắt / Không thuộc series --</option>
                                    <?php foreach ($seriesList as $ser): ?>
                                        <option value="<?= $ser['id'] ?>" <?= (int)old('series_id', $product['series_id'] ?? 0) === (int)$ser['id'] ? 'selected' : '' ?>><?= e($ser['name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="field__label">Đường dẫn tĩnh (Slug URL) <small style="color:#64748b; font-weight:normal;">(Để trống hệ thống sẽ tự động tạo từ tên)</small></label>
                            <input type="text" name="slug" value="<?= old('slug', $product['slug'] ?? '') ?>" placeholder="VD: aptomat-mcb-4p-32a-6ka-bbd4324chhv" style="width: 100%; color: #475569;">
                        </div>
                    </div>
                </div>

                <!-- 2. BÀI VIẾT MÔ TẢ & HƯỚNG DẪN TRANG CHI TIẾT -->
                <div class="admin-card" style="padding: 1.5rem;">
                    <div style="border-bottom: 1.5px solid #f1f5f9; padding-bottom: 0.75rem; margin-bottom: 1.25rem;">
                        <h2 style="font-size: 1.05rem; font-weight: 700; color: #004098; margin: 0; display: flex; align-items: center; gap: 0.5rem;">
                            📝 2. Mô Tả Sản Phẩm & Bài Viết Chi Tiết
                        </h2>
                    </div>

                    <div style="display: flex; flex-direction: column; gap: 1.25rem;">
                        <div>
                            <label class="field__label">Mô tả tóm tắt đầu trang <small style="color:#64748b; font-weight:normal;">(1-2 câu điểm nổi bật)</small></label>
                            <textarea name="short_desc" rows="2" style="width: 100%; resize: vertical;" placeholder="VD: Aptomat tép MCB Panasonic chính hãng bảo vệ quá tải và ngắn mạch tiêu chuẩn Nhật Bản..."><?= old('short_desc', $product['short_desc'] ?? '') ?></textarea>
                        </div>

                        <div>
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem; flex-wrap: wrap; gap: 0.5rem;">
                                <label class="field__label" style="margin: 0;">Nội dung bài viết chi tiết & Hướng dẫn lắp đặt</label>
                                <div style="display: flex; gap: 4px; flex-wrap: wrap;">
                                    <button type="button" class="tool-btn" onclick="insertTag('<h2>', '</h2>')">H2 Tiêu đề</button>
                                    <button type="button" class="tool-btn" onclick="insertTag('<h3>', '</h3>')">H3 Mục con</button>
                                    <button type="button" class="tool-btn" onclick="insertTag('<strong>', '</strong>')"><b>B</b> In đậm</button>
                                    <button type="button" class="tool-btn" onclick="insertTag('<ul>\n  <li>', '</li>\n</ul>')">• Danh sách</button>
                                    <button type="button" class="tool-btn" onclick="insertSampleTable()">📊 Bảng mẫu</button>
                                    <button type="button" class="tool-btn" onclick="insertTipBox()">💡 Hộp lưu ý</button>
                                </div>
                            </div>
                            <textarea name="description" id="productDescriptionEditor" rows="14" style="width: 100%; font-family: Consolas, Monaco, monospace; font-size: 0.88rem; line-height: 1.5; resize: vertical;" placeholder="Nhập bài viết mô tả chi tiết, ứng dụng thực tế và hướng dẫn lắp đặt tiêu chuẩn..."><?= old('description', $product['description'] ?? '') ?></textarea>
                        </div>
                    </div>
                </div>

                <!-- 3. BẢNG THÔNG SỐ KỸ THUẬT CHI TIẾT (SPECS) -->
                <div class="admin-card" style="padding: 1.5rem;">
                    <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1.5px solid #f1f5f9; padding-bottom: 0.75rem; margin-bottom: 1.25rem; flex-wrap: wrap; gap: 0.5rem;">
                        <div>
                            <h2 style="font-size: 1.05rem; font-weight: 700; color: #004098; margin: 0; display: flex; align-items: center; gap: 0.5rem;">
                                📋 3. Bảng Thông Số Kỹ Thuật Động (Chi Tiết Từng Mục)
                            </h2>
                            <p style="margin: 0.2rem 0 0; font-size: 0.8rem; color: #64748b;">
                                Hiển thị ở bảng thông số kỹ thuật tại tab "Mô Tả & Thông Số Kỹ Thuật" ngoài website.
                            </p>
                        </div>
                        <div style="display: flex; gap: 0.4rem;">
                            <button type="button" class="btn btn--sm" onclick="addPresetSpecs()" style="background: #f0fdf4; color: #166534; border: 1px solid #bbf7d0; font-weight: 600;">
                                + Thêm mẫu Panasonic
                            </button>
                            <button type="button" class="btn btn--sm" id="btnAddSpec" style="background: #004098; color: #fff; font-weight: 600;">
                                + Thêm dòng
                            </button>
                        </div>
                    </div>

                    <div id="specContainer" style="display: flex; flex-direction: column; gap: 0.5rem;">
                        <?php if (!empty($specs)): ?>
                            <?php foreach ($specs as $k => $v): ?>
                                <div class="spec-row" style="display: grid; grid-template-columns: 240px 1fr 40px; gap: 0.5rem; align-items: center;">
                                    <input type="text" name="spec_key[]" value="<?= e($k) ?>" placeholder="Tên thông số (VD: Tiêu chuẩn)" style="width: 100%; font-weight: 600;">
                                    <input type="text" name="spec_value[]" value="<?= e($v) ?>" placeholder="Giá trị (VD: IEC 60898-1)" style="width: 100%;">
                                    <button type="button" onclick="this.closest('.spec-row').remove()" style="background: #fee2e2; color: #b91c1c; border: 1px solid #fca5a5; border-radius: 6px; height: 38px; cursor: pointer; display: flex; align-items: center; justify-content: center; font-weight: 700;" title="Xóa dòng">✕</button>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="spec-row" style="display: grid; grid-template-columns: 240px 1fr 40px; gap: 0.5rem; align-items: center;">
                                <input type="text" name="spec_key[]" value="Điện áp định mức" placeholder="Tên thông số" style="width: 100%; font-weight: 600;">
                                <input type="text" name="spec_value[]" value="240VAC / 415VAC" placeholder="Giá trị" style="width: 100%;">
                                <button type="button" onclick="this.closest('.spec-row').remove()" style="background: #fee2e2; color: #b91c1c; border: 1px solid #fca5a5; border-radius: 6px; height: 38px; cursor: pointer; display: flex; align-items: center; justify-content: center; font-weight: 700;">✕</button>
                            </div>
                            <div class="spec-row" style="display: grid; grid-template-columns: 240px 1fr 40px; gap: 0.5rem; align-items: center;">
                                <input type="text" name="spec_key[]" value="Tiêu chuẩn chất lượng" placeholder="Tên thông số" style="width: 100%; font-weight: 600;">
                                <input type="text" name="spec_value[]" value="IEC 60898-1, JIS C 8370" placeholder="Giá trị" style="width: 100%;">
                                <button type="button" onclick="this.closest('.spec-row').remove()" style="background: #fee2e2; color: #b91c1c; border: 1px solid #fca5a5; border-radius: 6px; height: 38px; cursor: pointer; display: flex; align-items: center; justify-content: center; font-weight: 700;">✕</button>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

            </div>

            <!-- ============================================== -->
            <!-- CỘT PHẢI (SIDEBAR): HÀNH ĐỘNG, GIÁ, ẢNH & FILE -->
            <!-- ============================================== -->
            <div style="display: flex; flex-direction: column; gap: 1.5rem;">

                <!-- 1. HÀNH ĐỘNG LƯU & XUẤT BẢN (STICKY TOP) -->
                <div class="admin-card" style="padding: 1.5rem; position: sticky; top: 1rem; z-index: 10; border-top: 4px solid #004098;">
                    <div style="border-bottom: 1.5px solid #f1f5f9; padding-bottom: 0.75rem; margin-bottom: 1rem;">
                        <h2 style="font-size: 1rem; font-weight: 700; color: #0f172a; margin: 0;">
                            ⚙️ Trạng Thái & Xuất Bản
                        </h2>
                    </div>

                    <div style="display: flex; flex-direction: column; gap: 0.75rem; margin-bottom: 1.25rem;">
                        <label style="display: flex; align-items: flex-start; gap: 0.6rem; cursor: pointer; background: #f8fafc; padding: 0.65rem 0.75rem; border-radius: 6px; border: 1px solid #e2e8f0;">
                            <input type="checkbox" name="status" value="1" <?= (!isset($product['status']) || $product['status'] == 1) ? 'checked' : '' ?> style="margin-top: 3px;">
                            <div>
                                <span style="font-size: 0.88rem; font-weight: 700; color: #059669;">Kích hoạt kinh doanh</span>
                                <small style="display: block; color: #64748b; font-size: 0.75rem;">Sản phẩm hiển thị công khai trên website</small>
                            </div>
                        </label>

                        <label style="display: flex; align-items: flex-start; gap: 0.6rem; cursor: pointer; background: #f8fafc; padding: 0.65rem 0.75rem; border-radius: 6px; border: 1px solid #e2e8f0;">
                            <input type="checkbox" name="is_featured" value="1" <?= (!empty($product['is_featured'])) ? 'checked' : '' ?> style="margin-top: 3px;">
                            <div>
                                <span style="font-size: 0.88rem; font-weight: 700; color: #d97706;">Sản phẩm nổi bật</span>
                                <small style="display: block; color: #64748b; font-size: 0.75rem;">Hiển thị ở trang chủ & danh mục ưu tiên</small>
                            </div>
                        </label>
                    </div>

                    <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                        <button type="submit" class="btn btn--lg" style="width: 100%; background: #004098; color: #ffffff; border: none; font-weight: 700; padding: 0.75rem 1rem; border-radius: 8px; font-size: 1rem; box-shadow: 0 4px 12px rgba(0,64,152,0.25); cursor: pointer;">
                            💾 <?= $isEdit ? 'Lưu Toàn Bộ Cập Nhật' : 'Tạo Sản Phẩm Mới' ?>
                        </button>
                        <a href="<?= url('/admin/san-pham') ?>" class="btn btn--outline btn--sm" style="text-align: center; color: #64748b; border-color: #cbd5e1;">
                            Hủy bỏ và quay lại
                        </a>
                    </div>
                </div>

                <!-- 2. GIÁ BÁN & TỒN KHO -->
                <div class="admin-card" style="padding: 1.5rem;">
                    <div style="border-bottom: 1.5px solid #f1f5f9; padding-bottom: 0.75rem; margin-bottom: 1rem;">
                        <h2 style="font-size: 1rem; font-weight: 700; color: #004098; margin: 0;">
                            💰 Giá Bán & Kho Hàng
                        </h2>
                    </div>

                    <div style="display: flex; flex-direction: column; gap: 0.9rem;">
                        <div>
                            <label class="field__label">Giá niêm yết bán lẻ (VNĐ) <span style="color:#ef4444;">*</span></label>
                            <input type="number" name="price" required min="0" step="500" value="<?= old('price', $product['price'] ?? 0) ?>" style="width: 100%; font-weight: 700; font-size: 1.05rem; color: #004098;">
                        </div>

                        <div>
                            <label class="field__label">Giá chiết khấu dự án / Công trình (VNĐ)</label>
                            <input type="number" name="wholesale_price" min="0" step="500" value="<?= old('wholesale_price', $product['wholesale_price'] ?? 0) ?>" placeholder="0" style="width: 100%; font-weight: 600; color: #059669;">
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                            <div>
                                <label class="field__label">Số lượng giá sỉ</label>
                                <input type="number" name="wholesale_min_qty" min="1" value="<?= old('wholesale_min_qty', $product['wholesale_min_qty'] ?? 10) ?>" style="width: 100%;">
                            </div>
                            <div>
                                <label class="field__label">Tồn kho</label>
                                <input type="number" name="stock" min="0" value="<?= old('stock', $product['stock'] ?? 100) ?>" style="width: 100%; font-weight: 600;">
                            </div>
                        </div>

                        <div>
                            <label class="field__label">Đơn vị tính</label>
                            <input type="text" name="unit" value="<?= old('unit', $product['unit'] ?? 'Cái') ?>" placeholder="Cái, Bộ, Hộp..." style="width: 100%;">
                        </div>
                    </div>
                </div>

                <!-- 3. THUỘC TÍNH BỘ LỌC KỸ THUẬT -->
                <div class="admin-card" style="padding: 1.5rem;">
                    <div style="border-bottom: 1.5px solid #f1f5f9; padding-bottom: 0.75rem; margin-bottom: 1rem;">
                        <h2 style="font-size: 1rem; font-weight: 700; color: #004098; margin: 0;">
                            ⚡ Thuộc Tính Bộ Lọc Sản Phẩm
                        </h2>
                    </div>

                    <div style="display: flex; flex-direction: column; gap: 0.9rem;">
                        <div>
                            <label class="field__label">Số cực (Poles) <small style="color:#64748b;">(Thiết bị đóng cắt)</small></label>
                            <input type="number" name="poles" min="1" max="4" value="<?= old('poles', $product['poles'] ?? '') ?>" placeholder="VD: 1, 2, 3, 4" style="width: 100%;">
                        </div>

                        <div>
                            <label class="field__label">Dòng điện định mức In (A)</label>
                            <input type="number" name="rated_current" step="0.1" value="<?= old('rated_current', $product['rated_current'] ?? '') ?>" placeholder="VD: 6, 16, 20, 32, 40..." style="width: 100%;">
                        </div>

                        <div>
                            <label class="field__label">Dòng cắt ngắn mạch (kA) <small style="color:#64748b;">(MCB/RCBO)</small></label>
                            <input type="number" name="breaking_capacity" step="0.1" value="<?= old('breaking_capacity', $product['breaking_capacity'] ?? '') ?>" placeholder="VD: 4.5, 6, 10" style="width: 100%;">
                        </div>
                    </div>
                </div>

                <!-- 4. HÌNH ẢNH & GALLERY BỘ SƯU TẬP -->
                <div class="admin-card" style="padding: 1.5rem;">
                    <div style="border-bottom: 1.5px solid #f1f5f9; padding-bottom: 0.75rem; margin-bottom: 1rem;">
                        <h2 style="font-size: 1rem; font-weight: 700; color: #004098; margin: 0;">
                            🖼️ Hình Ảnh & Gallery Chi Tiết
                        </h2>
                    </div>

                    <!-- 4.1 Thumbnail -->
                    <div style="margin-bottom: 1.25rem;">
                        <label class="field__label" style="font-weight: 700;">Ảnh đại diện chính (Thumbnail) <span style="color:#ef4444;">*</span></label>
                        
                        <div style="display: flex; justify-content: center; margin-bottom: 0.75rem;">
                            <div style="width: 130px; height: 130px; border: 2px dashed #cbd5e1; border-radius: 10px; padding: 4px; background: #f8fafc; display: flex; align-items: center; justify-content: center; overflow: hidden;">
                                <?php if (!empty($product['thumbnail'])): ?>
                                    <img id="thumbPreview" src="<?= media($product['thumbnail']) ?>" alt="Thumbnail" style="width: 100%; height: 100%; object-fit: contain;">
                                <?php else: ?>
                                    <img id="thumbPreview" src="" alt="Thumbnail" style="display: none; width: 100%; height: 100%; object-fit: contain;">
                                    <span id="thumbPlaceholder" style="font-size: 0.75rem; color: #94a3b8; text-align: center;">Chưa có ảnh</span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div style="margin-bottom: 0.5rem;">
                            <span style="font-size: 0.8rem; color: #475569; display: block; margin-bottom: 3px;">Tải file ảnh từ máy:</span>
                            <input type="file" name="thumbnail" accept="image/*" onchange="previewSelectedThumb(this)" style="width: 100%;">
                        </div>

                        <div>
                            <span style="font-size: 0.8rem; color: #475569; display: block; margin-bottom: 3px;">Hoặc dán URL ảnh trực tiếp:</span>
                            <input type="url" name="thumbnail_url" value="<?= (!empty($product['thumbnail']) && (str_starts_with($product['thumbnail'], 'http://') || str_starts_with($product['thumbnail'], 'https://'))) ? e($product['thumbnail']) : '' ?>" placeholder="https://..." oninput="previewUrlThumb(this.value)" style="width: 100%;">
                        </div>
                    </div>

                    <!-- 4.2 Gallery nhiều ảnh -->
                    <div style="border-top: 1px dashed #e2e8f0; padding-top: 1rem;">
                        <label class="field__label" style="font-weight: 700;">Bộ sưu tập ảnh chi tiết (Gallery)</label>
                        
                        <!-- Grid ảnh gallery hiện tại -->
                        <?php if (!empty($gallery)): ?>
                            <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 0.5rem; margin-bottom: 0.75rem;">
                                <?php foreach ($gallery as $imgUrl): ?>
                                    <div class="gallery-thumb-item" style="position: relative; aspect-ratio: 1; border: 1px solid #cbd5e1; border-radius: 6px; padding: 2px; background: #fff;">
                                        <img src="<?= media($imgUrl) ?>" alt="Gallery" style="width: 100%; height: 100%; object-fit: contain;">
                                        <input type="hidden" name="existing_gallery[]" value="<?= e($imgUrl) ?>">
                                        <button type="button" onclick="this.closest('.gallery-thumb-item').remove()" style="position: absolute; top: -5px; right: -5px; width: 18px; height: 18px; border-radius: 50%; background: #ef4444; color: #fff; border: none; font-size: 10px; cursor: pointer; display: flex; align-items: center; justify-content: center;" title="Xóa ảnh này">✕</button>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                        <div style="margin-bottom: 0.5rem;">
                            <span style="font-size: 0.8rem; color: #475569; display: block; margin-bottom: 3px;">Chọn nhiều file ảnh tải lên:</span>
                            <input type="file" name="gallery_files[]" multiple accept="image/*" style="width: 100%;">
                        </div>

                        <div>
                            <span style="font-size: 0.8rem; color: #475569; display: block; margin-bottom: 3px;">Hoặc dán nhiều link ảnh (mỗi dòng 1 link):</span>
                            <textarea name="gallery_urls" rows="2" placeholder="https://.../anh-1.jpg&#10;https://.../anh-2.jpg" style="width: 100%; font-size: 0.8rem;"></textarea>
                        </div>
                    </div>
                </div>

                <!-- 5. TÀI LIỆU CATALOGUE PDF -->
                <div class="admin-card" style="padding: 1.5rem;">
                    <div style="border-bottom: 1.5px solid #f1f5f9; padding-bottom: 0.75rem; margin-bottom: 1rem;">
                        <h2 style="font-size: 1rem; font-weight: 700; color: #004098; margin: 0;">
                            📄 File Catalogue & Bản Vẽ PDF
                        </h2>
                    </div>

                    <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                        <div>
                            <span style="font-size: 0.8rem; color: #475569; display: block; margin-bottom: 3px;">Tải file PDF từ máy:</span>
                            <input type="file" name="pdf_file" accept=".pdf" style="width: 100%;">
                            <?php if (!empty($product['pdf_catalog_url'])): ?>
                                <div style="margin-top: 0.35rem; font-size: 0.8rem;">
                                    <span style="color: #059669; font-weight: 600;">✓ Có file:</span>
                                    <a href="<?= asset($product['pdf_catalog_url']) ?>" target="_blank" style="color: #004098; text-decoration: underline;">Xem file PDF</a>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div>
                            <span style="font-size: 0.8rem; color: #475569; display: block; margin-bottom: 3px;">Hoặc link file PDF online:</span>
                            <input type="text" name="pdf_catalog_url" value="<?= old('pdf_catalog_url', $product['pdf_catalog_url'] ?? '') ?>" placeholder="https://.../catalog.pdf" style="width: 100%;">
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </form>
</div>

<style>
/* Nút công cụ soạn thảo nhỏ gọn */
.tool-btn {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 5px;
    padding: 3px 8px;
    font-size: 0.78rem;
    font-weight: 600;
    color: #334155;
    cursor: pointer;
    transition: all 0.15s ease;
}
.tool-btn:hover {
    background: #f1f5f9;
    border-color: #94a3b8;
    color: #0f172a;
}

/* Responsive khi màn hình nhỏ */
@media (max-width: 1024px) {
    .product-form-grid {
        grid-template-columns: 1fr !important;
    }
}
</style>

<script>
// Xem trước ảnh khi chọn file
function previewSelectedThumb(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const img = document.getElementById('thumbPreview');
            const ph = document.getElementById('thumbPlaceholder');
            if (img) {
                img.src = e.target.result;
                img.style.display = 'block';
            }
            if (ph) ph.style.display = 'none';
        }
        reader.readAsDataURL(input.files[0]);
    }
}

// Xem trước ảnh khi nhập URL
function previewUrlThumb(url) {
    url = url.trim();
    const img = document.getElementById('thumbPreview');
    const ph = document.getElementById('thumbPlaceholder');
    if (url.length > 5) {
        if (img) {
            img.src = url;
            img.style.display = 'block';
        }
        if (ph) ph.style.display = 'none';
    }
}

// Thêm dòng thông số kỹ thuật mới
document.getElementById('btnAddSpec')?.addEventListener('click', function() {
    addSpecRow('', '');
});

function addSpecRow(key = '', val = '') {
    const wrap = document.getElementById('specContainer');
    const row = document.createElement('div');
    row.className = 'spec-row';
    row.style.cssText = 'display: grid; grid-template-columns: 240px 1fr 40px; gap: 0.5rem; align-items: center;';
    row.innerHTML = `
        <input type="text" name="spec_key[]" value="${escapeHtml(key)}" placeholder="Tên thông số" style="width: 100%; font-weight: 600;">
        <input type="text" name="spec_value[]" value="${escapeHtml(val)}" placeholder="Giá trị" style="width: 100%;">
        <button type="button" onclick="this.closest('.spec-row').remove()" style="background: #fee2e2; color: #b91c1c; border: 1px solid #fca5a5; border-radius: 6px; height: 38px; cursor: pointer; display: flex; align-items: center; justify-content: center; font-weight: 700;">✕</button>
    `;
    wrap.appendChild(row);
}

function addPresetSpecs() {
    const presets = [
        ['Điện áp định mức', '240VAC / 415VAC - 50/60Hz'],
        ['Tiêu chuẩn chất lượng', 'IEC 60898-1, IEC 60947-2, JIS C 8370'],
        ['Tuổi thọ cơ học & điện', '≥ 10.000 lần ngắt mạch / ≥ 40.000 lần bật tắt'],
        ['Xuất xứ sản phẩm', 'Chính hãng Panasonic (Việt Nam / Thái Lan / Nhật Bản)'],
        ['Quy cách đóng gói', '1 Cái / Hộp (Kèm hướng dẫn & tem niêm phong)'],
        ['Chế độ bảo hành', '24 tháng chính hãng đổi mới nếu lỗi kỹ thuật']
    ];
    presets.forEach(p => addSpecRow(p[0], p[1]));
}

// Chèn tag HTML nhanh vào trình soạn thảo mô tả
function insertTag(openTag, closeTag) {
    const editor = document.getElementById('productDescriptionEditor');
    if (!editor) return;
    const start = editor.selectionStart;
    const end = editor.selectionEnd;
    const selected = editor.value.substring(start, end);
    const replacement = openTag + (selected || 'Nội dung') + closeTag;
    editor.value = editor.value.substring(0, start) + replacement + editor.value.substring(end);
    editor.focus();
}

function insertSampleTable() {
    const tableHtml = `\n<table class="specs-modern-table">\n  <thead>\n    <tr><th>Đặc tính kỹ thuật</th><th>Thông số tiêu chuẩn</th></tr>\n  </thead>\n  <tbody>\n    <tr><td>Vật liệu thân vỏ</td><td>Nhựa Urea Resin chống cháy cao cấp</td></tr>\n    <tr><td>Tiếp điểm dập hồ quang</td><td>Hợp kim bạc AgNi nguyên chất</td></tr>\n  </tbody>\n</table>\n`;
    const editor = document.getElementById('productDescriptionEditor');
    if (!editor) return;
    const start = editor.selectionStart;
    editor.value = editor.value.substring(0, start) + tableHtml + editor.value.substring(start);
    editor.focus();
}

function insertTipBox() {
    const tipHtml = `\n<div style="background:#eff6ff; border-left:4px solid #004098; padding:12px 16px; border-radius:6px; margin:14px 0;">\n  <strong style="color:#004098;">Lưu ý an toàn khi thi công:</strong>\n  <p style="margin:4px 0 0; color:#334155;">Ngắt hoàn toàn nguồn điện trước khi đấu nối dây và siết chặt các ốc vít theo đúng lực siết tiêu chuẩn.</p>\n</div>\n`;
    const editor = document.getElementById('productDescriptionEditor');
    if (!editor) return;
    const start = editor.selectionStart;
    editor.value = editor.value.substring(0, start) + tipHtml + editor.value.substring(start);
    editor.focus();
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}
</script>
