<?php
/** @var string $heading */
/** @var array|null $category */
/** @var array|null $series */
/** @var array $filters */
/** @var array $fixed */
/** @var array $result */
/** @var array $categories */
/** @var array $seriesList */
/** @var array $facets */

$items = $result['items'];
$page  = $result['page'];
$pages = $result['pages'];
$total = $result['total'];
?>

<div class="page-header">
    <div class="container">
        <nav class="breadcrumb" aria-label="Đường dẫn trang">
            <a href="<?= url('/') ?>"><?= icon('home') ?> Trang chủ</a>
            <span class="breadcrumb__sep"><?= icon('chevron-right') ?></span>
            <a href="<?= url('/san-pham') ?>">Sản phẩm</a>
            <?php if ($category && $series): ?>
                <span class="breadcrumb__sep"><?= icon('chevron-right') ?></span>
                <a href="<?= url('/danh-muc/' . $category['slug']) ?>"><?= e($category['name']) ?></a>
                <span class="breadcrumb__sep"><?= icon('chevron-right') ?></span>
                <span class="breadcrumb__current">Dòng <?= e($series['name']) ?></span>
            <?php elseif ($category): ?>
                <span class="breadcrumb__sep"><?= icon('chevron-right') ?></span>
                <span class="breadcrumb__current"><?= e($category['name']) ?></span>
            <?php elseif ($series): ?>
                <span class="breadcrumb__sep"><?= icon('chevron-right') ?></span>
                <span class="breadcrumb__current">Dòng <?= e($series['name']) ?></span>
            <?php endif; ?>
        </nav>
        <div class="page-header__content">
            <h1 class="page-title"><?= e($heading) ?></h1>
            <p class="page-desc"><?= e($metaDescription ?? 'Danh sách thiết bị điện Panasonic chính hãng bảo hành 24 tháng.') ?></p>
        </div>
    </div>
</div>

<div class="container section">
    <div class="catalog-layout">
        <!-- Sidebar bộ lọc -->
        <aside class="catalog-sidebar" id="filterDrawer">
            <div class="sidebar-header hide-desktop">
                <h3>Bộ lọc kỹ thuật</h3>
                <button type="button" class="btn-close" data-filter-close><?= icon('x') ?></button>
            </div>
            
            <form action="<?= url(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ? substr(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), strlen(base_path())) : '/san-pham') ?>" method="get" class="filter-form" data-filter-form>
                <?php if (!empty($fixed['category'])): ?>
                    <input type="hidden" name="category" value="<?= e($fixed['category']) ?>">
                <?php endif; ?>
                <?php if (!empty($fixed['series'])): ?>
                    <input type="hidden" name="series" value="<?= e($fixed['series']) ?>">
                <?php endif; ?>
                <?php if (!empty($filters['q'])): ?>
                    <input type="hidden" name="q" value="<?= e($filters['q']) ?>">
                <?php endif; ?>

                <!-- Lọc Danh mục nếu không cố định -->
                <?php if (empty($fixed['category'])): ?>
                <div class="filter-group">
                    <h4 class="filter-group__title"><?= icon('grid') ?> Chủng loại thiết bị</h4>
                    <div class="filter-group__body">
                        <?php foreach ($categories as $c): ?>
                            <label class="filter-checkbox">
                                <input type="radio" name="category" value="<?= e($c['slug']) ?>" <?= ($filters['category'] === $c['slug']) ? 'checked' : '' ?> onchange="this.form.submit()">
                                <span><?= e($c['name']) ?></span>
                                <small>(<?= (int)$c['product_count'] ?>)</small>
                            </label>
                        <?php endforeach; ?>
                        <?php if (!empty($filters['category'])): ?>
                            <a href="<?= url('/san-pham') . query_with(['category' => null]) ?>" class="filter-clear">Xóa chọn</a>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Lọc Series nếu không cố định -->
                <?php if (empty($fixed['series'])): ?>
                <div class="filter-group">
                    <h4 class="filter-group__title"><?= icon('switch') ?> Dòng Series</h4>
                    <div class="filter-group__body">
                        <?php foreach ($seriesList as $s): ?>
                            <label class="filter-checkbox">
                                <input type="radio" name="series" value="<?= e($s['slug']) ?>" <?= ($filters['series'] === $s['slug']) ? 'checked' : '' ?> onchange="this.form.submit()">
                                <span><?= e($s['name']) ?></span>
                                <small>(<?= (int)$s['product_count'] ?>)</small>
                            </label>
                        <?php endforeach; ?>
                        <?php if (!empty($filters['series'])): ?>
                            <a href="<?= url('/san-pham') . query_with(['series' => null]) ?>" class="filter-clear">Xóa chọn</a>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Dòng cắt kA (Cầu dao) -->
                <?php if (!empty($facets['ka'])): ?>
                <div class="filter-group">
                    <h4 class="filter-group__title"><?= icon('breaker') ?> Dòng cắt ngắn mạch (Icu/Icn)</h4>
                    <div class="filter-group__body">
                        <?php foreach ($facets['ka'] as $ka): ?>
                            <label class="filter-checkbox">
                                <input type="checkbox" name="ka[]" value="<?= e($ka['value']) ?>" <?= in_array((string)$ka['value'], (array)$filters['ka'], true) ? 'checked' : '' ?> onchange="this.form.submit()">
                                <span><?= (float)$ka['value'] ?> kA</span>
                                <small>(<?= (int)$ka['total'] ?>)</small>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Dòng định mức Ampe -->
                <?php if (!empty($facets['ampe'])): ?>
                <div class="filter-group">
                    <h4 class="filter-group__title"><?= icon('zap') ?> Dòng định mức (Ampe)</h4>
                    <div class="filter-group__body filter-group__body--scroll">
                        <?php foreach ($facets['ampe'] as $amp): ?>
                            <label class="filter-checkbox">
                                <input type="checkbox" name="ampe[]" value="<?= e($amp['value']) ?>" <?= in_array((string)$amp['value'], (array)$filters['ampe'], true) ? 'checked' : '' ?> onchange="this.form.submit()">
                                <span><?= (float)$amp['value'] ?> A</span>
                                <small>(<?= (int)$amp['total'] ?>)</small>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Số cực Poles -->
                <?php if (!empty($facets['poles'])): ?>
                <div class="filter-group">
                    <h4 class="filter-group__title"><?= icon('layers') ?> Số cực (Poles)</h4>
                    <div class="filter-group__body">
                        <?php foreach ($facets['poles'] as $p): ?>
                            <label class="filter-checkbox">
                                <input type="checkbox" name="poles[]" value="<?= e($p['value']) ?>" <?= in_array((string)$p['value'], (array)$filters['poles'], true) ? 'checked' : '' ?> onchange="this.form.submit()">
                                <span><?= (int)$p['value'] ?>P</span>
                                <small>(<?= (int)$p['total'] ?>)</small>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Lọc khoảng giá -->
                <div class="filter-group">
                    <h4 class="filter-group__title"><?= icon('tag') ?> Khoảng giá (VNĐ)</h4>
                    <div class="filter-price">
                        <input type="number" name="price_min" placeholder="Từ..." value="<?= e($filters['price_min'] ?? '') ?>" step="10000" class="field__input">
                        <span>-</span>
                        <input type="number" name="price_max" placeholder="Đến..." value="<?= e($filters['price_max'] ?? '') ?>" step="10000" class="field__input">
                    </div>
                    <button type="submit" class="btn btn--outline btn--sm btn--block mt-2">Áp dụng</button>
                </div>

                <div class="filter-actions mt-3">
                    <a href="<?= url('/san-pham') ?>" class="btn btn--ghost btn--sm btn--block">Đặt lại bộ lọc</a>
                </div>
            </form>
        </aside>

        <!-- Main Product List -->
        <main class="catalog-main">
            <?php
            $hasActiveFilters = !empty($filters['category']) || !empty($filters['series']) || !empty($filters['ampe']) || !empty($filters['ka']) || !empty($filters['poles']) || !empty($filters['price_min']) || !empty($filters['price_max']) || !empty($filters['q']);
            ?>

            <?php if ($hasActiveFilters): ?>
                <div class="active-filters">
                    <span class="active-filters__label"><?= icon('filter') ?> Đang lọc:</span>

                    <?php if (!empty($filters['q'])): ?>
                        <a href="<?= url('/san-pham') . query_with(['q' => null, 'page' => null]) ?>" class="filter-tag" title="Xóa tìm kiếm">
                            Từ khóa: "<?= e($filters['q']) ?>" <span class="tag-remove">×</span>
                        </a>
                    <?php endif; ?>

                    <?php if ($category && empty($fixed['category'])): ?>
                        <a href="<?= url('/san-pham') . query_with(['category' => null, 'page' => null]) ?>" class="filter-tag" title="Bỏ chọn danh mục">
                            <?= e($category['name']) ?> <span class="tag-remove">×</span>
                        </a>
                    <?php endif; ?>

                    <?php if ($series && empty($fixed['series'])): ?>
                        <a href="<?= url('/san-pham') . query_with(['series' => null, 'page' => null]) ?>" class="filter-tag" title="Bỏ chọn dòng sản phẩm">
                            Dòng <?= e($series['name']) ?> <span class="tag-remove">×</span>
                        </a>
                    <?php endif; ?>

                    <?php if (!empty($filters['price_min']) || !empty($filters['price_max'])): ?>
                        <a href="<?= url('/san-pham') . query_with(['price_min' => null, 'price_max' => null, 'page' => null]) ?>" class="filter-tag" title="Xóa lọc giá">
                            Giá: <?= !empty($filters['price_min']) ? number_format((int)$filters['price_min'], 0, ',', '.') . 'đ' : '0' ?> - <?= !empty($filters['price_max']) ? number_format((int)$filters['price_max'], 0, ',', '.') . 'đ' : 'Tối đa' ?> <span class="tag-remove">×</span>
                        </a>
                    <?php endif; ?>

                    <?php if (!empty($filters['ampe'])): ?>
                        <?php foreach ((array)$filters['ampe'] as $a): ?>
                            <a href="<?= url('/san-pham') . query_with(['ampe' => array_values(array_diff((array)$filters['ampe'], [$a])), 'page' => null]) ?>" class="filter-tag">
                                <?= $a ?>A <span class="tag-remove">×</span>
                            </a>
                        <?php endforeach; ?>
                    <?php endif; ?>

                    <?php if (!empty($filters['ka'])): ?>
                        <?php foreach ((array)$filters['ka'] as $k): ?>
                            <a href="<?= url('/san-pham') . query_with(['ka' => array_values(array_diff((array)$filters['ka'], [$k])), 'page' => null]) ?>" class="filter-tag">
                                <?= $k ?>kA <span class="tag-remove">×</span>
                            </a>
                        <?php endforeach; ?>
                    <?php endif; ?>

                    <?php if (!empty($filters['poles'])): ?>
                        <?php foreach ((array)$filters['poles'] as $p): ?>
                            <a href="<?= url('/san-pham') . query_with(['poles' => array_values(array_diff((array)$filters['poles'], [$p])), 'page' => null]) ?>" class="filter-tag">
                                <?= $p ?>P <span class="tag-remove">×</span>
                            </a>
                        <?php endforeach; ?>
                    <?php endif; ?>

                    <a href="<?= url('/san-pham') ?>" class="filter-tag-clear">Xóa tất cả bộ lọc ✕</a>
                </div>
            <?php endif; ?>

            <!-- Toolbar bar -->
            <div class="catalog-toolbar">
                <div class="catalog-toolbar__info">
                    <button type="button" class="btn btn--outline btn--sm hide-desktop" data-filter-toggle>
                        <?= icon('filter') ?> Bộ lọc
                    </button>
                    <span>Hiển thị <strong><?= count($items) ?></strong> trên tổng <strong><?= $total ?></strong> sản phẩm<?= $pages > 1 ? " (Trang {$page}/{$pages})" : "" ?></span>
                </div>
                <div class="catalog-toolbar__controls">
                    <div class="catalog-toolbar__limit">
                        <span class="limit-label">Xem:</span>
                        <a href="<?= url('/san-pham') . query_with(['limit' => 12, 'page' => null]) ?>" class="limit-btn <?= empty($filters['limit']) || $filters['limit'] == '12' ? 'is-active' : '' ?>">12</a>
                        <a href="<?= url('/san-pham') . query_with(['limit' => 24, 'page' => null]) ?>" class="limit-btn <?= ($filters['limit'] ?? '') == '24' ? 'is-active' : '' ?>">24</a>
                        <a href="<?= url('/san-pham') . query_with(['limit' => 48, 'page' => null]) ?>" class="limit-btn <?= ($filters['limit'] ?? '') == '48' ? 'is-active' : '' ?>">48</a>
                        <a href="<?= url('/san-pham') . query_with(['limit' => 'all', 'page' => null]) ?>" class="limit-btn <?= ($filters['limit'] ?? '') == 'all' ? 'is-active' : '' ?>">Tất cả</a>
                    </div>
                    <div class="catalog-toolbar__sort">
                        <label for="sortSelect">Sắp xếp:</label>
                        <select id="sortSelect" class="field__input field__input--sm" onchange="location = this.value">
                            <option value="<?= url('/san-pham') . query_with(['sort' => 'newest']) ?>" <?= $filters['sort'] === 'newest' ? 'selected' : '' ?>>Mới nhất</option>
                            <option value="<?= url('/san-pham') . query_with(['sort' => 'popular']) ?>" <?= $filters['sort'] === 'popular' ? 'selected' : '' ?>>Bán chạy / Nổi bật</option>
                            <option value="<?= url('/san-pham') . query_with(['sort' => 'price_asc']) ?>" <?= $filters['sort'] === 'price_asc' ? 'selected' : '' ?>>Giá tăng dần</option>
                            <option value="<?= url('/san-pham') . query_with(['sort' => 'price_desc']) ?>" <?= $filters['sort'] === 'price_desc' ? 'selected' : '' ?>>Giá giảm dần</option>
                            <option value="<?= url('/san-pham') . query_with(['sort' => 'name']) ?>" <?= $filters['sort'] === 'name' ? 'selected' : '' ?>>Tên A - Z</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- List Grid -->
            <?php if (!empty($items)): ?>
                <div class="product-grid">
                    <?php foreach ($items as $p): ?>
                        <?php require VIEW_PATH . '/partials/product_card.php'; ?>
                    <?php endforeach; ?>
                </div>

                <!-- Phân trang -->
                <?php if ($pages > 1): ?>
                    <nav class="pagination" aria-label="Phân trang sản phẩm">
                        <?php if ($page > 1): ?>
                            <a href="<?= url('/san-pham') . query_with(['page' => $page - 1]) ?>" class="pagination__btn" aria-label="Trang trước"><?= icon('chevron-left') ?></a>
                        <?php endif; ?>

                        <?php for ($i = 1; $i <= $pages; $i++): ?>
                            <?php if ($i === $page): ?>
                                <span class="pagination__btn is-active"><?= $i ?></span>
                            <?php elseif ($i == 1 || $i == $pages || ($i >= $page - 2 && $i <= $page + 2)): ?>
                                <a href="<?= url('/san-pham') . query_with(['page' => $i]) ?>" class="pagination__btn"><?= $i ?></a>
                            <?php elseif ($i == $page - 3 || $i == $page + 3): ?>
                                <span class="pagination__dots">...</span>
                            <?php endif; ?>
                        <?php endfor; ?>

                        <?php if ($page < $pages): ?>
                            <a href="<?= url('/san-pham') . query_with(['page' => $page + 1]) ?>" class="pagination__btn" aria-label="Trang tiếp theo"><?= icon('chevron-right') ?></a>
                        <?php endif; ?>
                    </nav>
                <?php endif; ?>

            <?php else: ?>
                <div class="empty-state">
                    <span class="empty-state__icon"><?= icon('search') ?></span>
                    <h3>Không tìm thấy sản phẩm phù hợp</h3>
                    <p>Hãy thử tìm bằng từ khoá ngắn hơn (như WEV, BBD, Moderva) hoặc xóa bớt tiêu chí lọc kỹ thuật.</p>
                    <a href="<?= url('/san-pham') ?>" class="btn btn--primary">Xoá toàn bộ lọc</a>
                </div>
            <?php endif; ?>
        </main>
    </div>
</div>
