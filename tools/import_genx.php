<?php
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

$insertStmt = $pdo->prepare("
    INSERT INTO products (category_id, series_id, name, slug, sku, model_code, short_desc, description, price, wholesale_price, stock, unit, thumbnail, gallery, is_featured, status)
    VALUES (1, 3, :name, :slug, :sku, :model_code, 'Dòng công tắc nhôm phay cao cấp Panasonic Gen-X siêu mỏng chỉ 2mm, phím bấm chuyển động nhẹ nhàng.', '<p>Dòng sản phẩm Panasonic Gen-X mang phong cách thiết kế tương lai với bề mặt nhôm phay xước cao cấp, phím bấm êm ái, tiếp điểm hợp kim bạc nguyên chất siêu bền.</p>', :price, :wholesale_price, 30, 'Cái', :thumb, :gallery, 1, 1)
    ON DUPLICATE KEY UPDATE series_id = 3, thumbnail = VALUES(thumbnail), status = 1
");

foreach ($dienProducts as $d) {
    if (str_starts_with(strtoupper($d['sku']), 'WTFB')) {
        $sku = $d['sku'];
        $name = "Panasonic Gen-X " . $d['name'];
        $slug = "gen-x-" . strtolower(preg_replace('/[^a-z0-9]+/', '-', $sku));
        $imgUrl = $d['images'][0] ?? null;
        if (!$imgUrl) continue;
        
        $ext = pathinfo(parse_url($imgUrl, PHP_URL_PATH), PATHINFO_EXTENSION) ?: 'jpg';
        $fileName = "dp_genx_{$slug}.{$ext}";
        $localFile = $uploadDir . DIRECTORY_SEPARATOR . $fileName;
        $dbPath = "uploads/products/{$fileName}";
        
        $data = fetchImg($imgUrl);
        if ($data) {
            file_put_contents($localFile, $data);
            $price = 450000 + (rand(1, 10) * 20000);
            $insertStmt->execute([
                'name' => $name,
                'slug' => $slug,
                'sku' => $sku,
                'model_code' => $sku,
                'price' => $price,
                'wholesale_price' => (int)($price * 0.88),
                'thumb' => $dbPath,
                'gallery' => json_encode([$dbPath]),
            ]);
            echo "  + Added Gen-X: $name ($sku)\n";
        }
    }
}
