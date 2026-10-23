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
    INSERT INTO products (category_id, name, slug, sku, model_code, short_desc, description, price, wholesale_price, stock, unit, thumbnail, gallery, is_featured, status)
    VALUES (2, :name, :slug, :sku, :model_code, 'Aptomat tép MCB Panasonic chính hãng bảo vệ quá tải và ngắn mạch tiêu chuẩn Nhật Bản.', '<p>Aptomat tép MCB Panasonic với tiếp điểm hợp kim bạc dập hồ quang siêu tốc, độ nhạy cao ngắt mạch an toàn, đáp ứng tiêu chuẩn an toàn điện IEC 60898.</p>', :price, :wholesale_price, 100, 'Cái', :thumb, :gallery, 1, 1)
    ON DUPLICATE KEY UPDATE category_id = 2, status = 1
");

$imported = 0;
foreach ($dienProducts as $d) {
    if ($imported >= 6) break;
    $sku = $d['sku'] ?? '';
    if (str_starts_with(strtoupper($sku), 'BBD')) {
        $name = $d['name'];
        $slug = "mcb-" . strtolower(preg_replace('/[^a-z0-9]+/', '-', $sku));
        $imgUrl = $d['images'][0] ?? null;
        if (!$imgUrl) continue;
        
        $ext = pathinfo(parse_url($imgUrl, PHP_URL_PATH), PATHINFO_EXTENSION) ?: 'jpg';
        $fileName = "dp_mcb_{$slug}.{$ext}";
        $localFile = $uploadDir . DIRECTORY_SEPARATOR . $fileName;
        $dbPath = "uploads/products/{$fileName}";
        
        $data = fetchImg($imgUrl);
        if ($data) {
            file_put_contents($localFile, $data);
            $price = 85000 + (rand(1, 10) * 10000);
            $insertStmt->execute([
                'name' => $name,
                'slug' => $slug,
                'sku' => $sku,
                'model_code' => $sku,
                'price' => $price,
                'wholesale_price' => (int)($price * 0.85),
                'thumb' => $dbPath,
                'gallery' => json_encode([$dbPath]),
            ]);
            $imported++;
            echo "  + Added MCB: $name ($sku)\n";
        }
    }
}
