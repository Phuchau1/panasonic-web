<?php
/** @var array $p Sản phẩm đã decode */
$cardBrand = 'Panasonic';
if ((!empty($p['category_slug']) && $p['category_slug'] === 'day-cap-dien-cadivi') || stripos($p['name'] ?? '', 'CADIVI') !== false) {
    $cardBrand = 'CADIVI';
} elseif ((!empty($p['category_slug']) && $p['category_slug'] === 'o-cam-am-san-cong-nghiep') || stripos($p['name'] ?? '', 'PCE') !== false) {
    $cardBrand = 'PCE';
} elseif (stripos($p['name'] ?? '', 'Nanoco') !== false) {
    $cardBrand = 'Nanoco';
}
$brandModifier = strtolower($cardBrand);
?>
<article class="product-card">
    <div class="product-card__media">
        <div class="product-card__badges">
            <span class="badge-brand badge-brand--<?= $brandModifier ?>"><?= $cardBrand ?></span>
            <?php if ($p['discount_percent'] > 0): ?>
                <span class="badge-discount">-<?= (int) $p['discount_percent'] ?>%</span>
            <?php endif; ?>
        </div>
        <a href="<?= url('/san-pham/' . $p['slug']) ?>" class="product-card__img" tabindex="-1" aria-hidden="true">
            <img src="<?= media($p['thumbnail']) ?>" alt="<?= e($p['name']) ?>" loading="lazy" width="220" height="220" onerror="this.onerror=null;this.classList.add('img-fallback');">
        </a>
    </div>

    <div class="product-card__body">
        <div class="product-card__meta">
            <?php if (!empty($p['model_code'])): ?>
                <span class="model-code">Mã: <?= e($p['model_code']) ?></span>
            <?php endif; ?>
            <?php if (!empty($p['series_name'])): ?>
                <span class="product-card__series"><?= e($p['series_name']) ?></span>
            <?php endif; ?>
        </div>

        <h3 class="product-card__title">
            <a href="<?= url('/san-pham/' . $p['slug']) ?>" title="<?= e($p['name']) ?>"><?= e($p['name']) ?></a>
        </h3>

        <div class="product-card__rating" title="Đánh giá 5/5 sao">
            <span class="stars">★★★★★</span>
            <span class="rating-text">(5.0)</span>
        </div>

        <div class="product-card__price">
            <span class="price-val"><?= money($p['price']) ?></span>
            <?php if ($p['wholesale_price'] > 0): ?>
                <div class="price-wholesale" title="Chiết khấu công trình từ <?= (int) $p['wholesale_min_qty'] ?> <?= e($p['unit']) ?>">
                    Giá sỉ: <strong><?= money($p['wholesale_price']) ?></strong>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="product-card__actions">
        <button type="button" class="btn-buy" data-add-cart="<?= (int) $p['id'] ?>" data-qty="1" <?= $p['stock'] > 0 ? '' : 'disabled' ?>>
            <?= icon('cart') ?> <?= $p['stock'] > 0 ? 'Thêm vào giỏ' : 'Hết hàng' ?>
        </button>
    </div>
</article>
