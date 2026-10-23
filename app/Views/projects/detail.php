<?php
/** @var array $project */
/** @var array $related */
/** @var string $title */

$gallery = [];
if (!empty($project['gallery'])) {
    if (is_array($project['gallery'])) {
        $gallery = $project['gallery'];
    } else {
        $decoded = json_decode((string)$project['gallery'], true);
        $gallery = is_array($decoded) ? $decoded : [];
    }
}
if (empty($gallery) && !empty($project['thumbnail'])) {
    $gallery[] = $project['thumbnail'];
}
$mainImage = $gallery[0] ?? $project['thumbnail'];
?>

<div class="container project-detail-page" style="padding-top: 1.5rem; padding-bottom: 3.5rem;">
    <!-- Breadcrumb -->
    <nav class="breadcrumb" aria-label="Đường dẫn" style="margin-bottom: 1.5rem; font-size: 0.875rem;">
        <a href="<?= url('/') ?>" style="color: #64748b; text-decoration: none;">Trang chủ</a>
        <span style="color: #cbd5e1; margin: 0 0.5rem;">/</span>
        <a href="<?= url('/du-an') ?>" style="color: #64748b; text-decoration: none;">Dự án tiêu biểu</a>
        <span style="color: #cbd5e1; margin: 0 0.5rem;">/</span>
        <span style="color: #004098; font-weight: 600;"><?= e($project['title']) ?></span>
    </nav>

    <!-- Header Bài Viết Dự Án -->
    <div style="margin-bottom: 2rem;">
        <div style="display: flex; gap: 0.5rem; align-items: center; margin-bottom: 0.65rem; flex-wrap: wrap;">
            <span style="background: #e0f2fe; color: #004098; font-size: 0.8rem; font-weight: 800; padding: 4px 12px; border-radius: 99px; text-transform: uppercase;">
                ✓ DỰ ÁN ĐÃ THI CÔNG HOÀN THIỆN
            </span>
            <span style="background: #f1f5f9; color: #475569; font-size: 0.8rem; font-weight: 600; padding: 4px 12px; border-radius: 99px;">
                <?= e($project['category']) ?>
            </span>
            <?php if (!empty($project['completion_year'])): ?>
                <span style="background: #ecfdf5; color: #059669; font-size: 0.8rem; font-weight: 600; padding: 4px 12px; border-radius: 99px;">
                    Hoàn thành: <?= e((string)$project['completion_year']) ?>
                </span>
            <?php endif; ?>
        </div>

        <h1 style="font-size: 2.2rem; font-weight: 900; color: #0f172a; line-height: 1.3; margin: 0 0 1rem;">
            <?= e($project['title']) ?>
        </h1>

        <!-- Thanh thông tin dự án dạng bảng ngang -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1rem; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 1.25rem;">
            <div>
                <small style="color: #64748b; font-size: 0.78rem; text-transform: uppercase; font-weight: 700; display: block;">Chủ Đầu Tư</small>
                <strong style="color: #0f172a; font-size: 0.95rem;"><?= e($project['client_name']) ?></strong>
            </div>
            <div>
                <small style="color: #64748b; font-size: 0.78rem; text-transform: uppercase; font-weight: 700; display: block;">Địa Điểm Công Trình</small>
                <strong style="color: #0284c7; font-size: 0.95rem;"><?= icon('map-pin') ?> <?= e($project['location']) ?></strong>
            </div>
            <?php if (!empty($project['scale'])): ?>
                <div>
                    <small style="color: #64748b; font-size: 0.78rem; text-transform: uppercase; font-weight: 700; display: block;">Quy Mô Công Trình</small>
                    <strong style="color: #0f172a; font-size: 0.95rem;"><?= e($project['scale']) ?></strong>
                </div>
            <?php endif; ?>
            <div>
                <small style="color: #64748b; font-size: 0.78rem; text-transform: uppercase; font-weight: 700; display: block;">Đơn Vị Cung Cấp Vật Tư</small>
                <strong style="color: #004098; font-size: 0.95rem;">Tổng Kho Panasonic Chính Hãng</strong>
            </div>
        </div>
    </div>

    <!-- BỐ CỤC 2 CỘT: BÀI VIẾT & HÌNH ẢNH VẬT LIỆU THI CÔNG -->
    <div style="display: grid; grid-template-columns: minmax(0, 1fr) 360px; gap: 2.25rem; align-items: start;" class="project-detail-layout">

        <!-- CỘT TRÁI: HÌNH ẢNH VẬT LIỆU ĐÃ THI CÔNG & BÀI VIẾT CHI TIẾT -->
        <div>
            <!-- KHỐI GALLERY: HÌNH ẢNH NHỮNG VẬT LIỆU ĐÃ THI CÔNG BẰNG PANASONIC -->
            <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 1.5rem; margin-bottom: 2rem; box-shadow: 0 4px 15px rgba(0,0,0,0.04);">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem; border-bottom: 1.5px solid #f1f5f9; padding-bottom: 0.75rem;">
                    <h2 style="font-size: 1.15rem; font-weight: 800; color: #004098; margin: 0; display: flex; align-items: center; gap: 0.5rem;">
                        📸 Hình Ảnh Những Vật Liệu Đã Thi Công Bằng Panasonic
                    </h2>
                    <span style="font-size: 0.8rem; color: #64748b;"><?= count($gallery) ?> hình ảnh thực tế</span>
                </div>

                <!-- Ảnh lớn đang xem -->
                <div style="position: relative; width: 100%; aspect-ratio: 16/10; border-radius: 10px; overflow: hidden; background: #0f172a; margin-bottom: 1rem; border: 1px solid #cbd5e1;">
                    <img id="mainProjectImg" src="<?= media($mainImage) ?>" alt="<?= e($project['title']) ?>" style="width: 100%; height: 100%; object-fit: contain;">
                    <div style="position: absolute; bottom: 10px; left: 14px; background: rgba(0,0,0,0.7); backdrop-filter: blur(4px); color: #fff; padding: 4px 12px; border-radius: 6px; font-size: 0.8rem; font-weight: 600;">
                        Hình ảnh vật tư thiết bị điện Panasonic thực tế tại công trình
                    </div>
                </div>

                <!-- Dải Thumbnail chuyển ảnh vật liệu -->
                <div style="display: flex; gap: 0.65rem; overflow-x: auto; padding-bottom: 6px;" class="project-thumb-slider">
                    <?php foreach ($gallery as $idx => $img): ?>
                        <button type="button" onclick="switchProjectImg(this, '<?= media($img) ?>')" class="pj-thumb-btn <?= $idx === 0 ? 'is-active' : '' ?>" style="width: 90px; height: 70px; border-radius: 6px; overflow: hidden; border: 2px solid <?= $idx === 0 ? '#004098' : '#e2e8f0' ?>; background: #fff; padding: 2px; cursor: pointer; flex-shrink: 0; transition: all 0.2s;">
                            <img src="<?= media($img) ?>" alt="Vật liệu Panasonic" style="width: 100%; height: 100%; object-fit: contain;">
                        </button>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- KHỐI DANH MỤC THIẾT BỊ PANASONIC ĐÃ THI CÔNG LẮP ĐẶT -->
            <div style="background: #eff6ff; border-left: 4px solid #004098; border-radius: 8px; padding: 1.25rem 1.5rem; margin-bottom: 2rem;">
                <h3 style="font-size: 1.05rem; font-weight: 800; color: #004098; margin: 0 0 0.5rem; display: flex; align-items: center; gap: 0.4rem;">
                    ⚡ Danh Mục Vật Tư Thiết Bị Điện Panasonic Đã Cung Cấp:
                </h3>
                <p style="margin: 0; font-size: 0.95rem; line-height: 1.6; color: #1e3a8a; font-weight: 500;">
                    <?= e($project['supplied_items']) ?>
                </p>
            </div>

            <!-- BÀI VIẾT BÁO CÁO THI CÔNG CÔNG TRÌNH -->
            <article class="project-article-body" style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 2rem; box-shadow: 0 4px 15px rgba(0,0,0,0.04); font-size: 1rem; line-height: 1.7; color: #334155;">
                <?php if (!empty($project['content'])): ?>
                    <?= $project['content'] ?>
                <?php else: ?>
                    <p><?= nl2br(e($project['description'] ?? '')) ?></p>
                <?php endif; ?>
            </article>

            <!-- KHỐI HÀNH ĐỘNG KÊU GỌI BÁO GIÁ CÔNG TRÌNH -->
            <div style="margin-top: 2.5rem; background: linear-gradient(135deg, #002b66 0%, #004098 100%); border-radius: 12px; padding: 2rem; color: #ffffff; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1.5rem; box-shadow: 0 10px 25px rgba(0,64,152,0.25);">
                <div>
                    <h3 style="font-size: 1.35rem; font-weight: 800; margin: 0 0 0.4rem; color: #ffffff;">
                        Cần Báo Giá Cấu Hình Thiết Bị Tương Đương Công Trình Này?
                    </h3>
                    <p style="margin: 0; font-size: 0.9rem; color: #e0f2fe;">
                        Gửi ngay file dự toán khối lượng để kỹ sư Panasonic áp dụng chiết khấu nhà thầu tốt nhất.
                    </p>
                </div>
                <div style="display: flex; gap: 0.75rem;">
                    <a href="<?= url('/bao-gia-cong-trinh') ?>" style="background: #ffffff; color: #004098; padding: 0.75rem 1.5rem; border-radius: 6px; font-weight: 800; font-size: 0.95rem; text-decoration: none; box-shadow: 0 4px 12px rgba(0,0,0,0.15);">
                        BÁO GIÁ DỰ ÁN NGAY »
                    </a>
                </div>
            </div>
        </div>

        <!-- CỘT PHẢI: THÔNG TIN HỖ TRỢ & CÁC DỰ ÁN KHÁC -->
        <div style="display: flex; flex-direction: column; gap: 1.5rem; position: sticky; top: 1.5rem;">
            <!-- THẺ TƯ VẤN KỸ THUẬT -->
            <div style="background: #ffffff; border: 1px solid #cbd5e1; border-radius: 12px; padding: 1.5rem; box-shadow: 0 4px 15px rgba(0,0,0,0.04);">
                <h4 style="font-size: 1rem; font-weight: 800; color: #0f172a; margin: 0 0 0.5rem;">
                    KỸ SƯ TƯ VẤN DỰ ÁN
                </h4>
                <p style="font-size: 0.85rem; color: #64748b; margin: 0 0 1rem; line-height: 1.45;">
                    Bộ phận kỹ thuật Panasonic sẵn sàng tư vấn bóc tách bản vẽ và đề xuất phương án tối ưu chi phí:
                </p>
                <div style="display: flex; flex-direction: column; gap: 0.65rem;">
                    <a href="tel:0869800299" style="background: #dc2626; color: #ffffff; text-decoration: none; padding: 0.75rem 1rem; border-radius: 6px; font-weight: 700; font-size: 0.9rem; display: flex; align-items: center; justify-content: center; gap: 0.5rem;">
                        📞 Hotline Dự Án: 0869.800.299
                    </a>
                    <a href="https://zalo.me/0798966958" target="_blank" rel="noopener" style="background: #0284c7; color: #ffffff; text-decoration: none; padding: 0.75rem 1rem; border-radius: 6px; font-weight: 700; font-size: 0.9rem; display: flex; align-items: center; justify-content: center; gap: 0.5rem;">
                        💬 Gửi file qua Zalo Kỹ Thuật
                    </a>
                </div>
            </div>

            <!-- THẺ DỰ ÁN TIÊU BIỂU LIÊN QUAN -->
            <?php if (!empty($related)): ?>
                <div style="background: #ffffff; border: 1px solid #cbd5e1; border-radius: 12px; padding: 1.25rem; box-shadow: 0 4px 15px rgba(0,0,0,0.04);">
                    <h4 style="font-size: 0.95rem; font-weight: 800; color: #004098; margin: 0 0 1rem; text-transform: uppercase;">
                        Dự Án Đã Làm Khác
                    </h4>
                    <div style="display: flex; flex-direction: column; gap: 1rem;">
                        <?php foreach ($related as $rel): ?>
                            <?php if ($rel['id'] !== $project['id']): ?>
                                <a href="<?= url('/du-an/' . $rel['slug']) ?>" style="display: flex; gap: 0.75rem; text-decoration: none; color: inherit; align-items: center;">
                                    <img src="<?= media($rel['thumbnail']) ?>" alt="<?= e($rel['title']) ?>" style="width: 75px; height: 55px; object-fit: cover; border-radius: 6px; flex-shrink: 0; border: 1px solid #e2e8f0;">
                                    <div>
                                        <strong style="font-size: 0.85rem; color: #0f172a; display: block; line-height: 1.35; margin-bottom: 2px;">
                                            <?= mb_strimwidth($rel['title'], 0, 45, '...') ?>
                                        </strong>
                                        <small style="color: #64748b; font-size: 0.75rem;"><?= e($rel['location']) ?></small>
                                    </div>
                                </a>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>

    </div>
</div>

<style>
.project-article-body h2 {
    font-size: 1.3rem;
    font-weight: 800;
    color: #004098;
    margin-top: 1.5rem;
    margin-bottom: 0.75rem;
    border-bottom: 1.5px solid #e2e8f0;
    padding-bottom: 0.4rem;
}
.project-article-body h3 {
    font-size: 1.1rem;
    font-weight: 700;
    color: #0f172a;
    margin-top: 1.25rem;
    margin-bottom: 0.5rem;
}
.project-article-body ul {
    margin: 0.75rem 0 1.25rem;
    padding-left: 1.5rem;
}
.project-article-body li {
    margin-bottom: 0.5rem;
    line-height: 1.6;
}

@media (max-width: 992px) {
    .project-detail-layout {
        grid-template-columns: 1fr !important;
    }
}
</style>

<script>
function switchProjectImg(btn, imgUrl) {
    document.querySelectorAll('.pj-thumb-btn').forEach(b => {
        b.style.borderColor = '#e2e8f0';
        b.classList.remove('is-active');
    });
    btn.style.borderColor = '#004098';
    btn.classList.add('is-active');

    const mainImg = document.getElementById('mainProjectImg');
    if (mainImg) {
        mainImg.src = imgUrl;
    }
}
</script>
