<?php
define('ROOT_PATH', __DIR__);
require 'core/Database.php';

$pdo = Core\Database::getInstance()->pdo();

// Check products in category 1 (Công tắc - Ổ cắm) where series_id is null
$prods = $pdo->query('SELECT id, name, model_code, slug FROM products WHERE category_id = 1')->fetchAll(PDO::FETCH_ASSOC);

echo "Total Switch & Socket products: " . count($prods) . "\n";

$mapped = 0;
foreach ($prods as $p) {
    $mc = strtoupper($p['model_code']);
    $name = mb_strtolower($p['name']);
    $slug = strtolower($p['slug']);

    $newSeries = null;
    if (str_starts_with($mc, 'WEVH') || str_contains($name, 'halumie') || str_contains($slug, 'halumie')) {
        $newSeries = 2; // Halumie
    } elseif (str_starts_with($mc, 'WMF') || str_contains($name, 'moderva') || str_contains($slug, 'moderva')) {
        $newSeries = 4; // Moderva
    } elseif (str_starts_with($mc, 'WMT') || str_contains($name, 'minerva') || str_contains($slug, 'minerva')) {
        $newSeries = 6; // Minerva
    } elseif (str_starts_with($mc, 'WNG') || str_contains($name, 'gen-x') || str_contains($name, 'gen x') || str_contains($slug, 'gen-x')) {
        $newSeries = 3; // Gen-X
    } elseif (str_starts_with($mc, 'WEV') || str_contains($name, 'wide') || str_contains($slug, 'wide')) {
        $newSeries = 1; // Wide
    } elseif (str_starts_with($mc, 'WEG') || str_starts_with($mc, 'WKG') || str_starts_with($mc, 'WN') || str_contains($name, 'full color') || str_contains($slug, 'full-color')) {
        $newSeries = 5; // Full Color
    } elseif (str_starts_with($mc, 'WR') || str_contains($name, 'refina') || str_contains($slug, 'refina')) {
        $newSeries = 7; // Refina
    }

    if ($newSeries !== null) {
        $pdo->prepare('UPDATE products SET series_id = :sid WHERE id = :id')->execute([
            'sid' => $newSeries,
            'id'  => $p['id']
        ]);
        $mapped++;
    }
}

echo "Successfully updated $mapped products with their correct Series!\n\n";

$rows = $pdo->query('
    SELECT s.id, s.name, s.slug, COUNT(p.id) as total
    FROM product_series s
    LEFT JOIN products p ON p.series_id = s.id AND p.status = 1
    GROUP BY s.id
')->fetchAll(PDO::FETCH_ASSOC);

echo "=== UPDATED PRODUCTS PER SERIES ===\n";
foreach ($rows as $r) {
    echo " - [{$r['id']}] {$r['name']} ({$r['slug']}): {$r['total']} products\n";
}
