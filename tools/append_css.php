<?php
$mainCss = file_get_contents(__DIR__ . '/../public/css/main.css');
if (strpos($mainCss, 'CATEGORY OVERVIEW & CLASSIFIED SERIES') === false) {
    $newCss = <<<CSS


/* -------------------------------------------------------------
   CATEGORY OVERVIEW & CLASSIFIED SERIES
   ------------------------------------------------------------- */
.section-cat {
    padding-top: 1.25rem;
    padding-bottom: 3.5rem;
}

.cat-hero {
    display: grid;
    grid-template-columns: 360px 1fr;
    gap: 2rem;
    align-items: center;
    background: #ffffff;
    border: 1px solid var(--slate-200);
    border-radius: var(--radius-lg);
    padding: 1.5rem;
    margin-bottom: 1.75rem;
}

.cat-hero__banner {
    background: linear-gradient(135deg, #004098 0%, #002255 100%);
    border-radius: var(--radius-md);
    padding: 2.25rem 1.75rem;
    color: #ffffff;
    text-align: center;
    position: relative;
    overflow: hidden;
    box-shadow: 0 4px 14px rgba(0, 64, 152, 0.25);
}

.cat-hero__banner::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(255, 255, 255, 0.15) 10%, transparent 20%);
    background-size: 20px 20px;
    pointer-events: none;
}

.cat-hero__badge {
    display: inline-block;
    background: #ffcc00;
    color: #002255;
    font-size: 11px;
    font-weight: 800;
    padding: 3px 10px;
    border-radius: 99px;
    letter-spacing: 0.5px;
    margin-bottom: 0.75rem;
}

.cat-hero__banner-title {
    font-size: 1.45rem;
    font-weight: 800;
    color: #ffffff;
    margin-bottom: 0.5rem;
    line-height: 1.25;
}

.cat-hero__banner-sub {
    font-size: 0.85rem;
    color: rgba(255, 255, 255, 0.85);
    margin: 0;
}

.cat-hero__info {
    display: flex;
    flex-direction: column;
    justify-content: center;
}

.cat-hero__title {
    font-size: 1.6rem;
    font-weight: 800;
    color: var(--slate-900);
    margin-bottom: 0.75rem;
}

.cat-hero__desc {
    font-size: 0.925rem;
    color: var(--slate-600);
    line-height: 1.65;
    margin-bottom: 1.25rem;
}

.cat-hero__actions {
    display: flex;
    gap: 0.75rem;
    flex-wrap: wrap;
}

.btn-hero {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    padding: 0.65rem 1.4rem;
    border-radius: var(--radius-md);
    font-size: 0.875rem;
    font-weight: 700;
    text-decoration: none;
    transition: all 0.2s ease;
}

.btn-hero--red {
    background: #dc2626;
    color: #ffffff;
    border: 1px solid #dc2626;
}
.btn-hero--red:hover {
    background: #b91c1c;
    color: #ffffff;
}

.btn-hero--blue {
    background: #0284c7;
    color: #ffffff;
    border: 1px solid #0284c7;
}
.btn-hero--blue:hover {
    background: #0369a1;
    color: #ffffff;
}

/* Series Quick Bar */
.cat-series-bar {
    background: #ffffff;
    border: 1px solid var(--slate-200);
    border-radius: var(--radius-lg);
    padding: 1.25rem;
    margin-bottom: 2rem;
}

.cat-series-bar__grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(130px, 1fr));
    gap: 1rem;
}

.cat-series-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    padding: 0.75rem 0.5rem;
    border: 1px solid var(--slate-200);
    border-radius: var(--radius-md);
    background: #ffffff;
    text-decoration: none;
    transition: all 0.2s ease;
}

.cat-series-item:hover {
    border-color: #004098;
    box-shadow: 0 4px 12px rgba(0, 64, 152, 0.1);
    transform: translateY(-2px);
}

.cat-series-item__thumb {
    width: 68px;
    height: 68px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 0.5rem;
}

.cat-series-item__thumb img {
    max-width: 100%;
    max-height: 100%;
    object-fit: contain;
}

.cat-series-item__name {
    font-size: 0.85rem;
    font-weight: 700;
    color: #004098;
}

/* Sections */
.cat-sections-container {
    display: flex;
    flex-direction: column;
    gap: 2.25rem;
}

.cat-section-block {
    background: #ffffff;
    border: 1px solid var(--slate-200);
    border-radius: var(--radius-lg);
    padding: 1.5rem;
}

.cat-section-block__header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 0.75rem;
    border-bottom: 2px solid #004098;
    padding-bottom: 0.5rem;
}

.ribbon-title-box {
    background: #004098;
    color: #ffffff;
    padding: 6px 16px;
    border-radius: 4px 4px 0 0;
    position: relative;
    clip-path: polygon(0 0, 100% 0, 96% 100%, 0 100%);
    padding-right: 24px;
}

.ribbon-text {
    font-size: 0.95rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin: 0;
    color: #ffffff;
}

.cat-section-block__more {
    font-size: 0.85rem;
    font-weight: 700;
    color: #004098;
    text-decoration: none;
}
.cat-section-block__more:hover {
    text-decoration: underline;
}

.cat-section-block__desc {
    margin-bottom: 1.25rem;
}

.cat-section-block__desc ul {
    margin: 0;
    padding-left: 1.25rem;
    font-size: 0.85rem;
    color: #475569;
    line-height: 1.6;
}

.cat-section-block__grid {
    display: grid;
    grid-template-columns: repeat(6, 1fr);
    gap: 12px;
}

CSS;
    file_put_contents(__DIR__ . '/../public/css/main.css', $mainCss . $newCss);
    echo "Appended to main.css\n";
}

$respCss = file_get_contents(__DIR__ . '/../public/css/responsive.css');
if (strpos($respCss, '.cat-section-block__grid') === false) {
    $newResp = <<<CSS


/* Responsive Category Overview */
@media (max-width: 1100px) {
    .cat-section-block__grid {
        grid-template-columns: repeat(4, 1fr);
    }
    .cat-hero {
        grid-template-columns: 1fr;
    }
}
@media (max-width: 768px) {
    .cat-section-block__grid {
        grid-template-columns: repeat(2, 1fr) !important;
        gap: 8px !important;
    }
    .cat-series-bar__grid {
        grid-template-columns: repeat(3, 1fr) !important;
        gap: 0.5rem !important;
    }
    .cat-hero {
        padding: 1rem !important;
    }
    .cat-section-block {
        padding: 1rem !important;
    }
    .ribbon-text {
        font-size: 0.825rem !important;
    }
}

CSS;
    file_put_contents(__DIR__ . '/../public/css/responsive.css', $respCss . $newResp);
    echo "Appended to responsive.css\n";
}
