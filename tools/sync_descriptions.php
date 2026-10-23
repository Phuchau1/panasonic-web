<?php
declare(strict_types=1);

set_time_limit(600);
$config = require __DIR__ . '/../config/database.php';
$dsn = "mysql:host={$config['host']};port={$config['port']};dbname={$config['database']};charset=utf8mb4";
$pdo = new PDO($dsn, $config['username'], $config['password'], [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]);

function fetchDienProduct(string $query): ?array {
    $url = "https://dienpanasonic.com.vn/wp-json/wc/store/v1/products?per_page=3&search=" . urlencode($query);
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 15,
        CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
        CURLOPT_ENCODING => '',
        CURLOPT_SSL_VERIFYPEER => false,
    ]);
    $raw = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($code !== 200 || !$raw) return null;
    $pos = strpos($raw, '[');
    if ($pos === false) return null;
    $items = json_decode(substr($raw, $pos), true);
    return !empty($items) ? $items[0] : null;
}

// Select active products that need richer description
$stmt = $pdo->query("SELECT id, name, model_code, sku, category_id, description, short_desc FROM products WHERE status = 1");
$products = $stmt->fetchAll();

echo "Total products: " . count($products) . "\n";
$updated = 0;

$updateStmt = $pdo->prepare("UPDATE products SET description = :desc, short_desc = :sdesc WHERE id = :id");

foreach ($products as $p) {
    // Only fetch if description is small or short
    if (!empty($p['description']) && strlen($p['description']) > 1500) {
        continue;
    }
    
    $searchKey = $p['model_code'];
    if (str_starts_with($searchKey, 'PANA-') || strlen($searchKey) < 4) {
        $searchKey = $p['sku'];
    }
    if (str_starts_with($searchKey, 'PANA-') || strlen($searchKey) < 4) {
        // use cleaned name
        $searchKey = preg_replace('/^(Đèn|Quạt|Máy|Công tắc|Ổ cắm|Bộ|Aptomat|Cầu dao)\s+/iu', '', $p['name']);
        $words = explode(' ', $searchKey);
        $searchKey = implode(' ', array_slice($words, 0, 4));
    }
    
    $cleanKey = trim(str_replace(['-', '_'], ' ', $searchKey));
    echo "[ID {$p['id']}] Searching: {$cleanKey}... ";
    
    $res = fetchDienProduct($cleanKey);
    if ($res && !empty($res['description']) && strlen($res['description']) > 300) {
        $desc = $res['description'];
        // Remove WP images that might point to external domain or keep them clean
        $shortDesc = !empty($res['short_description']) ? strip_tags($res['short_description'], '<p><br><strong><ul><li><table><tr><td><th>') : $p['short_desc'];
        
        $updateStmt->execute([
            'desc' => $desc,
            'sdesc' => $shortDesc,
            'id' => $p['id'],
        ]);
        $updated++;
        echo "FOUND & UPDATED (" . strlen($desc) . " bytes)\n";
    } else {
        echo "Not found.\n";
    }
    usleep(150000); // 0.15s
}

echo "Finished. Updated $updated products.\n";
