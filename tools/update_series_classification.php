<?php
$config = require __DIR__ . '/../config/database.php';
$dsn = "mysql:host={$config['host']};port={$config['port']};dbname={$config['database']};charset=utf8mb4";
$pdo = new PDO($dsn, $config['username'], $config['password'], [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]);

// 1. Ensure series 7 (Refina) exists
$hasRefina = $pdo->query("SELECT id FROM product_series WHERE slug = 'refina'")->fetch();
if (!$hasRefina) {
    $pdo->exec("INSERT INTO product_series (name, slug, tagline, color, sort_order, status) VALUES ('Refina', 'refina', 'Thiết kế bo tròn thanh thoát – ánh kim hiện đại', '#0284c7', 7, 1)");
    echo "Added series Refina.\n";
}

// 2. Move Tủ điện (BQDX, BQDF) from Category 1 to Category 2 (Thiết bị đóng cắt) where it belongs
$updatedTuDien = $pdo->exec("UPDATE products SET category_id = 2 WHERE (model_code LIKE 'BQD%' OR name LIKE '%Tủ điện%') AND category_id = 1");
echo "Moved $updatedTuDien Tủ điện products to Category 2.\n";

// 3. Classify switches and sockets in Category 1
// Minerva
$pdo->exec("UPDATE products SET series_id = 6 WHERE category_id = 1 AND (name LIKE '%Minerva%' OR model_code LIKE 'WMT%' OR sku LIKE 'WMT%')");
// Wide
$pdo->exec("UPDATE products SET series_id = 1 WHERE category_id = 1 AND (name LIKE '%Wide%' OR model_code LIKE 'WEV%' OR model_code LIKE 'WEG%' OR sku LIKE 'WEV%' OR sku LIKE 'WEG%')");
// Moderva
$pdo->exec("UPDATE products SET series_id = 4 WHERE category_id = 1 AND (name LIKE '%Moderva%' OR name LIKE '%moderva%')");
// Halumie
$pdo->exec("UPDATE products SET series_id = 2 WHERE category_id = 1 AND (name LIKE '%Halumie%' OR name LIKE '%halumie%')");
// Refina
$pdo->exec("UPDATE products SET series_id = 7 WHERE category_id = 1 AND (name LIKE '%Refina%' OR name LIKE '%refina%')");
// Gen-X
$pdo->exec("UPDATE products SET series_id = 3 WHERE category_id = 1 AND (name LIKE '%Gen-X%' OR name LIKE '%GenX%')");

echo "Switches and sockets series updated.\n";

// Check products count per series in Category 1
$counts = $pdo->query("SELECT s.name, COUNT(p.id) as total FROM products p LEFT JOIN product_series s ON p.series_id = s.id WHERE p.category_id = 1 AND p.status = 1 GROUP BY p.series_id")->fetchAll();
foreach ($counts as $c) {
    echo "  " . ($c['name'] ?: 'None') . ": " . $c['total'] . " products\n";
}
