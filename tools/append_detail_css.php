<?php
$mainCss = file_get_contents(__DIR__ . '/../public/css/main.css');

$detailCss = <<<CSS


/* -------------------------------------------------------------
   MODERN PRODUCT DETAIL LAYOUT (MATCHING REFERENCE SPEC)
   ------------------------------------------------------------- */
.product-detail-layout {
    display: grid;
    grid-template-columns: 380px 1fr 280px;
    gap: 1.75rem;
    align-items: flex-start;
    margin-bottom: 2.5rem;
}

/* Media Gallery */
.pdetail-media {
    background: #ffffff;
    border: 1px solid var(--slate-200);
    border-radius: var(--radius-lg);
    padding: 1.25rem;
}

.pdetail-gallery__main {
    position: relative;
    width: 100%;
    aspect-ratio: 1 / 1;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #ffffff;
    border-radius: var(--radius-md);
    overflow: hidden;
    margin-bottom: 0.875rem;
}

.pdetail-gallery__main img {
    max-width: 100%;
    max-height: 100%;
    object-fit: contain;
}

.pdetail-badge-genuine {
    position: absolute;
    top: 10px;
    left: 10px;
    background: #15803d;
    color: #ffffff;
    font-size: 11px;
    font-weight: 700;
    padding: 3px 8px;
    border-radius: 4px;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}

.pdetail-badge-discount {
    position: absolute;
    top: 10px;
    right: 10px;
    background: #dc2626;
    color: #ffffff;
    font-size: 12px;
    font-weight: 800;
    padding: 3px 8px;
    border-radius: 4px;
}

.pdetail-gallery__thumbs {
    display: flex;
    gap: 0.5rem;
    overflow-x: auto;
    padding-bottom: 4px;
}

.pdetail-thumb-btn {
    width: 62px;
    height: 62px;
    flex-shrink: 0;
    border: 1.5px solid var(--slate-200);
    border-radius: 6px;
    background: #ffffff;
    padding: 3px;
    cursor: pointer;
    transition: all 0.2s ease;
}

.pdetail-thumb-btn:hover,
.pdetail-thumb-btn.is-active {
    border-color: #004098;
    box-shadow: 0 0 0 2px rgba(0, 64, 152, 0.15);
}

.pdetail-thumb-btn img {
    width: 100%;
    height: 100%;
    object-fit: contain;
}

.pdetail-share-box {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-top: 1rem;
    padding-top: 0.875rem;
    border-top: 1px solid var(--slate-100);
    font-size: 0.8rem;
    color: var(--slate-500);
}

.share-btn {
    padding: 3px 8px;
    border-radius: 4px;
    font-size: 11.5px;
    font-weight: 600;
    text-decoration: none;
    border: 1px solid var(--slate-200);
    color: var(--slate-700);
    cursor: pointer;
    background: #f8fafc;
}
.share-btn--fb { color: #1877f2; border-color: #e2e8f0; }
.share-btn--zalo { color: #0068ff; border-color: #e2e8f0; }

/* Center Details */
.pdetail-center {
    background: #ffffff;
    border: 1px solid var(--slate-200);
    border-radius: var(--radius-lg);
    padding: 1.5rem;
}

.pdetail-meta-tags {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
    margin-bottom: 0.5rem;
}

.meta-tag {
    font-size: 11.5px;
    padding: 2px 8px;
    border-radius: 4px;
    font-weight: 600;
}
.meta-tag--model { background: #eff6ff; color: #1d4ed8; }
.meta-tag--sku { background: #f1f5f9; color: #475569; }
.meta-tag--series { background: #fef3c7; color: #b45309; }

.pdetail-title {
    font-size: 1.35rem;
    font-weight: 800;
    color: var(--slate-900);
    line-height: 1.35;
    margin: 0 0 0.5rem;
}

.pdetail-rating-row {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.825rem;
    margin-bottom: 1rem;
    flex-wrap: wrap;
}

.pdetail-stars { color: #f59e0b; font-size: 13px; }
.pdetail-rating-score { font-weight: 700; color: var(--slate-900); }
.pdetail-rating-count { color: var(--slate-500); }

.pdetail-status-pill {
    margin-left: auto;
    font-size: 11.5px;
    font-weight: 600;
    padding: 2px 8px;
    border-radius: 99px;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}
.pdetail-status-pill.in-stock { background: #dcfce7; color: #166534; }
.pdetail-status-pill .dot { width: 6px; height: 6px; border-radius: 50%; background: currentColor; }

/* Price Box */
.pdetail-price-box {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: var(--radius-md);
    padding: 1rem;
    margin-bottom: 1.25rem;
}

.pdetail-price-row {
    display: flex;
    align-items: baseline;
    gap: 0.75rem;
    flex-wrap: wrap;
}

.pdetail-price-current {
    font-size: 1.65rem;
    font-weight: 800;
    color: #dc2626;
    letter-spacing: -0.5px;
}

.pdetail-price-original {
    font-size: 1rem;
    color: #94a3b8;
    text-decoration: line-through;
}

.pdetail-price-vat {
    font-size: 0.8rem;
    color: #64748b;
}

.pdetail-wholesale-banner {
    margin-top: 0.65rem;
    padding-top: 0.65rem;
    border-top: 1px dashed #cbd5e1;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.825rem;
    flex-wrap: wrap;
}

.wholesale-tag {
    background: #0284c7;
    color: #ffffff;
    font-size: 10px;
    font-weight: 800;
    padding: 2px 6px;
    border-radius: 3px;
}

.wholesale-val {
    color: #0369a1;
    font-size: 1rem;
}

.wholesale-min {
    color: #64748b;
    font-size: 11.5px;
}

/* Checklist */
.pdetail-checklist {
    margin-bottom: 1.25rem;
}

.checklist-items {
    list-style: none;
    padding: 0;
    margin: 0;
    display: flex;
    flex-direction: column;
    gap: 0.35rem;
}

.checklist-items li {
    font-size: 0.85rem;
    color: #334155;
    display: flex;
    align-items: flex-start;
    gap: 0.45rem;
    line-height: 1.45;
}

.check-icon {
    color: #16a34a;
    font-weight: 800;
    flex-shrink: 0;
}

/* Purchase Controls */
.pdetail-purchase-area {
    padding-top: 1rem;
    border-top: 1px solid var(--slate-200);
}

.pdetail-qty-row {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-bottom: 1rem;
}

.qty-label {
    font-size: 0.85rem;
    font-weight: 600;
    color: var(--slate-700);
}

.qty-stepper {
    display: inline-flex;
    align-items: center;
    border: 1px solid var(--slate-300);
    border-radius: var(--radius-sm);
    background: #ffffff;
    overflow: hidden;
}

.stepper-btn {
    width: 32px;
    height: 32px;
    background: #f1f5f9;
    border: none;
    font-size: 1rem;
    font-weight: 700;
    color: var(--slate-700);
    cursor: pointer;
}
.stepper-btn:hover { background: #e2e8f0; }

.qty-stepper input {
    width: 48px;
    height: 32px;
    text-align: center;
    border: none;
    font-weight: 700;
    font-size: 0.9rem;
}

.stock-note {
    font-size: 0.8rem;
    color: #16a34a;
    font-weight: 500;
}

/* Dual CTA Buttons */
.pdetail-cta-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 0.75rem;
    margin-bottom: 0.75rem;
}

.btn-cta {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 0.65rem 0.5rem;
    border-radius: var(--radius-md);
    text-decoration: none;
    border: none;
    cursor: pointer;
    transition: all 0.2s ease;
    text-align: center;
}

.btn-cta .cta-title {
    font-size: 0.95rem;
    font-weight: 800;
    letter-spacing: 0.3px;
    line-height: 1.2;
}

.btn-cta .cta-subtitle {
    font-size: 10.5px;
    font-weight: 400;
    opacity: 0.9;
    margin-top: 2px;
}

.btn-cta--buynow {
    background: #dc2626;
    color: #ffffff;
    box-shadow: 0 4px 10px rgba(220, 38, 38, 0.25);
}
.btn-cta--buynow:hover { background: #b91c1c; }

.btn-cta--quote {
    background: #16a34a;
    color: #ffffff;
    box-shadow: 0 4px 10px rgba(22, 163, 74, 0.25);
}
.btn-cta--quote:hover { background: #15803d; }

.pdetail-cart-row {
    display: flex;
    gap: 0.5rem;
}

.btn-add-cart-secondary {
    flex: 1;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    padding: 0.5rem;
    font-size: 0.825rem;
    font-weight: 700;
    color: #004098;
    background: #eff6ff;
    border: 1px solid #bfdbfe;
    border-radius: var(--radius-sm);
    cursor: pointer;
}
.btn-add-cart-secondary:hover { background: #dbeafe; }

.btn-zalo-secondary {
    flex: 1;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 0.5rem;
    font-size: 0.825rem;
    font-weight: 700;
    color: #0369a1;
    background: #f0fdf4;
    border: 1px solid #bbf7d0;
    border-radius: var(--radius-sm);
    text-decoration: none;
}

/* Sidebar Right */
.pdetail-sidebar {
    display: flex;
    flex-direction: column;
    gap: 1.25rem;
}

.sidebar-hotline-card {
    background: #fff1f2;
    border: 1.5px solid #fecdd3;
    border-radius: var(--radius-lg);
    padding: 1.25rem;
}

.hotline-card__head {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-bottom: 0.5rem;
}

.hotline-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: #dc2626;
    color: #ffffff;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.hotline-label {
    font-size: 11px;
    font-weight: 700;
    color: #9f1239;
    letter-spacing: 0.3px;
}

.hotline-phone {
    font-size: 1.25rem;
    font-weight: 800;
    color: #dc2626;
    text-decoration: none;
    line-height: 1.2;
}

.hotline-card__sub {
    font-size: 0.8rem;
    color: #475569;
    border-top: 1px dashed #fecdd3;
    padding-top: 0.5rem;
    display: flex;
    flex-direction: column;
    gap: 2px;
}
.hotline-card__sub a { color: #dc2626; font-weight: 700; text-decoration: none; }

.sidebar-policy-card {
    background: #ffffff;
    border: 1px solid var(--slate-200);
    border-radius: var(--radius-lg);
    padding: 1.25rem;
}

.policy-card__title {
    font-size: 0.85rem;
    font-weight: 800;
    color: var(--slate-900);
    letter-spacing: 0.5px;
    margin: 0 0 0.875rem;
    padding-bottom: 0.5rem;
    border-bottom: 1px solid var(--slate-200);
}

.policy-list {
    list-style: none;
    padding: 0;
    margin: 0;
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.policy-list li {
    display: flex;
    align-items: flex-start;
    gap: 0.65rem;
    font-size: 0.8rem;
}

.policy-ico {
    font-size: 1.1rem;
    line-height: 1;
    flex-shrink: 0;
}

.policy-list strong {
    display: block;
    color: var(--slate-900);
    font-size: 0.825rem;
}

.policy-list p {
    margin: 2px 0 0;
    color: var(--slate-500);
    font-size: 11.5px;
    line-height: 1.35;
}

.sidebar-catalog-card {
    background: #f8fafc;
    border: 1px solid #cbd5e1;
    border-radius: var(--radius-md);
    padding: 1rem;
}

.badge-pdf {
    background: #dc2626;
    color: #ffffff;
    font-size: 10px;
    font-weight: 800;
    padding: 1px 5px;
    border-radius: 3px;
}

.sidebar-catalog-card strong {
    display: block;
    font-size: 0.85rem;
    color: var(--slate-900);
    margin-top: 4px;
}

.sidebar-catalog-card p {
    font-size: 11.5px;
    color: #64748b;
    margin: 2px 0 8px;
}

.btn-catalog-dl {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    padding: 0.4rem 0.75rem;
    background: #004098;
    color: #ffffff;
    font-size: 11.5px;
    font-weight: 700;
    border-radius: 4px;
    text-decoration: none;
}

/* Tabs Section */
.product-body-tabs {
    background: #ffffff;
    border: 1px solid var(--slate-200);
    border-radius: var(--radius-lg);
    overflow: hidden;
    margin-bottom: 2.5rem;
}

.pbody-nav {
    display: flex;
    background: #f8fafc;
    border-bottom: 1px solid var(--slate-200);
    overflow-x: auto;
}

.pbody-tab-btn {
    padding: 0.875rem 1.25rem;
    border: none;
    background: transparent;
    font-size: 0.875rem;
    font-weight: 700;
    color: var(--slate-600);
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    white-space: nowrap;
    border-bottom: 2px solid transparent;
    transition: all 0.2s ease;
}

.pbody-tab-btn:hover { color: #004098; }

.pbody-tab-btn.is-active {
    color: #004098;
    background: #ffffff;
    border-bottom-color: #004098;
}

.pbody-content {
    padding: 2rem;
}

.pbody-pane {
    display: none;
}
.pbody-pane.is-active {
    display: block;
}

/* Rich Content */
.rich-content-article {
    font-size: 0.925rem;
    color: var(--slate-700);
    line-height: 1.7;
}

.rich-content-article h1,
.rich-content-article h2,
.rich-content-article h3 {
    color: var(--slate-900);
    margin-top: 1.5rem;
    margin-bottom: 0.75rem;
}

.rich-content-article img {
    max-width: 100%;
    height: auto;
    border-radius: var(--radius-md);
    margin: 1rem 0;
}

/* Specs Table */
.specs-modern-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.875rem;
}

.specs-modern-table th,
.specs-modern-table td {
    padding: 0.65rem 1rem;
    border: 1px solid #e2e8f0;
}

.specs-modern-table th {
    background: #f8fafc;
    color: var(--slate-700);
    width: 35%;
    font-weight: 600;
}

.specs-modern-table tr:nth-child(even) td {
    background: #fafafa;
}

/* Drawings */
.technical-drawing-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1.5rem;
    margin-top: 1rem;
}

.drawing-item {
    border: 1px solid var(--slate-200);
    border-radius: var(--radius-md);
    padding: 1rem;
    text-align: center;
    background: #ffffff;
}

.drawing-item img {
    max-width: 100%;
    max-height: 280px;
    object-fit: contain;
    margin-bottom: 0.5rem;
}

.drawing-item span {
    font-size: 0.8rem;
    font-weight: 600;
    color: var(--slate-600);
}

/* Genuine Comparison */
.genuine-comparison-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1.5rem;
    margin-top: 1.25rem;
}

.genuine-card {
    border-radius: var(--radius-md);
    padding: 1.25rem;
}

.genuine-card--real {
    background: #f0fdf4;
    border: 1.5px solid #86efac;
}

.genuine-card--real .genuine-card__title {
    color: #166534;
    font-weight: 800;
    font-size: 0.95rem;
    margin-bottom: 0.75rem;
}

.genuine-card--fake {
    background: #fef2f2;
    border: 1.5px solid #fca5a5;
}

.genuine-card--fake .genuine-card__title {
    color: #991b1b;
    font-weight: 800;
    font-size: 0.95rem;
    margin-bottom: 0.75rem;
}

.genuine-card__list {
    list-style: none;
    padding: 0;
    margin: 0;
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
    font-size: 0.825rem;
    line-height: 1.5;
}

/* Warehouse cards */
.contact-cards-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1.5rem;
    margin-top: 1rem;
}

.warehouse-card {
    background: #f8fafc;
    border: 1px solid var(--slate-200);
    border-radius: var(--radius-md);
    padding: 1.25rem;
}

.warehouse-card h4 {
    margin: 0 0 0.5rem;
    font-size: 0.95rem;
    color: #004098;
    font-weight: 800;
}

.warehouse-card p {
    margin: 0 0 0.25rem;
    font-size: 0.85rem;
    color: #475569;
}

CSS;

if (strpos($mainCss, 'MODERN PRODUCT DETAIL LAYOUT') === false) {
    file_put_contents(__DIR__ . '/../public/css/main.css', $mainCss . $detailCss);
    echo "Appended modern detail CSS to main.css\n";
}

$respCss = file_get_contents(__DIR__ . '/../public/css/responsive.css');
$respDetail = <<<CSS


/* Responsive Product Detail */
@media (max-width: 1100px) {
    .product-detail-layout {
        grid-template-columns: 340px 1fr !important;
    }
    .pdetail-sidebar {
        grid-column: span 2;
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
    }
}

@media (max-width: 768px) {
    .product-detail-layout {
        grid-template-columns: 1fr !important;
        gap: 1.25rem !important;
    }
    .pdetail-sidebar {
        grid-column: span 1;
        display: flex;
        flex-direction: column;
    }
    .pdetail-cta-row {
        grid-template-columns: 1fr !important;
    }
    .pdetail-title {
        font-size: 1.2rem !important;
    }
    .pdetail-price-current {
        font-size: 1.4rem !important;
    }
    .pbody-content {
        padding: 1.25rem !important;
    }
    .technical-drawing-grid,
    .genuine-comparison-grid,
    .contact-cards-grid {
        grid-template-columns: 1fr !important;
    }
}

CSS;

if (strpos($respCss, 'Responsive Product Detail') === false) {
    file_put_contents(__DIR__ . '/../public/css/responsive.css', $respCss . $respDetail);
    echo "Appended detail responsive rules to responsive.css\n";
}
