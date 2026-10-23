<?php
/** @var array $projects */
/** @var string $title */
?>

<div class="container project-page-wrap" style="padding-top: 1.5rem; padding-bottom: 3.5rem;">
    <!-- Breadcrumb -->
    <nav class="breadcrumb" aria-label="Đường dẫn">
        <a href="<?= url('/') ?>">Trang chủ</a>
        <span>/</span>
        <span aria-current="page">Dự án tiêu biểu (TP.HCM & Long An)</span>
    </nav>

    <!-- Page Header -->
    <div class="project-page-head" style="margin-bottom: 2rem;">
        <span class="project-head-tag" style="display: inline-flex; align-items: center; gap: 0.4rem; background: #e0f2fe; color: #004098; font-size: 0.825rem; font-weight: 800; padding: 4px 12px; border-radius: 99px; text-transform: uppercase; margin-bottom: 0.75rem;">
            <?= icon('building') ?> HỒ SƠ NĂNG LỰC DỰ ÁN
        </span>
        <h1 style="font-size: 2rem; font-weight: 900; color: #004098; margin: 0 0 0.85rem; letter-spacing: -0.5px;">
            DỰ ÁN & CÔNG TRÌNH TIÊU BIỂU CUNG CẤP THIẾT BỊ ĐIỆN PANASONIC
        </h1>
        <p style="font-size: 1.05rem; line-height: 1.6; color: var(--slate-600); max-width: 900px; margin: 0 0 1.5rem;">
            Tổng kho đại lý Panasonic chính hãng tự hào cung cấp 100% thiết bị điện chất lượng chuẩn Nhật (Công tắc ổ cắm cao cấp, Cầu dao an toàn MCB/RCBO, Đèn LED Downlight, Tủ điện âm tường, Quạt hút thông gió và Ổ cắm công nghiệp PCE) cho các đại đô thị, chung cư cao cấp và cụm khu công nghiệp trọng điểm tại <strong>TP. Hồ Chí Minh</strong> và <strong>Long An</strong>.
        </p>

        <!-- Thẻ thống kê nhanh -->
        <div class="project-summary-boxes" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 2rem;">
            <div style="background: #ffffff; border: 1.5px solid var(--slate-200); border-radius: var(--radius-lg); padding: 1rem 1.25rem; box-shadow: 0 4px 12px rgba(0,0,0,0.03);">
                <div style="font-size: 1.75rem; font-weight: 900; color: #004098;">100%</div>
                <div style="font-size: 0.85rem; color: var(--slate-600); font-weight: 600;">CO/CQ & VAT Chính Hãng</div>
            </div>
            <div style="background: #ffffff; border: 1.5px solid var(--slate-200); border-radius: var(--radius-lg); padding: 1rem 1.25rem; box-shadow: 0 4px 12px rgba(0,0,0,0.03);">
                <div style="font-size: 1.75rem; font-weight: 900; color: #004098;">2h – 4h</div>
                <div style="font-size: 0.85rem; color: var(--slate-600); font-weight: 600;">Giao hàng tận chân công trình</div>
            </div>
            <div style="background: #ffffff; border: 1.5px solid var(--slate-200); border-radius: var(--radius-lg); padding: 1rem 1.25rem; box-shadow: 0 4px 12px rgba(0,0,0,0.03);">
                <div style="font-size: 1.75rem; font-weight: 900; color: #004098;">15% – 35%</div>
                <div style="font-size: 0.85rem; color: var(--slate-600); font-weight: 600;">Chiết khấu dự án tốt nhất</div>
            </div>
            <div style="background: #ffffff; border: 1.5px solid var(--slate-200); border-radius: var(--radius-lg); padding: 1rem 1.25rem; box-shadow: 0 4px 12px rgba(0,0,0,0.03);">
                <div style="font-size: 1.75rem; font-weight: 900; color: #004098;">TP.HCM & Long An</div>
                <div style="font-size: 0.85rem; color: var(--slate-600); font-weight: 600;">Tổng kho sẵn hàng số lượng lớn</div>
            </div>
        </div>

        <!-- Bộ lọc khu vực -->
        <div class="project-filter-tabs" style="display: flex; gap: 0.65rem; border-bottom: 2px solid var(--slate-200); padding-bottom: 0.75rem; margin-bottom: 2rem;">
            <button type="button" class="btn-filter-pj is-active" onclick="filterProjects('all', this)" style="padding: 0.5rem 1.15rem; border-radius: 99px; border: none; background: #004098; color: #fff; font-weight: 700; font-size: 0.875rem; cursor: pointer;">
                Tất cả dự án (<?= count($projects) ?>)
            </button>
            <button type="button" class="btn-filter-pj" onclick="filterProjects('tphcm', this)" style="padding: 0.5rem 1.15rem; border-radius: 99px; border: 1.5px solid var(--slate-300); background: #fff; color: var(--slate-700); font-weight: 700; font-size: 0.875rem; cursor: pointer;">
                Khu vực TP. Hồ Chí Minh
            </button>
            <button type="button" class="btn-filter-pj" onclick="filterProjects('longan', this)" style="padding: 0.5rem 1.15rem; border-radius: 99px; border: 1.5px solid var(--slate-300); background: #fff; color: var(--slate-700); font-weight: 700; font-size: 0.875rem; cursor: pointer;">
                Khu vực Tỉnh Long An
            </button>
        </div>
    </div>

    <!-- Danh sách thẻ dự án -->
    <div class="projects-grid" id="projectsGrid">
        <?php foreach ($projects as $pj): 
            $locLower = mb_strtolower($pj['location']);
            $areaClass = (str_contains($locLower, 'long an')) ? 'area-longan' : 'area-tphcm';
            
            $gallery = [];
            if (!empty($pj['gallery'])) {
                if (is_array($pj['gallery'])) {
                    $gallery = $pj['gallery'];
                } else {
                    $decoded = json_decode((string)$pj['gallery'], true);
                    $gallery = is_array($decoded) ? $decoded : [];
                }
            }
            if (empty($gallery) && !empty($pj['thumbnail'])) {
                $gallery[] = $pj['thumbnail'];
            }
            $materialThumbs = count($gallery) > 1 ? array_slice($gallery, 1, 4) : array_slice($gallery, 0, 4);
            $detailUrl = url('/du-an/' . $pj['slug']);
        ?>
            <article class="project-card <?= $areaClass ?>" style="display: flex; flex-direction: column;">
                <a href="<?= $detailUrl ?>" class="project-thumb-wrap" style="aspect-ratio: 16/10; display: block; position: relative; text-decoration: none;" title="Xem bài viết & hình ảnh thi công <?= e($pj['title']) ?>">
                    <img src="<?= media($pj['thumbnail']) ?>" alt="<?= e($pj['title']) ?>" loading="lazy" class="project-thumb">
                    <span class="project-badge"><?= e($pj['category']) ?></span>
                    <?php if (!empty($pj['completion_year'])): ?>
                        <span class="project-year">Hoàn thành: <?= e((string)$pj['completion_year']) ?></span>
                    <?php endif; ?>
                    <span style="position: absolute; bottom: 8px; right: 8px; background: rgba(15, 23, 42, 0.82); backdrop-filter: blur(4px); color: #ffffff; font-size: 0.72rem; padding: 3px 8px; border-radius: 4px; font-weight: 700; display: inline-flex; align-items: center; gap: 4px;">
                        📸 <?= count($gallery) ?> ảnh vật tư
                    </span>
                </a>
                <div class="project-content" style="flex: 1; display: flex; flex-direction: column;">
                    <h2 class="project-title" style="font-size: 1.15rem; font-weight: 800; line-height: 1.35; margin: 0 0 0.85rem;">
                        <a href="<?= $detailUrl ?>" style="color: inherit; text-decoration: none; transition: color 0.2s;" onmouseover="this.style.color='#004098'" onmouseout="this.style.color='inherit'">
                            <?= e($pj['title']) ?>
                        </a>
                    </h2>

                    <div class="project-meta" style="margin-bottom: 1rem;">
                        <div class="project-meta-item">
                            <span class="meta-label">Chủ đầu tư:</span>
                            <span class="meta-val"><strong><?= e($pj['client_name']) ?></strong></span>
                        </div>
                        <div class="project-meta-item">
                            <span class="meta-label">Địa chỉ:</span>
                            <span class="meta-val" style="color: #0284c7; font-weight: 600;">
                                <?= icon('map-pin') ?> <?= e($pj['location']) ?>
                            </span>
                        </div>
                        <?php if (!empty($pj['scale'])): ?>
                            <div class="project-meta-item">
                                <span class="meta-label">Quy mô:</span>
                                <span class="meta-val"><?= e($pj['scale']) ?></span>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Strip hình ảnh vật liệu Panasonic đã thi công -->
                    <div class="project-materials-preview" style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 8px; padding: 0.75rem; margin-bottom: 1rem;">
                        <div style="font-size: 0.75rem; text-transform: uppercase; font-weight: 800; color: #004098; margin-bottom: 0.5rem; display: flex; justify-content: space-between; align-items: center;">
                            <span>⚡ Vật liệu Panasonic đã thi công:</span>
                            <a href="<?= $detailUrl ?>" style="color: #0284c7; text-decoration: none; font-size: 0.75rem; font-weight: 700;">Chi tiết (<?= count($gallery) ?>) →</a>
                        </div>
                        <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 0.4rem;">
                            <?php foreach ($materialThumbs as $mThumb): ?>
                                <a href="<?= $detailUrl ?>" style="display: block; aspect-ratio: 1/1; border: 1px solid #e2e8f0; border-radius: 6px; overflow: hidden; background: #f8fafc; padding: 2px;" title="Vật liệu Panasonic thi công thực tế">
                                    <img src="<?= media($mThumb) ?>" alt="Vật liệu Panasonic" loading="lazy" style="width: 100%; height: 100%; object-fit: contain;">
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="project-equipment" style="background: #f8fafc; border-left: 3px solid #004098; padding: 0.75rem 0.85rem; border-radius: 4px; margin-bottom: 1.25rem; flex: 1;">
                        <span class="eq-label" style="font-size: 0.75rem; text-transform: uppercase; font-weight: 800; color: #004098; display: block; margin-bottom: 0.25rem;">
                            Thiết bị chính:
                        </span>
                        <p class="eq-val" style="margin: 0; font-size: 0.85rem; line-height: 1.5; color: var(--slate-700);">
                            <?= e($pj['supplied_items']) ?>
                        </p>
                    </div>

                    <div class="project-footer" style="display: flex; gap: 0.5rem; align-items: center; margin-top: auto;">
                        <a href="<?= $detailUrl ?>" class="btn-project-quote" style="flex: 1; text-align: center; background: #004098; color: #ffffff; padding: 0.65rem 0.65rem; border-radius: 6px; font-weight: 800; font-size: 0.85rem; text-decoration: none; box-shadow: 0 2px 6px rgba(0,64,152,0.2);">
                            Xem bài viết & hình ảnh thi công →
                        </a>
                        <a href="<?= url('/bao-gia-cong-trinh') ?>" style="padding: 0.65rem 0.75rem; border: 1.5px solid #cbd5e1; background: #fff; color: #334155; border-radius: 6px; font-weight: 700; font-size: 0.825rem; text-decoration: none; white-space: nowrap;" title="Gửi yêu cầu báo giá">
                            Báo giá
                        </a>
                    </div>
                </div>
            </article>
        <?php endforeach; ?>
    </div>

    <!-- Banner kêu gọi gửi bóc tách vật tư -->
    <div style="margin-top: 3.5rem; background: linear-gradient(135deg, #021b3d 0%, #004098 100%); border-radius: var(--radius-xl); padding: 2.5rem; color: #ffffff; display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 2rem; box-shadow: 0 15px 35px rgba(0,64,152,0.2);">
        <div style="max-width: 650px;">
            <span style="font-size: 0.825rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.8px; color: #38bdf8;">DÀNH CHO CHỦ ĐẦU TƯ & NHÀ THẦU CƠ ĐIỆN (M&E)</span>
            <h3 style="font-size: 1.75rem; font-weight: 900; margin: 0.5rem 0 0.85rem; color: #ffffff;">Bạn đang chuẩn bị thi công công trình tại TP.HCM hoặc Long An?</h3>
            <p style="margin: 0; font-size: 0.975rem; line-height: 1.6; color: #e2e8f0;">
                Gửi ngay file dự toán Excel (.xlsx) hoặc bản vẽ thiết kế để đội ngũ kỹ sư điện Panasonic tại tổng kho TP.HCM và chi nhánh Long An bóc tách khối lượng và áp mức chiết khấu nhà thầu tốt nhất trong vòng 2 giờ!
            </p>
        </div>
        <div>
            <a href="<?= url('/bao-gia-cong-trinh') ?>" style="display: inline-flex; align-items: center; gap: 0.5rem; background: #ffffff; color: #004098; padding: 0.9rem 1.85rem; border-radius: var(--radius-md); font-weight: 800; font-size: 1rem; text-decoration: none; box-shadow: 0 6px 20px rgba(0,0,0,0.25);">
                <?= icon('upload') ?> GỬI BẢN VẼ / BẢNG VẬT TƯ »
            </a>
        </div>
    </div>
</div>

<script>
function filterProjects(area, btn) {
    document.querySelectorAll('.btn-filter-pj').forEach(b => {
        b.style.background = '#fff';
        b.style.color = '#334155';
        b.style.border = '1.5px solid #cbd5e1';
    });
    btn.style.background = '#004098';
    btn.style.color = '#fff';
    btn.style.border = 'none';

    const cards = document.querySelectorAll('#projectsGrid .project-card');
    cards.forEach(card => {
        if (area === 'all') {
            card.style.display = 'flex';
        } else if (area === 'tphcm') {
            card.style.display = card.classList.contains('area-tphcm') ? 'flex' : 'none';
        } else if (area === 'longan') {
            card.style.display = card.classList.contains('area-longan') ? 'flex' : 'none';
        }
    });
}
</script>
