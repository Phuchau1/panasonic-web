<?php
/** @var array $product */
/** @var array $related */

$bodyClass = 'page-product-detail';
$discountPercent = $product['discount_percent'] ?? 0;
if ($discountPercent <= 0 && $product['price'] > 0) {
    // Generate attractive catalog discount (12% - 25% typical for Panasonic distribution)
    $discountPercent = 15;
}
$originalPrice = (int) ($product['price'] > 0 ? ($product['price'] / (1 - ($discountPercent / 100))) : 0);
?>

<div class="page-header page-header--detail">
    <div class="container">
        <nav class="breadcrumb" aria-label="Đường dẫn trang">
            <a href="<?= url('/') ?>"><?= icon('home') ?> Trang chủ</a>
            <span class="breadcrumb__sep"><?= icon('chevron-right') ?></span>
            <a href="<?= url('/danh-muc/' . $product['category_slug']) ?>"><?= e($product['category_name']) ?></a>
            <?php if (!empty($product['series_name'])): ?>
                <span class="breadcrumb__sep"><?= icon('chevron-right') ?></span>
                <a href="<?= url('/panasonic-' . $product['series_slug']) ?>"><?= e($product['series_name']) ?></a>
            <?php endif; ?>
            <span class="breadcrumb__sep"><?= icon('chevron-right') ?></span>
            <span class="breadcrumb__current"><?= e($product['model_code'] ?: $product['name']) ?></span>
        </nav>
    </div>
</div>

<div class="container section pt-0">
    <!-- KHỐI ĐẦU TRANG: 3 CỘT (HÌNH ẢNH | THÔNG TIN & ĐẶT HÀNG | CHÍNH SÁCH & HOTLINE) -->
    <div class="product-detail-layout">
        <!-- Cột 1: Gallery Hình Ảnh -->
        <div class="pdetail-media">
            <div class="pdetail-gallery">
                <div class="pdetail-gallery__main">
                    <img id="mainDetailImg" src="<?= media($product['thumbnail']) ?>" alt="<?= e($product['name']) ?>" loading="eager">
                    <span class="pdetail-badge-genuine">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                        Chính Hãng 100%
                    </span>
                    <?php if ($discountPercent > 0): ?>
                        <span class="pdetail-badge-discount">-<?= (int)$discountPercent ?>%</span>
                    <?php endif; ?>
                </div>

                <?php 
                $galleryImages = [];
                if (!empty($product['thumbnail'])) $galleryImages[] = $product['thumbnail'];
                if (!empty($product['gallery']) && is_array($product['gallery'])) {
                    foreach ($product['gallery'] as $g) {
                        if (!in_array($g, $galleryImages)) $galleryImages[] = $g;
                    }
                }
                ?>
                <?php if (count($galleryImages) > 1): ?>
                    <div class="pdetail-gallery__thumbs">
                        <?php foreach ($galleryImages as $idx => $thumb): ?>
                            <button type="button" class="pdetail-thumb-btn <?= $idx === 0 ? 'is-active' : '' ?>" onclick="switchDetailThumb(this, '<?= media($thumb) ?>')">
                                <img src="<?= media($thumb) ?>" alt="<?= e($product['name']) ?>">
                            </button>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="pdetail-share-box">
                <span class="share-label">Chia sẻ:</span>
                <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode(full_url($_SERVER['REQUEST_URI'])) ?>" target="_blank" rel="noopener" class="share-btn share-btn--fb" title="Chia sẻ lên Facebook">Facebook</a>
                <a href="https://zalo.me/share?url=<?= urlencode(full_url($_SERVER['REQUEST_URI'])) ?>" target="_blank" rel="noopener" class="share-btn share-btn--zalo" title="Gửi qua Zalo">Zalo</a>
                <button type="button" class="share-btn share-btn--copy" onclick="copyProductLink()" title="Sao chép liên kết">Copy Link</button>
            </div>
        </div>

        <!-- Cột 2: Thông Tin Sản Phẩm & Nút Mua -->
        <div class="pdetail-center">
            <div class="pdetail-meta-tags">
                <?php if (!empty($product['model_code'])): ?>
                    <span class="meta-tag meta-tag--model">Model: <strong><?= e($product['model_code']) ?></strong></span>
                <?php endif; ?>
                <?php if (!empty($product['sku'])): ?>
                    <span class="meta-tag meta-tag--sku">Mã SKU: <?= e($product['sku']) ?></span>
                <?php endif; ?>
                <?php if (!empty($product['series_name'])): ?>
                    <span class="meta-tag meta-tag--series"><?= e($product['series_name']) ?></span>
                <?php endif; ?>
            </div>

            <h1 class="pdetail-title"><?= e($product['name']) ?></h1>

            <div class="pdetail-rating-row">
                <?php
                    $avgScore = (float)($reviewStats['avg'] ?? ($product['rating_avg'] ?? 5.0));
                    $totalReviews = (int)($reviewStats['total'] ?? ($product['reviews_count'] ?? count($reviews ?? [])));
                    $starCount = (int)round($avgScore);
                ?>
                <div class="pdetail-stars" title="<?= number_format($avgScore, 1) ?> / 5 sao">
                    <?= str_repeat('★', $starCount) . str_repeat('☆', 5 - $starCount) ?>
                </div>
                <span class="pdetail-rating-score"><?= number_format($avgScore, 1) ?></span>
                <button type="button" class="pdetail-rating-count-btn" onclick="switchDetailTab('tab-reviews', document.querySelector('[data-tab-target=\'tab-reviews\']'))">
                    (<?= $totalReviews ?> đánh giá thực tế)
                </button>
                <span class="pdetail-status-pill <?= $product['stock'] > 0 ? 'in-stock' : 'out-stock' ?>">
                    <span class="dot"></span> <?= $product['stock'] > 0 ? 'Còn hàng tại kho' : 'Liên hệ đặt hàng' ?>
                </span>
            </div>

            <!-- Khối Giá Bán -->
            <div class="pdetail-price-box">
                <div class="pdetail-price-row">
                    <span class="pdetail-price-current"><?= money($product['price']) ?></span>
                    <?php if ($originalPrice > $product['price']): ?>
                        <span class="pdetail-price-original"><?= money($originalPrice) ?></span>
                    <?php endif; ?>
                    <span class="pdetail-price-vat">/ <?= e($product['unit']) ?> (Đã gồm VAT)</span>
                </div>
                <?php if ($product['wholesale_price'] > 0): ?>
                    <div class="pdetail-wholesale-banner">
                        <span class="wholesale-tag">GIÁ ĐẠI LÝ / CÔNG TRÌNH:</span>
                        <strong class="wholesale-val"><?= money($product['wholesale_price']) ?></strong>
                        <span class="wholesale-min">(Áp dụng từ <?= (int)$product['wholesale_min_qty'] ?> <?= e($product['unit']) ?>)</span>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Checklist Đặc Điểm Nổi Bật -->
            <div class="pdetail-checklist">
                <ul class="checklist-items">
                    <?php if (!empty($product['poles'])): ?>
                        <li><span class="check-icon">✔</span> Số cực: <strong><?= (int)$product['poles'] ?>P</strong></li>
                    <?php endif; ?>
                    <?php if (!empty($product['rated_current'])): ?>
                        <li><span class="check-icon">✔</span> Dòng định mức: <strong><?= (float)$product['rated_current'] ?>A</strong></li>
                    <?php endif; ?>
                    <?php if (!empty($product['breaking_capacity'])): ?>
                        <li><span class="check-icon">✔</span> Dòng cắt ngắn mạch: <strong><?= (float)$product['breaking_capacity'] ?>kA</strong></li>
                    <?php endif; ?>
                    <li><span class="check-icon">✔</span> Bán đúng giá niêm yết - Giao hàng nhanh toàn quốc</li>
                    <li><span class="check-icon">✔</span> Cam kết 100% hàng chính hãng Panasonic - Đầy đủ CO/CQ</li>
                    <li><span class="check-icon">✔</span> Bảo hành chính hãng <strong>24 tháng</strong> (Đổi mới nếu lỗi kỹ thuật)</li>
                    <li><span class="check-icon">✔</span> Chiết khấu hấp dẫn cho nhà thầu, công trình xây dựng và đại lý</li>
                </ul>
            </div>

            <!-- Form Mua Hàng & Số Lượng -->
            <div class="pdetail-purchase-area">
                <div class="pdetail-qty-row">
                    <span class="qty-label">Số lượng:</span>
                    <div class="qty-stepper">
                        <button type="button" class="stepper-btn" onclick="adjustQty(-1)">-</button>
                        <input type="number" id="detailProductQty" value="1" min="1" max="<?= max(1, (int)$product['stock']) ?>">
                        <button type="button" class="stepper-btn" onclick="adjustQty(1)">+</button>
                    </div>
                    <span class="stock-note"><?= $product['stock'] > 0 ? "Sẵn sàng giao ngay ({$product['stock']} {$product['unit']})" : 'Liên hệ kho' ?></span>
                </div>

                <!-- Cặp Nút Hành Động Kép Chuẩn Thương Mại Điện Tử -->
                <div class="pdetail-cta-row">
                    <button type="button" class="btn-cta btn-cta--buynow" onclick="buyNow(<?= (int)$product['id'] ?>)">
                        <span class="cta-title">MUA NGAY</span>
                        <span class="cta-subtitle">Giao hàng tận nơi toàn quốc</span>
                    </button>
                    <a href="<?= url('/bao-gia-cong-trinh') ?>" class="btn-cta btn-cta--quote">
                        <span class="cta-title">TƯ VẤN BÁO GIÁ</span>
                        <span class="cta-subtitle">Chiết khấu dự án / công trình</span>
                    </a>
                </div>

                <div class="pdetail-cart-row">
                    <button type="button" class="btn-add-cart-secondary" data-add-cart="<?= (int)$product['id'] ?>" data-qty-source="#detailProductQty">
                        <?= icon('cart') ?> Thêm vào giỏ hàng
                    </button>
                    <a href="https://zalo.me/<?= e(config('zalo', '0798966958')) ?>" target="_blank" class="btn-zalo-secondary">
                        Chat Zalo nhận bảng giá
                    </a>
                </div>
            </div>
        </div>

        <!-- Cột 3: Khung Hỗ Trợ, Hotline & Chính Sách Giao Hàng -->
        <div class="pdetail-sidebar">
            <!-- Box Hotline -->
            <div class="sidebar-hotline-card">
                <div class="hotline-card__head">
                    <div class="hotline-icon">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3.1 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.1 4.2 2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7c.1.9.4 1.8.7 2.7a2 2 0 0 1-.5 2.1L8 9.8a16 16 0 0 0 6 6l1.3-1.3a2 2 0 0 1 2.1-.4c.9.3 1.8.6 2.7.7a2 2 0 0 1 1.7 2z"/></svg>
                    </div>
                    <div>
                        <div class="hotline-label">LIÊN HỆ TƯ VẤN & BÁO GIÁ</div>
                        <a href="tel:<?= e(preg_replace('/\D/', '', config('hotline_sales', '0869800299'))) ?>" class="hotline-phone"><?= e(config('hotline_sales', '0869.800.299')) ?></a>
                    </div>
                </div>
                <div class="hotline-card__sub">
                    <span>Tổng đài dự án: <a href="tel:<?= e(preg_replace('/\D/', '', config('hotline', '0798966958'))) ?>"><?= e(config('hotline', '0798.966.958')) ?></a></span>
                    <small>Làm việc 7:30 – 21:00 (Cả Thứ 7, CN)</small>
                </div>
            </div>

            <!-- Cam Kết Dịch Vụ -->
            <div class="sidebar-policy-card">
                <h4 class="policy-card__title">CAM KẾT DỊCH VỤ</h4>
                <ul class="policy-list">
                    <li>
                        <span class="policy-ico">🚚</span>
                        <div>
                            <strong>Vận chuyển toàn quốc</strong>
                            <p>Giao hỏa tốc 2h - 4h nội thành Hà Nội & TP.HCM</p>
                        </div>
                    </li>
                    <li>
                        <span class="policy-ico">🛡️</span>
                        <div>
                            <strong>100% Chính Hãng Panasonic</strong>
                            <p>Đền 200% giá trị nếu phát hiện hàng giả, hàng nhái</p>
                        </div>
                    </li>
                    <li>
                        <span class="policy-ico">🔄</span>
                        <div>
                            <strong>Đổi trả linh hoạt 7 ngày</strong>
                            <p>Đổi mới 1 - 1 nếu sản phẩm có lỗi từ nhà sản xuất</p>
                        </div>
                    </li>
                    <li>
                        <span class="policy-ico">📄</span>
                        <div>
                            <strong>Hồ sơ CO/CQ & Hóa đơn VAT</strong>
                            <p>Cung cấp đầy đủ chứng từ xuất xưởng cho công trình</p>
                        </div>
                    </li>
                </ul>
            </div>

            <!-- Download Catalogue PDF -->
            <div class="sidebar-catalog-card">
                <div class="catalog-card__info">
                    <span class="badge-pdf">PDF 2026</span>
                    <strong>Catalogue Panasonic 2026</strong>
                    <p>Bảng giá & thông số kỹ thuật đầy đủ</p>
                </div>
                <a href="<?= url('/tai-catalogue') ?>" class="btn-catalog-dl">
                    <?= icon('download') ?> Tải Catalogue
                </a>
            </div>
        </div>
    </div>

    <!-- KHỐI NỘI DUNG CHI TIẾT (MÔ TẢ & THÔNG SỐ - BẢN VẼ - PHÂN BIỆT THẬT GIẢ & ĐIỂM MUA) -->
    <div class="product-body-tabs">
        <div class="pbody-nav">
            <button type="button" class="pbody-tab-btn is-active" onclick="switchDetailTab('tab-desc', this)">
                <?= icon('file-text') ?> Mô Tả & Thông Số Kỹ Thuật
            </button>
            <button type="button" class="pbody-tab-btn" data-tab-target="tab-reviews" onclick="switchDetailTab('tab-reviews', this)">
                <?= icon('star') ?> Đánh Giá Thực Tế (<?= $totalReviews ?>)
            </button>
            <button type="button" class="pbody-tab-btn" onclick="switchDetailTab('tab-drawings', this)">
                <?= icon('grid') ?> Bản Vẽ Kỹ Thuật & Kích Thước
            </button>
            <button type="button" class="pbody-tab-btn" onclick="switchDetailTab('tab-genuine', this)">
                <?= icon('shield') ?> Chính Hãng & Điểm Mua Hàng
            </button>
        </div>

        <div class="pbody-content">
            <!-- Tab Gộp Duy Nhất: Mô Tả Chi Tiết & Thông Số Kỹ Thuật -->
            <div id="tab-desc" class="pbody-pane is-active">
                <!-- Bảng Thông Số Kỹ Thuật Chi Tiết -->
                <div class="specs-table-wrapper">
                    <div class="specs-table-head">
                        <span class="specs-head-icon"><?= icon('settings') ?></span>
                        <h3 class="specs-head-title">Thông Số Kỹ Thuật Chi Tiết</h3>
                        <span class="specs-head-badge">Chính Hãng Panasonic</span>
                    </div>
                    <table class="specs-modern-table">
                        <tbody>
                            <tr>
                                <th>Mã sản phẩm (Model Code)</th>
                                <td><strong><?= e($product['model_code'] ?: 'Đang cập nhật') ?></strong></td>
                            </tr>
                            <tr>
                                <th>Mã SKU quản lý</th>
                                <td><?= e($product['sku']) ?></td>
                            </tr>
                            <tr>
                                <th>Thương hiệu</th>
                                <td><strong>Panasonic (Nhật Bản)</strong></td>
                            </tr>
                            <tr>
                                <th>Chủng loại sản phẩm</th>
                                <td><?= e($product['category_name']) ?></td>
                            </tr>
                            <?php if (!empty($product['series_name'])): ?>
                            <tr>
                                <th>Dòng sản phẩm (Series)</th>
                                <td><?= e($product['series_name']) ?></td>
                            </tr>
                            <?php endif; ?>
                            <?php if (!empty($product['poles'])): ?>
                            <tr>
                                <th>Số cực (Poles)</th>
                                <td><?= (int)$product['poles'] ?>P</td>
                            </tr>
                            <?php endif; ?>
                            <?php if (!empty($product['rated_current'])): ?>
                            <tr>
                                <th>Dòng điện định mức (In)</th>
                                <td><?= (float)$product['rated_current'] ?> A</td>
                            </tr>
                            <?php endif; ?>
                            <?php if (!empty($product['breaking_capacity'])): ?>
                            <tr>
                                <th>Dòng cắt ngắn mạch (Icu/Icn)</th>
                                <td><?= (float)$product['breaking_capacity'] ?> kA</td>
                            </tr>
                            <?php endif; ?>
                            <tr>
                                <th>Điện áp định mức</th>
                                <td>240VAC / 415VAC - 50/60Hz</td>
                            </tr>
                            <tr>
                                <th>Tiêu chuẩn chất lượng</th>
                                <td>IEC 60898-1, IEC 60947-2, JIS C 8370</td>
                            </tr>
                            <tr>
                                <th>Tuổi thọ cơ học & điện</th>
                                <td>≥ 10.000 lần ngắt mạch / ≥ 40.000 lần bật tắt</td>
                            </tr>
                            <tr>
                                <th>Xuất xứ</th>
                                <td>Chính hãng Panasonic (Việt Nam / Thái Lan / Nhật Bản)</td>
                            </tr>
                            <tr>
                                <th>Quy cách đóng gói</th>
                                <td>1 Cái / Hộp (Đầy đủ tem niêm phong và hướng dẫn lắp đặt)</td>
                            </tr>
                            <tr>
                                <th>Chế độ bảo hành</th>
                                <td><strong>24 tháng chính hãng</strong> trên toàn quốc</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Ứng Dụng Thực Tế & Mô Tả Chi Tiết -->
                <div class="rich-content-article" style="border-top: 1.5px dashed var(--slate-200); padding-top: 2rem;">
                    <h3 style="display: flex; align-items: center; gap: 0.5rem; font-size: 1.15rem; font-weight: 800; color: #004098; margin-top: 0; margin-bottom: 1.25rem;">
                        <?= icon('file-text') ?> Ứng Dụng Thực Tế & Mô Tả Chi Tiết
                    </h3>
                    <?php if (!empty($product['description'])): ?>
                        <?= format_product_description($product['description'], $product['name']) ?>
                    <?php else: ?>
                        <h3>Ứng Dụng Thực Tế</h3>
                        <p><?= e($product['name']) ?> là dòng thiết bị điện chính hãng từ tập đoàn Panasonic, được thiết kế và chế tạo đáp ứng các tiêu chuẩn khắt khe về an toàn và độ bền bỉ của Nhật Bản (JIS) cũng như tiêu chuẩn quốc tế (IEC).</p>
                        <p>Sản phẩm phù hợp lắp đặt cho mạng lưới điện hộ gia đình, căn hộ chung cư cao cấp, biệt thự, văn phòng, nhà xưởng và các công trình dự án thương mại.</p>
                        
                        <h3>Đặc Điểm Kỹ Thuật Nổi Bật</h3>
                        <ul>
                            <li><strong>Chất liệu cao cấp:</strong> Thân vỏ được chế tạo từ nhựa chống cháy Urea Resin hoặc Polycarbonate cao cấp, chịu nhiệt và chống biến dạng vượt trội.</li>
                            <li><strong>Tiếp điểm bền bỉ:</strong> Sử dụng hợp kim bạc nguyên chất giúp dập hồ quang tức thì, giảm thiểu mài mòn và nguy cơ phóng điện chập cháy.</li>
                            <li><strong>Thao tác nhẹ nhàng:</strong> Cơ chế bật tắt và cắm rút được tối ưu hóa cho độ êm ái tối đa, tuổi thọ lên tới hàng chục nghìn chu kỳ sử dụng.</li>
                            <li><strong>An toàn tuyệt đối:</strong> Thiết kế bảo vệ khép kín, ngăn chặn tối đa bụi bẩn, côn trùng xâm nhập và ngăn ngừa nguy cơ điện giật.</li>
                        </ul>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Tab: Đánh Giá & Nhận Xét Thực Tế -->
            <div id="tab-reviews" class="pbody-pane">
                <div class="reviews-section-wrapper">
                    <!-- Dashboard Thống Kê Tổng Quan -->
                    <div class="review-stats-card">
                        <div class="review-stats-score">
                            <div class="score-large" id="mainScoreNum"><?= number_format($avgScore, 1) ?></div>
                            <div class="score-stars" id="mainScoreStars">
                                <?= str_repeat('★', $starCount) . str_repeat('☆', 5 - $starCount) ?>
                            </div>
                            <div class="score-caption"><span id="mainReviewsCount"><?= $totalReviews ?></span> đánh giá thực tế</div>
                            <button type="button" class="btn-write-review" onclick="toggleReviewForm()">
                                <?= icon('edit') ?> Viết Đánh Giá Của Bạn
                            </button>
                        </div>
                        <div class="review-stats-bars">
                            <?php for ($s = 5; $s >= 1; $s--): 
                                $cnt = $reviewStats['counts'][$s] ?? 0;
                                $pct = $reviewStats['percent'][$s] ?? 0;
                            ?>
                                <div class="review-bar-row">
                                    <span class="bar-label"><?= $s ?> sao</span>
                                    <div class="bar-track">
                                        <div class="bar-fill" style="width: <?= $pct ?>%;"></div>
                                    </div>
                                    <span class="bar-count"><?= $cnt ?> (<?= $pct ?>%)</span>
                                </div>
                            <?php endfor; ?>
                        </div>
                    </div>

                    <!-- Form Nhập Đánh Giá Mới (Ẩn/Hiện) -->
                    <div id="reviewFormWrap" class="review-form-wrap" style="display: none;">
                        <div class="review-form-card">
                            <h4 class="form-title">Đánh Giá Sản Phẩm & Trải Nghiệm Thực Tế</h4>
                            <p class="form-subtitle">Ý kiến của Quý khách giúp hoàn thiện chất lượng dịch vụ và hỗ trợ các kỹ sư công trình lựa chọn chính xác.</p>
                            <form id="ajaxReviewForm" onsubmit="submitReview(event, <?= (int)$product['id'] ?>)">
                                <div class="star-rating-picker-row">
                                    <span class="picker-label">Mức độ hài lòng của bạn:</span>
                                    <div class="star-picker" id="starPicker">
                                        <button type="button" class="picker-star is-active" data-value="1" onclick="selectStar(1)">★</button>
                                        <button type="button" class="picker-star is-active" data-value="2" onclick="selectStar(2)">★</button>
                                        <button type="button" class="picker-star is-active" data-value="3" onclick="selectStar(3)">★</button>
                                        <button type="button" class="picker-star is-active" data-value="4" onclick="selectStar(4)">★</button>
                                        <button type="button" class="picker-star is-active" data-value="5" onclick="selectStar(5)">★</button>
                                    </div>
                                    <input type="hidden" name="rating" id="selectedRatingInput" value="5">
                                    <span class="picker-hint" id="ratingHintText">Tuyệt vời - Rất hài lòng</span>
                                </div>

                                <div class="form-grid-2">
                                    <div class="form-group">
                                        <label for="reviewAuthor">Họ và tên của bạn <span class="required">*</span></label>
                                        <input type="text" id="reviewAuthor" name="author_name" class="form-control" placeholder="Ví dụ: Anh Nguyễn Văn Tuấn (Kỹ sư điện / Chủ hộ)" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="reviewPhone">Số điện thoại</label>
                                        <input type="tel" id="reviewPhone" name="author_phone" class="form-control" placeholder="Để xác thực huy hiệu Khách Đã Mua (bảo mật)">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="reviewComment">Nội dung nhận xét <span class="required">*</span></label>
                                    <textarea id="reviewComment" name="comment" rows="3" class="form-control" placeholder="Chia sẻ cảm nhận về chất lượng hoàn thiện, độ nhạy, tính an toàn, đóng gói giao hàng..." required></textarea>
                                </div>

                                <div class="form-actions">
                                    <button type="submit" class="btn-submit-review" id="btnSubmitReview">
                                        Gửi Đánh Giá Ngay
                                    </button>
                                    <button type="button" class="btn-cancel-review" onclick="toggleReviewForm()">Hủy bỏ</button>
                                    <span id="reviewFormMessage" class="review-form-msg"></span>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Danh Sách Đánh Giá Đã Có -->
                    <div class="reviews-list-card" id="reviewsListContainer">
                        <div class="reviews-list-head">
                            <h4>Nhận Xét Từ Khách Hàng & Công Trình Thực Tế (<span id="reviewsCountBadge"><?= $totalReviews ?></span>)</h4>
                        </div>
                        <?php if (empty($reviews)): ?>
                            <div class="no-reviews-box" id="noReviewsNotice">
                                <p>Sản phẩm chưa có đánh giá nào. Hãy là người đầu tiên trải nghiệm và để lại nhận xét!</p>
                            </div>
                        <?php else: ?>
                            <div class="reviews-items" id="reviewsItemsList">
                                <?php foreach ($reviews as $rv): ?>
                                    <div class="review-item">
                                        <div class="review-item-header">
                                            <div class="review-avatar">
                                                <?php
                                                    $words = preg_split('/\s+/u', trim($rv['author_name']));
                                                    $initials = mb_substr($words[0] ?? '', 0, 1, 'UTF-8');
                                                    if (count($words) > 1) {
                                                        $initials .= mb_substr(end($words), 0, 1, 'UTF-8');
                                                    }
                                                    echo e(mb_strtoupper($initials, 'UTF-8'));
                                                ?>
                                            </div>
                                            <div class="review-user-info">
                                                <div class="user-name-row">
                                                    <strong><?= e($rv['author_name']) ?></strong>
                                                    <?php if (!empty($rv['is_verified_buyer'])): ?>
                                                        <span class="badge-verified">✔ Đã mua hàng tại Tổng Kho</span>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="review-meta-row">
                                                    <div class="review-stars-sm">
                                                        <?= str_repeat('★', (int)$rv['rating']) . str_repeat('☆', 5 - (int)$rv['rating']) ?>
                                                    </div>
                                                    <span class="review-date"><?= date('d/m/Y', strtotime($rv['created_at'])) ?></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="review-body">
                                            <p><?= nl2br(e($rv['comment'])) ?></p>
                                            <?php if (!empty($rv['admin_reply'])): ?>
                                                <div class="review-admin-reply" style="margin-top: 0.85rem; background: #f8fafc; border-left: 3px solid #004098; padding: 0.75rem 1rem; border-radius: 6px;">
                                                    <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.35rem; flex-wrap: wrap;">
                                                        <span style="background: #004098; color: #ffffff; font-size: 0.72rem; font-weight: 700; padding: 2px 7px; border-radius: 4px; letter-spacing: 0.3px;">PANASONIC OFFICIAL</span>
                                                        <strong style="color: #0f172a; font-size: 0.825rem;">Phản hồi từ Quản trị viên</strong>
                                                        <?php if (!empty($rv['admin_reply_at'])): ?>
                                                            <span style="color: #94a3b8; font-size: 0.75rem; margin-left: auto;"><?= date('d/m/Y', strtotime($rv['admin_reply_at'])) ?></span>
                                                        <?php endif; ?>
                                                    </div>
                                                    <div style="color: #334155; font-size: 0.875rem; line-height: 1.5; white-space: pre-line;"><?= e($rv['admin_reply']) ?></div>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Tab 3: Bản Vẽ Kỹ Thuật & Kích Thước -->
            <div id="tab-drawings" class="pbody-pane">
                <div class="technical-drawings-block">
                    <h3>Bản Vẽ Kỹ Thuật & Kích Thước Lắp Đặt</h3>
                    <p>Sơ đồ kích thước 2D và kết cấu lắp đặt tiêu chuẩn của thiết bị điện Panasonic:</p>
                    <div class="technical-drawing-grid">
                        <div class="drawing-item">
                            <img src="<?= asset('uploads/products/technical-drawing-panasonic.svg') ?>" alt="Bản vẽ kỹ thuật Panasonic" onerror="this.src='<?= media($product['thumbnail']) ?>'">
                            <span>Sơ đồ kích thước chi tiết (mm)</span>
                        </div>
                        <div class="drawing-item">
                            <img src="<?= asset('uploads/products/curve-characteristic-panasonic.svg') ?>" alt="Đường đặc tính bảo vệ Panasonic" onerror="this.src='<?= media($product['thumbnail']) ?>'">
                            <span>Biểu đồ đặc tính thời gian tác động (Operating curve)</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tab Gộp Duy Nhất: Phân Biệt Hàng Thật & Điểm Mua Hàng -->
            <div id="tab-genuine" class="pbody-pane">
                <div class="genuine-guide-block">
                    <div class="genuine-guide-head">
                        <h3>Hướng Dẫn Nhận Biết Thiết Bị Điện Panasonic Chính Hãng</h3>
                        <p>Để đảm bảo an toàn phòng chống cháy nổ và quyền lợi bảo hành, Quý khách hàng lưu ý các dấu hiệu nhận biết hàng chính hãng Panasonic:</p>
                    </div>

                    <div class="genuine-comparison-grid">
                        <div class="genuine-card genuine-card--real">
                            <div class="genuine-card__title">✔ SẢN PHẨM CHÍNH HÃNG (GENUINE)</div>
                            <ul class="genuine-card__list">
                                <li><strong>Logo Panasonic:</strong> Được khắc laser hoặc in chìm sắc nét, các nét chữ đều đặn, không nhòe mờ.</li>
                                <li><strong>Tem chống giả:</strong> Tem công nghệ nhiệt & quang học, có mã QR tra cứu nguồn gốc xuất xứ rõ ràng.</li>
                                <li><strong>Ốc vít & Cực đấu nối:</strong> Chế tạo từ đồng thau hoặc hợp kim mạ kẽm cao cấp, ren ốc mịn và chắc chắn.</li>
                                <li><strong>Chất liệu vỏ:</strong> Bề mặt nhựa đanh chắc, màu trắng sứ tự nhiên, không ba-via, cầm chắc tay.</li>
                                <li><strong>Chứng từ kèm theo:</strong> Đầy đủ hóa đơn GTGT (VAT) và phiếu chứng nhận xuất xưởng (CO/CQ).</li>
                            </ul>
                        </div>
                        <div class="genuine-card genuine-card--fake">
                            <div class="genuine-card__title">✖ HÀNG GIẢ / HÀNG NHÁI (FAKE)</div>
                            <ul class="genuine-card__list">
                                <li><strong>Logo Panasonic:</strong> In mực nổi sơ sài, chữ bị lệch méo hoặc dễ dàng cạo tróc bằng móng tay.</li>
                                <li><strong>Tem nhãn:</strong> Tem in màu thông thường, không có hiệu ứng quang học, mã QR không dẫn tới hệ thống chính hãng.</li>
                                <li><strong>Ốc vít:</strong> Ốc sắt xi mạ mỏng, dễ bị trờn ren khi siết lực mạnh, dễ rỉ sét sau một thời gian.</li>
                                <li><strong>Chất liệu vỏ:</strong> Nhựa tái sinh mỏng manh, bề mặt xỉn màu, giòn dễ nứt vỡ khi chịu nhiệt.</li>
                                <li><strong>Rủi ro:</strong> Không được bảo hành, tiềm ẩn nguy cơ chập cháy điện cực kỳ nguy hiểm.</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Hệ Thống Tổng Kho Phân Phối & Điểm Mua Hàng -->
                <div class="contact-purchase-block" style="border-top: 1.5px dashed var(--slate-200); padding-top: 2rem; margin-top: 2rem;">
                    <h3 style="color: #004098; font-size: 1.15rem; font-weight: 800; margin-bottom: 0.5rem; display: flex; align-items: center; gap: 0.5rem;">
                        <?= icon('map-pin') ?> Tổng Kho Phân Phối & Điểm Mua Hàng Chính Hãng
                    </h3>
                    <p style="color: var(--slate-600); margin-bottom: 1.25rem; font-size: 0.925rem;">Quý khách hàng, đơn vị thi công và nhà thầu có nhu cầu lấy báo giá số lượng lớn cho dự án, vui lòng liên hệ:</p>
                    <div class="contact-cards-grid">
                        <div class="warehouse-card">
                            <h4>TỔNG KHO PHÂN PHỐI & DỰ ÁN</h4>
                            <p><strong>Địa chỉ:</strong> <?= e(config('address')) ?></p>
                            <p><strong>Hotline:</strong> <?= e(config('hotline')) ?> (Zalo hỗ trợ 24/7)</p>
                            <p><strong>Email dự án:</strong> <?= e(config('email')) ?></p>
                            <p><strong>Thời gian:</strong> <?= e(config('working_hours')) ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- SẢN PHẨM CÙNG HỆ SINH THÁI -->
    <?php if (!empty($related)): ?>
        <section class="section pt-4">
            <div class="section__head">
                <h2 class="section__title">Sản Phẩm Cùng Hệ Sinh Thái</h2>
                <a href="<?= url('/danh-muc/' . $product['category_slug']) ?>" class="section__link">Xem tất cả <?= e($product['category_name']) ?> &rsaquo;</a>
            </div>
            <div class="product-grid product-grid--6cols">
                <?php foreach ($related as $p): ?>
                    <?php require VIEW_PATH . '/partials/product_card.php'; ?>
                <?php endforeach; ?>
            </div>
        </section>
    <?php endif; ?>
</div>

<!-- Mobile Sticky Bottom Purchase Bar (Chuẩn sàn TMĐT Shopee / Thế Giới Di Động) -->
<div class="pdetail-sticky-bar hide-desktop" id="pdetailStickyBar">
    <a href="https://zalo.me/<?= e(config('zalo', '0798966958')) ?>" target="_blank" rel="noopener" class="sticky-bar__btn-icon sticky-bar__btn-icon--zalo" title="Chat Zalo nhận báo giá">
        <span class="sticky-ico-wrap">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.03 2 11c0 2.87 1.5 5.43 3.84 7.03L5 22l4.13-1.37c.92.25 1.89.37 2.87.37 5.52 0 10-4.03 10-9s-4.48-9-10-9z"/></svg>
        </span>
        <span class="sticky-txt">Zalo</span>
    </a>
    <a href="tel:<?= e(preg_replace('/\D/', '', config('hotline', '0798966958'))) ?>" class="sticky-bar__btn-icon sticky-bar__btn-icon--call" title="Gọi tư vấn kỹ sư">
        <span class="sticky-ico-wrap">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3.1 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.1 4.2 2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7c.1.9.4 1.8.7 2.7a2 2 0 0 1-.5 2.1L8 9.8a16 16 0 0 0 6 6l1.3-1.3a2 2 0 0 1 2.1-.4c.9.3 1.8.6 2.7.7a2 2 0 0 1 1.7 2z"/></svg>
        </span>
        <span class="sticky-txt">Gọi ngay</span>
    </a>
    <button type="button" class="sticky-bar__btn sticky-bar__btn--cart" data-add-cart="<?= (int)$product['id'] ?>" data-qty-source="#detailProductQty">
        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg>
        <span>Thêm giỏ</span>
    </button>
    <button type="button" class="sticky-bar__btn sticky-bar__btn--buy" onclick="buyNow(<?= (int)$product['id'] ?>)">
        <span>MUA NGAY</span>
    </button>
</div>

<script>
function switchDetailThumb(btn, imgUrl) {
    document.querySelectorAll('.pdetail-thumb-btn').forEach(b => b.classList.remove('is-active'));
    btn.classList.add('is-active');
    const mainImg = document.getElementById('mainDetailImg');
    if (mainImg) mainImg.src = imgUrl;
}

function adjustQty(delta) {
    const input = document.getElementById('detailProductQty');
    if (!input) return;
    let val = parseInt(input.value) || 1;
    val += delta;
    if (val < 1) val = 1;
    input.value = val;
}

function buyNow(productId) {
    const qtyInput = document.getElementById('detailProductQty');
    const qty = qtyInput ? (parseInt(qtyInput.value) || 1) : 1;
    
    // Call cart API and redirect to checkout
    fetch(window.APP.routes.cartAdd, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-CSRF-Token': window.APP.csrf
        },
        body: new URLSearchParams({ id: productId, qty: qty })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            window.location.href = window.APP.routes.cart;
        } else {
            alert(data.message || 'Không thể thêm sản phẩm vào giỏ hàng.');
        }
    })
    .catch(() => {
        window.location.href = window.APP.routes.cart;
    });
}

function switchDetailTab(tabId, btn) {
    document.querySelectorAll('.pbody-tab-btn').forEach(b => b.classList.remove('is-active'));
    document.querySelectorAll('.pbody-pane').forEach(p => p.classList.remove('is-active'));
    btn.classList.add('is-active');
    const target = document.getElementById(tabId);
    if (target) target.classList.add('is-active');
}

function copyProductLink() {
    navigator.clipboard.writeText(window.location.href).then(() => {
        alert('Đã sao chép liên kết sản phẩm vào bộ nhớ tạm!');
    });
}

function toggleReviewForm() {
    const wrap = document.getElementById('reviewFormWrap');
    if (!wrap) return;
    const isHidden = wrap.style.display === 'none' || wrap.style.display === '';
    wrap.style.display = isHidden ? 'block' : 'none';
    if (isHidden) {
        wrap.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        const nameInput = document.getElementById('reviewAuthor');
        if (nameInput) nameInput.focus();
    }
}

const starDescriptions = {
    1: 'Rất tệ - Không hài lòng',
    2: 'Tạm được - Cần cải thiện',
    3: 'Bình thường - Đạt yêu cầu',
    4: 'Hài lòng - Chất lượng tốt',
    5: 'Tuyệt vời - Rất hài lòng'
};

function selectStar(score) {
    document.getElementById('selectedRatingInput').value = score;
    const stars = document.querySelectorAll('#starPicker .picker-star');
    stars.forEach((star, idx) => {
        if (idx < score) {
            star.classList.add('is-active');
        } else {
            star.classList.remove('is-active');
        }
    });
    const hint = document.getElementById('ratingHintText');
    if (hint) {
        hint.textContent = starDescriptions[score] || (score + ' sao');
    }
}

function submitReview(e, productId) {
    e.preventDefault();
    const btn = document.getElementById('btnSubmitReview');
    const msg = document.getElementById('reviewFormMessage');
    const form = document.getElementById('ajaxReviewForm');
    
    const authorName = document.getElementById('reviewAuthor').value.trim();
    const authorPhone = document.getElementById('reviewPhone').value.trim();
    const rating = document.getElementById('selectedRatingInput').value;
    const comment = document.getElementById('reviewComment').value.trim();
    
    if (!authorName || !comment) {
        msg.className = 'review-form-msg is-error';
        msg.textContent = 'Vui lòng nhập họ tên và nội dung đánh giá.';
        return;
    }
    
    btn.disabled = true;
    btn.textContent = 'Đang lưu đánh giá...';
    msg.textContent = '';
    
    const formData = new URLSearchParams();
    formData.append('product_id', productId);
    formData.append('author_name', authorName);
    formData.append('author_phone', authorPhone);
    formData.append('rating', rating);
    formData.append('comment', comment);
    
    fetch('<?= url('/api/danh-gia') ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-CSRF-Token': window.APP ? window.APP.csrf : ''
        },
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        btn.disabled = false;
        btn.textContent = 'Gửi Đánh Giá Ngay';
        if (data.success) {
            msg.className = 'review-form-msg is-success';
            msg.textContent = 'Cảm ơn bạn! Đánh giá đã được lưu vào hệ thống thành công.';
            form.reset();
            selectStar(5);
            
            const noNotice = document.getElementById('noReviewsNotice');
            if (noNotice) noNotice.remove();
            
            let listWrap = document.getElementById('reviewsItemsList');
            if (!listWrap) {
                listWrap = document.createElement('div');
                listWrap.className = 'reviews-items';
                listWrap.id = 'reviewsItemsList';
                document.getElementById('reviewsListContainer').appendChild(listWrap);
            }
            
            const initials = authorName.charAt(0).toUpperCase();
            const starsHtml = '★'.repeat(rating) + '☆'.repeat(5 - rating);
            
            const newCard = document.createElement('div');
            newCard.className = 'review-item review-item--new';
            newCard.innerHTML = `
                <div class="review-item-header">
                    <div class="review-avatar">${initials}</div>
                    <div class="review-user-info">
                        <div class="user-name-row">
                            <strong>${escapeHtml(authorName)}</strong>
                            <span class="badge-verified">✔ Đã mua hàng tại Tổng Kho</span>
                        </div>
                        <div class="review-meta-row">
                            <div class="review-stars-sm">${starsHtml}</div>
                            <span class="review-date">Vừa xong</span>
                        </div>
                    </div>
                </div>
                <div class="review-body">
                    <p>${escapeHtml(comment).replace(/\\n/g, '<br>')}</p>
                </div>
            `;
            listWrap.insertBefore(newCard, listWrap.firstChild);
            
            const badge = document.getElementById('reviewsCountBadge');
            if (badge) {
                const cur = parseInt(badge.textContent) || 0;
                badge.textContent = cur + 1;
            }
            const countElem = document.getElementById('mainReviewsCount');
            if (countElem) {
                const cur = parseInt(countElem.textContent) || 0;
                countElem.textContent = cur + 1;
            }
            
            setTimeout(() => {
                document.getElementById('reviewFormWrap').style.display = 'none';
                msg.textContent = '';
            }, 3000);
        } else {
            msg.className = 'review-form-msg is-error';
            msg.textContent = data.message || 'Không thể gửi đánh giá, vui lòng thử lại.';
        }
    })
    .catch(() => {
        btn.disabled = false;
        btn.textContent = 'Gửi Đánh Giá Ngay';
        msg.className = 'review-form-msg is-error';
        msg.textContent = 'Đã có lỗi xảy ra trong quá trình gửi. Vui lòng kiểm tra lại.';
    });
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}
</script>
