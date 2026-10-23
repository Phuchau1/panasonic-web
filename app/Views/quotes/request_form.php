<?php
/** @var array $customerTypes */
/** @var int $cartCount */
?>

<!-- ==================== HERO BANNER DOANH NGHIỆP VỚI HÌNH ẢNH THỰC TẾ ==================== -->
<section class="quote-corp-hero" style="position: relative; background: url('<?= asset('uploads/banners/banner-dien-xay-dung.jpg') ?>') center center / cover no-repeat; color: #ffffff; padding: 3rem 0 2.5rem; border-bottom: 4px solid #004098;">
    <!-- Lớp phủ Gradient Doanh Nghiệp Panasonic chính hãng -->
    <div style="position: absolute; inset: 0; background: linear-gradient(90deg, rgba(0, 32, 77, 0.96) 0%, rgba(0, 48, 115, 0.90) 55%, rgba(0, 64, 152, 0.75) 100%);"></div>

    <div class="container" style="position: relative; z-index: 2;">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" style="margin-bottom: 1.25rem; font-size: 0.85rem;">
            <a href="<?= url('/') ?>" style="color: #93c5fd; text-decoration: none; display: inline-flex; align-items: center; gap: 0.35rem;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                Trang chủ
            </a>
            <span style="color: #60a5fa; margin: 0 0.5rem;">&rsaquo;</span>
            <span style="color: #ffffff; font-weight: 600;">Phòng Dự Án & Báo Giá Công Trình</span>
        </nav>

        <div style="max-width: 920px;">
            <div style="display: inline-flex; align-items: center; gap: 0.5rem; background: rgba(255,255,255,0.12); border: 1px solid rgba(255,255,255,0.25); color: #fef08a; padding: 5px 14px; border-radius: 20px; font-size: 0.8rem; font-weight: 700; margin-bottom: 1rem; letter-spacing: 0.5px; text-transform: uppercase;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
                TRUNG TÂM TIẾP NHẬN BÓC TÁCH VẬT TƯ & BÁO GIÁ DỰ ÁN PANASONIC VIỆT NAM
            </div>

            <h1 style="font-size: 2.35rem; font-weight: 800; line-height: 1.25; margin: 0 0 1rem; color: #ffffff; text-shadow: 0 2px 4px rgba(0,0,0,0.3);">
                Tiếp Nhận Bảng Bóc Tách Vật Tư & Báo Giá Công Trình
            </h1>

            <p style="font-size: 1.05rem; line-height: 1.6; color: #e0f2fe; margin: 0 0 1.75rem;">
                Chính sách chiết khấu phân phối trực tiếp từ Tổng Kho dành cho <strong>Nhà Thầu Cơ Điện M&E, Chủ Đầu Tư, Đơn Vị Thi Công & Đại Lý</strong> tại TP. Hồ Chí Minh, Long An và toàn quốc. Kỹ sư chuyên trách cam kết bóc tách và phản hồi bảng dự toán chi tiết trong <strong>2 giờ làm việc</strong>.
            </p>

            <!-- 4 Cam kết doanh nghiệp chính hãng -->
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(210px, 1fr)); gap: 1rem;">
                <div style="display: flex; align-items: center; gap: 0.75rem; background: rgba(0, 20, 50, 0.45); backdrop-filter: blur(4px); padding: 10px 14px; border-radius: 8px; border: 1px solid rgba(255,255,255,0.15);">
                    <div style="width: 36px; height: 36px; border-radius: 6px; background: rgba(0, 64, 152, 0.6); display: flex; align-items: center; justify-content: center; color: #38bdf8; flex-shrink: 0;">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    </div>
                    <div>
                        <strong style="display: block; font-size: 0.88rem; color: #ffffff;">Phản hồi trong 2 giờ</strong>
                        <small style="color: #bfdbfe; font-size: 0.75rem;">Bảng chào giá chi tiết có mộc</small>
                    </div>
                </div>

                <div style="display: flex; align-items: center; gap: 0.75rem; background: rgba(0, 20, 50, 0.45); backdrop-filter: blur(4px); padding: 10px 14px; border-radius: 8px; border: 1px solid rgba(255,255,255,0.15);">
                    <div style="width: 36px; height: 36px; border-radius: 6px; background: rgba(0, 64, 152, 0.6); display: flex; align-items: center; justify-content: center; color: #facc15; flex-shrink: 0;">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                    </div>
                    <div>
                        <strong style="display: block; font-size: 0.88rem; color: #ffffff;">Chiết khấu dự án kịch trần</strong>
                        <small style="color: #bfdbfe; font-size: 0.75rem;">Cạnh tranh trực tiếp từ Tổng Kho</small>
                    </div>
                </div>

                <div style="display: flex; align-items: center; gap: 0.75rem; background: rgba(0, 20, 50, 0.45); backdrop-filter: blur(4px); padding: 10px 14px; border-radius: 8px; border: 1px solid rgba(255,255,255,0.15);">
                    <div style="width: 36px; height: 36px; border-radius: 6px; background: rgba(0, 64, 152, 0.6); display: flex; align-items: center; justify-content: center; color: #4ade80; flex-shrink: 0;">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                    </div>
                    <div>
                        <strong style="display: block; font-size: 0.88rem; color: #ffffff;">Đầy đủ CO/CQ & Hóa đơn VAT</strong>
                        <small style="color: #bfdbfe; font-size: 0.75rem;">Chứng chỉ xuất xưởng chính hãng</small>
                    </div>
                </div>

                <div style="display: flex; align-items: center; gap: 0.75rem; background: rgba(0, 20, 50, 0.45); backdrop-filter: blur(4px); padding: 10px 14px; border-radius: 8px; border: 1px solid rgba(255,255,255,0.15);">
                    <div style="width: 36px; height: 36px; border-radius: 6px; background: rgba(0, 64, 152, 0.6); display: flex; align-items: center; justify-content: center; color: #fb923c; flex-shrink: 0;">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="3" width="15" height="13"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
                    </div>
                    <div>
                        <strong style="display: block; font-size: 0.88rem; color: #ffffff;">Giao hàng chân công trình</strong>
                        <small style="color: #bfdbfe; font-size: 0.75rem;">TP.HCM, Long An & liên tỉnh</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ==================== NỘI DUNG CHÍNH (BỐ CỤC 2 CỘT CHUẨN DOANH NGHIỆP) ==================== -->
<div class="container" style="padding-top: 2.5rem; padding-bottom: 3.5rem;">
    <div class="corp-quote-grid" style="display: grid; grid-template-columns: minmax(0, 1fr) 400px; gap: 2rem; align-items: start;">

        <!-- CỘT TRÁI: FORM TIẾP NHẬN HỒ SƠ DOANH NGHIỆP -->
        <div style="background: #ffffff; border-radius: 12px; border: 1px solid #cbd5e1; box-shadow: 0 4px 20px rgba(0,0,0,0.06); padding: 2.25rem;">
            
            <form action="<?= url('/bao-gia-cong-trinh') ?>" method="post" enctype="multipart/form-data" id="corpQuoteForm" novalidate>
                <?= csrf_field() ?>
                <input type="text" name="website" style="display:none !important;" tabindex="-1" autocomplete="off" aria-hidden="true">

                <!-- PHẦN 1: THÔNG TIN ĐƠN VỊ YÊU CẦU BÁO GIÁ -->
                <div style="margin-bottom: 2rem;">
                    <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1.25rem; border-bottom: 2px solid #e2e8f0; padding-bottom: 0.75rem;">
                        <div style="width: 32px; height: 32px; border-radius: 6px; background: #004098; color: #fff; font-weight: 700; font-size: 0.95rem; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            01
                        </div>
                        <div>
                            <h2 style="font-size: 1.15rem; font-weight: 800; color: #004098; margin: 0;">
                                THÔNG TIN ĐƠN VỊ YÊU CẦU BÁO GIÁ
                            </h2>
                            <small style="color: #64748b; font-size: 0.8rem;">Bảng chào giá dự toán chính thức sẽ được gửi trực tiếp tới thông tin người liên hệ này.</small>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;" class="form-grid-2col">
                        <div>
                            <label class="corp-label">Họ và tên người liên hệ <span style="color:#dc2626;">*</span></label>
                            <input type="text" name="customer_name" class="corp-input" required minlength="2" value="<?= old('customer_name') ?>" placeholder="Ví dụ: Kỹ sư Nguyễn Văn Tuấn">
                        </div>

                        <div>
                            <label class="corp-label">Số điện thoại / Zalo nhận báo giá <span style="color:#dc2626;">*</span></label>
                            <input type="tel" name="phone" class="corp-input" required value="<?= old('phone') ?>" placeholder="Ví dụ: 0909 123 456 (Zalo)">
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-top: 1rem;" class="form-grid-2col">
                        <div>
                            <label class="corp-label">Email công ty nhận file báo giá (Excel/PDF)</label>
                            <input type="email" name="email" class="corp-input" value="<?= old('email') ?>" placeholder="duan@congty.com">
                        </div>

                        <div>
                            <label class="corp-label">Đối tượng khách hàng</label>
                            <select name="customer_type" class="corp-input">
                                <?php foreach ($customerTypes as $val => $label): ?>
                                    <option value="<?= e($val) ?>" <?= old('customer_type') === $val ? 'selected' : '' ?>><?= e($label) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div style="margin-top: 1rem;">
                        <label class="corp-label">Tên Công ty / Nhà thầu Cơ Điện M&E <small style="color:#64748b; font-weight:normal;">(Ghi rõ để áp dụng chính sách đại lý)</small></label>
                        <input type="text" name="company" class="corp-input" value="<?= old('company') ?>" placeholder="Ví dụ: Công ty CP Cơ Điện & Xây Lắp Công Trình Nam Phát">
                    </div>

                    <div style="margin-top: 1rem;">
                        <label class="corp-label">Tên công trình & Địa điểm giao hàng <small style="color:#64748b; font-weight:normal;">(Để kỹ sư tính toán phương án vận chuyển chân công trình)</small></label>
                        <input type="text" name="project_name" class="corp-input" value="<?= old('project_name') ?>" placeholder="Ví dụ: Dự án Căn hộ cao cấp Masterise, Quận 2, TP.HCM hoặc Nhà xưởng KCN Long Hậu, Cần Giuộc, Long An">
                    </div>
                </div>

                <!-- PHẦN 2: ĐÍNH KÈM FILE DANH MỤC VẬT TƯ (BOM) & BẢN VẼ -->
                <div style="margin-bottom: 2rem;">
                    <div style="display: flex; align-items: center; justify-content: space-between; border-bottom: 2px solid #e2e8f0; padding-bottom: 0.75rem; margin-bottom: 1.25rem; flex-wrap: wrap; gap: 0.5rem;">
                        <div style="display: flex; align-items: center; gap: 0.75rem;">
                            <div style="width: 32px; height: 32px; border-radius: 6px; background: #004098; color: #fff; font-weight: 700; font-size: 0.95rem; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                02
                            </div>
                            <div>
                                <h2 style="font-size: 1.15rem; font-weight: 800; color: #004098; margin: 0;">
                                    ĐÍNH KÈM FILE DANH MỤC VẬT TƯ (BOM)
                                </h2>
                                <small style="color: #64748b; font-size: 0.8rem;">Tải lên bảng dự toán Excel, bản vẽ CAD hoặc file tổng hợp khối lượng.</small>
                            </div>
                        </div>

                        <!-- Mẫu file bóc tách Excel chuẩn -->
                        <span style="font-size: 0.8rem; color: #004098; font-weight: 600; display: inline-flex; align-items: center; gap: 0.35rem;">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                            Mẫu BOM chuẩn Panasonic
                        </span>
                    </div>

                    <!-- Khung Dropzone Kéo Thả File Doanh Nghiệp -->
                    <div class="corp-dropzone" id="corpDropzone" style="border: 2px dashed #004098; background: #f8fafc; border-radius: 10px; padding: 2.25rem 1.5rem; text-align: center; cursor: pointer; transition: all 0.25s ease; position: relative;">
                        <input type="file" name="bom_file" id="corpFileInput" accept=".xlsx,.xls,.csv,.pdf,.dwg,.doc,.docx,.zip,.rar" style="position: absolute; inset:0; opacity:0; cursor:pointer;" onchange="corpFileChanged(this)">
                        
                        <div id="corpDropzonePrompt">
                            <div style="width: 60px; height: 60px; margin: 0 auto 0.75rem; background: #e0f2fe; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #004098;">
                                <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                            </div>
                            <strong style="display: block; font-size: 1.05rem; color: #0f172a; margin-bottom: 0.35rem;">
                                Nhấp chọn file từ máy tính hoặc Kéo & Thả file vào đây
                            </strong>
                            <p style="font-size: 0.85rem; color: #64748b; margin: 0 0 0.5rem;">
                                Hỗ trợ các định dạng: <strong>Excel (.xlsx, .xls), PDF, Word, Bản vẽ AutoCAD (.dwg) hoặc file nén .zip/.rar</strong>
                            </p>
                            <span style="font-size: 0.78rem; color: #004098; background: #eff6ff; padding: 3px 12px; border-radius: 12px; border: 1px solid #bfdbfe; font-weight: 600;">
                                Dung lượng tối đa: 25MB / file
                            </span>
                        </div>

                        <!-- Khung trạng thái khi đã chọn file -->
                        <div id="corpFileInfo" style="display: none; background: #ffffff; border: 1.5px solid #16a34a; border-radius: 8px; padding: 1rem; align-items: center; justify-content: center; gap: 1rem;">
                            <div style="width: 40px; height: 40px; border-radius: 6px; background: #dcfce7; color: #16a34a; display: flex; align-items: center; justify-content: center;">
                                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                            </div>
                            <div style="text-align: left;">
                                <strong id="corpFileName" style="display: block; color: #0f172a; font-size: 0.95rem;">bang_du_toan_vat_tu.xlsx</strong>
                                <small id="corpFileSize" style="color: #64748b; font-size: 0.8rem;">2.4 MB</small>
                            </div>
                            <button type="button" onclick="corpFileReset(event)" style="margin-left: 1.5rem; background: #fee2e2; color: #b91c1c; border: 1px solid #fca5a5; border-radius: 6px; padding: 5px 12px; font-size: 0.825rem; cursor: pointer; font-weight: 600;">
                                Đổi file khác
                            </button>
                        </div>
                    </div>

                    <?php if ($cartCount > 0): ?>
                        <div style="margin-top: 1rem; background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 8px; padding: 0.85rem 1rem;">
                            <label style="display: flex; align-items: center; gap: 0.65rem; cursor: pointer;">
                                <input type="checkbox" name="include_cart" value="1" checked style="width: 18px; height: 18px; accent-color: #004098;">
                                <span style="font-size: 0.88rem; color: #0f172a;">
                                    <strong>Đính kèm <?= $cartCount ?> sản phẩm hiện có trong giỏ hàng</strong> vào hồ sơ bóc tách dự toán này.
                                </span>
                            </label>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- PHẦN 3: YÊU CẦU KỸ THUẬT & TIẾN ĐỘ GIAO HÀNG -->
                <div style="margin-bottom: 2rem;">
                    <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1.25rem; border-bottom: 2px solid #e2e8f0; padding-bottom: 0.75rem;">
                        <div style="width: 32px; height: 32px; border-radius: 6px; background: #004098; color: #fff; font-weight: 700; font-size: 0.95rem; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            03
                        </div>
                        <div>
                            <h2 style="font-size: 1.15rem; font-weight: 800; color: #004098; margin: 0;">
                                GHI CHÚ YÊU CẦU KỸ THUẬT & TIẾN ĐỘ
                            </h2>
                            <small style="color: #64748b; font-size: 0.8rem;">Nếu chưa có file, bạn có thể dán trực tiếp danh sách mã thiết bị & số lượng tại đây.</small>
                        </div>
                    </div>

                    <div>
                        <label class="corp-label">Nội dung yêu cầu chi tiết</label>
                        <textarea name="note" rows="4" class="corp-input" style="height: auto; resize: vertical; font-family: inherit; line-height: 1.5;" placeholder="Ví dụ:&#10;1. Cần bóc tách tương đương công tắc Moderva Xám ánh kim sang dòng Halumie hoặc Minerva.&#10;2. Cần cung cấp đầy đủ hồ sơ kỹ thuật CO/CQ, Catalog gốc có mộc và Hóa đơn VAT điện tử.&#10;3. Tiến độ giao hàng chia làm 3 đợt về kho dự án tại TP.HCM hoặc Long An..."><?= old('note') ?></textarea>
                    </div>
                </div>

                <!-- NÚT GỬI HỒ SƠ DOANH NGHIỆP -->
                <div>
                    <button type="submit" id="btnCorpSubmit" style="width: 100%; background: #004098; color: #ffffff; border: none; padding: 1.1rem 1.75rem; border-radius: 8px; font-size: 1.1rem; font-weight: 800; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 0.65rem; box-shadow: 0 6px 18px rgba(0,64,152,0.3); transition: all 0.2s ease; letter-spacing: 0.5px;">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
                        GỬI HỒ SƠ YÊU CẦU BÁO GIÁ DỰ ÁN
                    </button>
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 0.85rem; font-size: 0.825rem; color: #64748b; flex-wrap: wrap; gap: 0.5rem;">
                        <span style="display: flex; align-items: center; gap: 0.35rem;">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                            Cam kết bảo mật hồ sơ thầu 100%
                        </span>
                        <span style="display: flex; align-items: center; gap: 0.35rem;">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#004098" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                            Phản hồi dự toán trong 2 giờ
                        </span>
                        <span style="display: flex; align-items: center; gap: 0.35rem;">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                            Tư vấn giải pháp miễn phí
                        </span>
                    </div>
                </div>
            </form>

        </div>

        <!-- CỘT PHẢI: CHỨNG THỰC DOANH NGHIỆP, TỔNG KHO & DỰ ÁN THỰC TẾ -->
        <div style="display: flex; flex-direction: column; gap: 1.5rem; position: sticky; top: 1.5rem;">
            
            <!-- THẺ 1: HÌNH ẢNH THỰC TẾ TỔNG KHO PANASONIC -->
            <div style="background: #ffffff; border: 1px solid #cbd5e1; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.05);">
                <div style="position: relative; height: 160px; overflow: hidden;">
                    <img src="<?= asset('uploads/banners/banner-tong-kho-panasonic.jpg') ?>" alt="Tổng Kho Thiết Bị Điện Panasonic" style="width: 100%; height: 100%; object-fit: cover;">
                    <div style="position: absolute; inset: 0; background: linear-gradient(180deg, transparent 40%, rgba(0,32,77,0.85) 100%);"></div>
                    <span style="position: absolute; bottom: 10px; left: 14px; color: #ffffff; font-weight: 700; font-size: 0.95rem;">
                        TỔNG KHO VẬT TƯ DỰ ÁN PANASONIC
                    </span>
                </div>
                <div style="padding: 1.25rem;">
                    <div style="display: flex; flex-direction: column; gap: 0.65rem; font-size: 0.85rem; color: #334155;">
                        <div style="display: flex; gap: 0.5rem; align-items: flex-start;">
                            <strong style="color: #004098; min-width: 95px;">Kho TP.HCM:</strong>
                            <span>4491 Nguyễn Cửu Phú, Tân Tạo A, Bình Tân, TP.HCM</span>
                        </div>
                        <div style="display: flex; gap: 0.5rem; align-items: flex-start;">
                            <strong style="color: #004098; min-width: 95px;">Kho Long An:</strong>
                            <span>KCN Long Hậu, Cần Giuộc, Long An (Sát ranh Nhà Bè)</span>
                        </div>
                        <div style="display: flex; gap: 0.5rem; align-items: flex-start;">
                            <strong style="color: #004098; min-width: 95px;">Quy mô kho:</strong>
                            <span style="color: #16a34a; font-weight: 600;">Hơn 2.500m² - Sẵn sàng xuất kho 24/7</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- THẺ 2: QUY TRÌNH TIẾP NHẬN BÁO GIÁ 4 GIAI ĐOẠN -->
            <div style="background: #ffffff; border: 1px solid #cbd5e1; border-radius: 12px; padding: 1.5rem; box-shadow: 0 4px 15px rgba(0,0,0,0.05);">
                <div style="border-bottom: 2px solid #e2e8f0; padding-bottom: 0.65rem; margin-bottom: 1.25rem;">
                    <h3 style="font-size: 1.05rem; font-weight: 800; color: #004098; margin: 0;">
                        QUY TRÌNH PHỐI HỢP KỸ THUẬT
                    </h3>
                </div>

                <div style="display: flex; flex-direction: column; gap: 1.15rem;">
                    <div style="display: flex; gap: 0.75rem; align-items: flex-start;">
                        <span style="width: 26px; height: 26px; border-radius: 4px; background: #004098; color: #fff; font-weight: 700; font-size: 0.825rem; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">1</span>
                        <div>
                            <strong style="color: #0f172a; font-size: 0.9rem; display: block;">Tiếp nhận hồ sơ thầu</strong>
                            <p style="margin: 2px 0 0; color: #64748b; font-size: 0.8rem; line-height: 1.4;">Kỹ sư dự án tiếp nhận file bóc tách trong 15 phút.</p>
                        </div>
                    </div>

                    <div style="display: flex; gap: 0.75rem; align-items: flex-start;">
                        <span style="width: 26px; height: 26px; border-radius: 4px; background: #004098; color: #fff; font-weight: 700; font-size: 0.825rem; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">2</span>
                        <div>
                            <strong style="color: #0f172a; font-size: 0.9rem; display: block;">Tối ưu mã & Chiết khấu cao nhất</strong>
                            <p style="margin: 2px 0 0; color: #64748b; font-size: 0.8rem; line-height: 1.4;">Tư vấn giải pháp tương đương, áp dụng giá công trình tốt nhất.</p>
                        </div>
                    </div>

                    <div style="display: flex; gap: 0.75rem; align-items: flex-start;">
                        <span style="width: 26px; height: 26px; border-radius: 4px; background: #004098; color: #fff; font-weight: 700; font-size: 0.825rem; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">3</span>
                        <div>
                            <strong style="color: #0f172a; font-size: 0.9rem; display: block;">Gửi file chào giá chuẩn dự toán</strong>
                            <p style="margin: 2px 0 0; color: #64748b; font-size: 0.8rem; line-height: 1.4;">Gửi file Excel/PDF chi tiết đơn giá, tiến độ giao nhận qua Zalo/Email.</p>
                        </div>
                    </div>

                    <div style="display: flex; gap: 0.75rem; align-items: flex-start;">
                        <span style="width: 26px; height: 26px; border-radius: 4px; background: #16a34a; color: #fff; font-weight: 700; font-size: 0.825rem; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">✓</span>
                        <div>
                            <strong style="color: #16a34a; font-size: 0.9rem; display: block;">Ký HĐ & Giao hàng kèm CO/CQ</strong>
                            <p style="margin: 2px 0 0; color: #64748b; font-size: 0.8rem; line-height: 1.4;">Ký hợp đồng, giao hàng tận chân công trình kèm hóa đơn VAT.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- THẺ 3: LIÊN HỆ KỸ SƯ DỰ ÁN 24/7 -->
            <div style="background: #ffffff; border: 1px solid #cbd5e1; border-radius: 12px; padding: 1.5rem; box-shadow: 0 4px 15px rgba(0,0,0,0.05);">
                <h3 style="font-size: 1.05rem; font-weight: 800; color: #0f172a; margin: 0 0 0.5rem;">
                    CẦN BÁO GIÁ GẤP HỒ SƠ THẦU?
                </h3>
                <p style="font-size: 0.85rem; color: #64748b; margin: 0 0 1rem; line-height: 1.45;">
                    Quý khách có khối lượng lớn cần chốt nhanh tiến độ đấu thầu, hãy kết nối trực tiếp với Kỹ sư phụ trách:
                </p>

                <div style="display: flex; flex-direction: column; gap: 0.65rem;">
                    <a href="tel:0869800299" style="background: #dc2626; color: #ffffff; text-decoration: none; padding: 0.8rem 1rem; border-radius: 6px; font-weight: 700; font-size: 0.95rem; display: flex; align-items: center; justify-content: center; gap: 0.5rem; box-shadow: 0 4px 10px rgba(220,38,38,0.25);">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                        Hotline Dự Án: 0869.800.299
                    </a>

                    <a href="https://zalo.me/0798966958" target="_blank" rel="noopener" style="background: #0284c7; color: #ffffff; text-decoration: none; padding: 0.8rem 1rem; border-radius: 6px; font-weight: 700; font-size: 0.95rem; display: flex; align-items: center; justify-content: center; gap: 0.5rem; box-shadow: 0 4px 10px rgba(2,132,199,0.25);">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                        Gửi File Qua Zalo Kỹ Thuật
                    </a>
                </div>
            </div>

            <!-- THẺ 4: CÁC DỰ ÁN TIÊU BIỂU ĐÃ CUNG CẤP -->
            <div style="background: #ffffff; border: 1px solid #cbd5e1; border-radius: 12px; padding: 1.25rem; box-shadow: 0 4px 15px rgba(0,0,0,0.05);">
                <h4 style="font-size: 0.95rem; font-weight: 800; color: #004098; margin: 0 0 0.85rem; text-transform: uppercase;">
                    Công Trình Tiêu Biểu TP.HCM & Long An
                </h4>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.65rem;">
                    <div style="border-radius: 6px; overflow: hidden; border: 1px solid #e2e8f0;">
                        <img src="<?= asset('uploads/projects/landmark-81.jpg') ?>" alt="Landmark 81" style="width: 100%; height: 65px; object-fit: cover;">
                        <span style="display: block; font-size: 0.75rem; font-weight: 600; padding: 4px 6px; color: #0f172a; text-align: center; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">Landmark 81 TP.HCM</span>
                    </div>
                    <div style="border-radius: 6px; overflow: hidden; border: 1px solid #e2e8f0;">
                        <img src="<?= asset('uploads/projects/waterpoint-nam-long.jpg') ?>" alt="Waterpoint Nam Long" style="width: 100%; height: 65px; object-fit: cover;">
                        <span style="display: block; font-size: 0.75rem; font-weight: 600; padding: 4px 6px; color: #0f172a; text-align: center; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">Waterpoint Long An</span>
                    </div>
                    <div style="border-radius: 6px; overflow: hidden; border: 1px solid #e2e8f0;">
                        <img src="<?= asset('uploads/projects/kcn-long-hau.jpg') ?>" alt="KCN Long Hậu" style="width: 100%; height: 65px; object-fit: cover;">
                        <span style="display: block; font-size: 0.75rem; font-weight: 600; padding: 4px 6px; color: #0f172a; text-align: center; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">KCN Long Hậu</span>
                    </div>
                    <div style="border-radius: 6px; overflow: hidden; border: 1px solid #e2e8f0;">
                        <img src="<?= asset('uploads/projects/masterise-lumiere.jpg') ?>" alt="Masterise Lumiere" style="width: 100%; height: 65px; object-fit: cover;">
                        <span style="display: block; font-size: 0.75rem; font-weight: 600; padding: 4px 6px; color: #0f172a; text-align: center; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">Masterise Q.2</span>
                    </div>
                </div>
            </div>

        </div>

    </div>

    <!-- ==================== DẢI ĐỐI TÁC NHÀ THẦU & CHỦ ĐẦU TƯ (PARTNER TRUST BAR) ==================== -->
    <div style="margin-top: 3.5rem; background: #ffffff; border: 1px solid #cbd5e1; border-radius: 12px; padding: 1.75rem 2rem; box-shadow: 0 4px 20px rgba(0,0,0,0.04);">
        <div style="text-align: center; margin-bottom: 1.25rem;">
            <h3 style="font-size: 1rem; font-weight: 800; color: #004098; margin: 0 0 0.25rem; text-transform: uppercase; letter-spacing: 0.5px;">
                ĐỐI TÁC NHÀ THẦU CƠ ĐIỆN & CHỦ ĐẦU TƯ TIN TƯỞNG LỰA CHỌN
            </h3>
            <p style="font-size: 0.825rem; color: #64748b; margin: 0;">
                Tổng Kho Panasonic tự hào đồng hành cùng các tập đoàn xây dựng và nhà thầu M&E uy tín hàng đầu Việt Nam
            </p>
        </div>

        <div style="display: flex; justify-content: space-around; align-items: center; flex-wrap: wrap; gap: 1.5rem; opacity: 0.85;">
            <div style="font-size: 1.1rem; font-weight: 800; color: #1e3a8a; letter-spacing: 1px;">COTECCONS</div>
            <div style="font-size: 1.1rem; font-weight: 800; color: #15803d; letter-spacing: 1px;">HÒA BÌNH GROUP</div>
            <div style="font-size: 1.1rem; font-weight: 800; color: #b91c1c; letter-spacing: 1px;">RICONS M&E</div>
            <div style="font-size: 1.1rem; font-weight: 800; color: #0369a1; letter-spacing: 1px;">AN PHONG</div>
            <div style="font-size: 1.1rem; font-weight: 800; color: #9333ea; letter-spacing: 1px;">MASTERISE HOMES</div>
            <div style="font-size: 1.1rem; font-weight: 800; color: #c2410c; letter-spacing: 1px;">VINGROUP</div>
        </div>
    </div>
</div>

<style>
/* CSS ĐỊNH DẠNG FORM DOANH NGHIỆP CAO CẤP */
.corp-label {
    display: block !important;
    font-size: 0.875rem !important;
    font-weight: 700 !important;
    color: #0f172a !important;
    margin-bottom: 0.4rem !important;
}

.corp-input {
    width: 100% !important;
    height: 44px !important;
    background: #ffffff !important;
    color: #0f172a !important;
    border: 1.5px solid #cbd5e1 !important;
    border-radius: 6px !important;
    padding: 0 1rem !important;
    font-size: 0.925rem !important;
    font-family: inherit !important;
    transition: all 0.2s ease !important;
    outline: none !important;
}

.corp-input:focus {
    border-color: #004098 !important;
    box-shadow: 0 0 0 3px rgba(0, 64, 152, 0.12) !important;
    background-color: #ffffff !important;
}

.corp-dropzone:hover {
    background: #f0f7ff !important;
    border-color: #002b66 !important;
}

#btnCorpSubmit:hover {
    background: #002d6b !important;
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(0,64,152,0.4) !important;
}

@media (max-width: 992px) {
    .corp-quote-grid {
        grid-template-columns: 1fr !important;
    }
    .form-grid-2col {
        grid-template-columns: 1fr !important;
    }
}
</style>

<script>
function corpFileChanged(input) {
    if (input.files && input.files[0]) {
        const file = input.files[0];
        document.getElementById('corpFileName').textContent = file.name;
        
        let sizeStr = '';
        if (file.size < 1024 * 1024) {
            sizeStr = (file.size / 1024).toFixed(1) + ' KB';
        } else {
            sizeStr = (file.size / (1024 * 1024)).toFixed(2) + ' MB';
        }
        document.getElementById('corpFileSize').textContent = sizeStr;

        document.getElementById('corpDropzonePrompt').style.display = 'none';
        const info = document.getElementById('corpFileInfo');
        info.style.display = 'flex';
    }
}

function corpFileReset(e) {
    e.stopPropagation();
    const input = document.getElementById('corpFileInput');
    if (input) input.value = '';
    document.getElementById('corpDropzonePrompt').style.display = 'block';
    document.getElementById('corpFileInfo').style.display = 'none';
}

// Bắt sự kiện kéo thả file
const cdz = document.getElementById('corpDropzone');
if (cdz) {
    ['dragenter', 'dragover'].forEach(n => {
        cdz.addEventListener(n, (e) => {
            e.preventDefault();
            cdz.style.background = '#e0f2fe';
            cdz.style.borderColor = '#002b66';
        });
    });
    ['dragleave', 'drop'].forEach(n => {
        cdz.addEventListener(n, (e) => {
            e.preventDefault();
            cdz.style.background = '#f8fafc';
            cdz.style.borderColor = '#004098';
        });
    });
}

// Hiệu ứng nút gửi khi submit
document.getElementById('corpQuoteForm')?.addEventListener('submit', function(e) {
    const btn = document.getElementById('btnCorpSubmit');
    if (btn) {
        btn.innerHTML = '⏳ Đang chuyển hồ sơ bóc tách & gửi yêu cầu...';
        btn.style.opacity = '0.85';
    }
});
</script>
