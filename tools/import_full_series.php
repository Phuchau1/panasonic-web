<?php
declare(strict_types=1);

$config = require __DIR__ . '/../config/database.php';
$dsn = "mysql:host={$config['host']};port={$config['port']};dbname={$config['database']};charset=utf8mb4";
$pdo = new PDO($dsn, $config['username'], $config['password'], [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]);

$dienProducts = json_decode(file_get_contents(__DIR__ . '/dienpanasonic_cache.json'), true);
$uploadDir = realpath(__DIR__ . '/../public/uploads/products') ?: (__DIR__ . '/../public/uploads/products');

function fetchImg(string $url): ?string {
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 20,
        CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
    ]);
    $res = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    return ($code === 200 && $res) ? $res : null;
}

function makeSlug(string $title): string {
    $s = mb_strtolower($title, 'UTF-8');
    $s = preg_replace('/[àáạảãâầấậẩẫăằắặẳẵ]/u', 'a', $s);
    $s = preg_replace('/[èéẹẻẽêềếệểễ]/u', 'e', $s);
    $s = preg_replace('/[ìíịỉĩ]/u', 'i', $s);
    $s = preg_replace('/[òóọỏõôồốộổỗơờớợởỡ]/u', 'o', $s);
    $s = preg_replace('/[ùúụủũưừứựửữ]/u', 'u', $s);
    $s = preg_replace('/[ỳýỵỷỹ]/u', 'y', $s);
    $s = preg_replace('/[đ]/u', 'd', $s);
    $s = preg_replace('/[^a-z0-9]+/', '-', $s);
    return trim($s, '-');
}

$insertStmt = $pdo->prepare("
    INSERT INTO products (category_id, series_id, name, slug, sku, model_code, short_desc, description, price, wholesale_price, stock, unit, thumbnail, gallery, is_featured, status)
    VALUES (:cat_id, :series_id, :name, :slug, :sku, :model_code, :short_desc, :description, :price, :wholesale_price, 50, 'Cái', :thumb, :gallery, 1, 1)
    ON DUPLICATE KEY UPDATE series_id = VALUES(series_id), thumbnail = VALUES(thumbnail), status = 1
");

// Select items for series
$seriesTargets = [
    4 => ['prefix' => 'WMF', 'name' => 'Moderva', 'limit' => 8, 'price_base' => 125000],
    2 => ['prefix' => 'WEF', 'name' => 'Halumie', 'limit' => 8, 'price_base' => 45000],
    7 => ['prefix' => 'WEVH', 'name' => 'Refina', 'limit' => 6, 'price_base' => 65000],
];

$count = 0;
foreach ($seriesTargets as $seriesId => $cfg) {
    echo "Importing series {$cfg['name']} (series_id={$seriesId})...\n";
    $imported = 0;
    foreach ($dienProducts as $dp) {
        if ($imported >= $cfg['limit']) break;
        if (empty($dp['images'])) continue;
        
        $sku = strtoupper($dp['sku'] ?? '');
        $name = $dp['name'] ?? '';
        
        if (str_starts_with($sku, $cfg['prefix']) || str_contains(strtoupper($name), $cfg['prefix'])) {
            $modelCode = $sku ?: $cfg['prefix'] . '-' . $dp['id'];
            $slug = makeSlug($name . '-' . $modelCode);
            
            // Check if exists
            $exists = $pdo->query("SELECT id FROM products WHERE model_code = " . $pdo->quote($modelCode) . " OR sku = " . $pdo->quote($sku))->fetch();
            if ($exists) {
                $pdo->exec("UPDATE products SET series_id = {$seriesId}, category_id = 1, status = 1 WHERE id = {$exists['id']}");
                $imported++;
                continue;
            }
            
            // Download image
            $imgUrl = $dp['images'][0];
            $ext = pathinfo(parse_url($imgUrl, PHP_URL_PATH), PATHINFO_EXTENSION) ?: 'jpg';
            $fileName = "dp_s{$seriesId}_{$slug}.{$ext}";
            $localFile = $uploadDir . DIRECTORY_SEPARATOR . $fileName;
            $dbPath = "uploads/products/{$fileName}";
            
            $imgData = fetchImg($imgUrl);
            if ($imgData) {
                file_put_contents($localFile, $imgData);
                
                $price = $cfg['price_base'] + (rand(1, 10) * 5000);
                $insertStmt->execute([
                    'cat_id' => 1,
                    'series_id' => $seriesId,
                    'name' => $name,
                    'slug' => $slug,
                    'sku' => $sku ?: $modelCode,
                    'model_code' => $modelCode,
                    'short_desc' => "Dòng công tắc ổ cắm Panasonic {$cfg['name']} chính hãng, tiếp điểm hợp kim bạc độ bền cao.",
                    'description' => "<p>{$name} thuộc dòng sản phẩm Panasonic {$cfg['name']} cao cấp, thiết kế thẩm mỹ vượt trội, dễ dàng lắp đặt và an toàn tuyệt đối theo tiêu chuẩn JIS Nhật Bản.</p>",
                    'price' => $price,
                    'wholesale_price' => (int)($price * 0.85),
                    'thumb' => $dbPath,
                    'gallery' => json_encode([$dbPath]),
                ]);
                $imported++;
                $count++;
                echo "  + Added [{$cfg['name']}]: {$name} ({$modelCode})\n";
            }
        }
    }
}

echo "Total new series products imported: $count\n";
