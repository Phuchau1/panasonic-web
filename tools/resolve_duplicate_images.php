<?php
define('ROOT_PATH', dirname(__DIR__));
require ROOT_PATH . '/core/Database.php';

set_time_limit(900);
ini_set('memory_limit', '512M');

$pdo = Core\Database::getInstance()->pdo();

// 1. Identify which products have duplicate images (same md5 or shared thumbnail or placeholder)
$prods = $pdo->query('SELECT id, model_code, name, slug, thumbnail, category_id, series_id FROM products WHERE status = 1')->fetchAll(PDO::FETCH_ASSOC);

$hashMap = [];
$dupProductIds = [];

foreach ($prods as $p) {
    $thumb = $p['thumbnail'];
    if (!$thumb || in_array($thumb, [
        'uploads/products/placeholder.jpg',
        'uploads/products/switch-white.svg',
        'uploads/products/switch-grey.svg',
        'uploads/products/socket-white.svg'
    ])) {
        $dupProductIds[$p['id']] = true;
        continue;
    }
    $fullPath = ROOT_PATH . '/public/' . ltrim($thumb, '/');
    if (!file_exists($fullPath)) {
        $dupProductIds[$p['id']] = true;
        continue;
    }
    $hash = md5_file($fullPath);
    $hashMap[$hash][] = $p['id'];
}

foreach ($hashMap as $hash => $ids) {
    if (count($ids) > 1) {
        // Keep the first product if its filename or model code looks like it belongs to it,
        // but mark the rest for replacement!
        foreach (array_slice($ids, 1) as $id) {
            $dupProductIds[$id] = true;
        }
    }
}

echo "Total products in database: " . count($prods) . "\n";
echo "Total candidate products needing unique authentic images: " . count($dupProductIds) . "\n\n";

// 2. Build index from dienpanasonic_vn_cache.json
$vnCache = json_decode(file_get_contents(__DIR__ . '/dienpanasonic_vn_cache.json'), true) ?: [];
// Build index from dienpanasonic_cache.json
$comCache = json_decode(file_get_contents(__DIR__ . '/dienpanasonic_cache.json'), true) ?: [];

echo "VN Cache items: " . count($vnCache) . "\n";
echo "COM Cache items: " . count($comCache) . "\n";

$cacheCatalog = [];

function extractCodes($text) {
    $codes = [];
    if (preg_match_all('/\b([A-Z]{2,5}[0-9]{2,}[A-Z0-9\/\-]*)\b/i', $text, $m)) {
        foreach ($m[1] as $c) {
            $codes[] = strtoupper($c);
        }
    }
    return $codes;
}

foreach ($vnCache as $it) {
    if (empty($it['images'])) continue;
    $img = $it['images'][0];
    if (!empty($it['sku'])) {
        $cacheCatalog[strtoupper(trim($it['sku']))] = $img;
        $clean = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $it['sku']));
        if ($clean) $cacheCatalog[$clean] = $img;
    }
    foreach (extractCodes($it['name']) as $c) {
        if (!isset($cacheCatalog[$c])) $cacheCatalog[$c] = $img;
        $clean = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $c));
        if ($clean && !isset($cacheCatalog[$clean])) $cacheCatalog[$clean] = $img;
    }
}

foreach ($comCache as $it) {
    if (empty($it['images'])) continue;
    $img = $it['images'][0];
    if (!empty($it['sku'])) {
        $sku = strtoupper(trim($it['sku']));
        if (!isset($cacheCatalog[$sku])) $cacheCatalog[$sku] = $img;
        $clean = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $sku));
        if ($clean && !isset($cacheCatalog[$clean])) $cacheCatalog[$clean] = $img;
    }
    foreach (extractCodes($it['name']) as $c) {
        if (!isset($cacheCatalog[$c])) $cacheCatalog[$c] = $img;
        $clean = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $c));
        if ($clean && !isset($cacheCatalog[$clean])) $cacheCatalog[$clean] = $img;
    }
}

echo "Indexed " . count($cacheCatalog) . " unique code keys in cache catalog.\n\n";

// Function to download image safely
function downloadProductImg($url, $savePath) {
    // Try clean full resolution first (without -247x296, -300x300, etc.)
    $cleanUrl = preg_replace('/-\d+x\d+(\.[a-z]+)$/i', '$1', $url);
    
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $cleanUrl,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_TIMEOUT => 20,
        CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/120.0.0.0 Safari/537.36',
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
    ]);
    $data = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($code !== 200 || strlen($data) < 500) {
        // Fallback to original url
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_TIMEOUT => 20,
            CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/120.0.0.0 Safari/537.36',
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
        ]);
        $data = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
    }

    if ($code === 200 && strlen($data) >= 500) {
        file_put_contents($savePath, $data);
        return true;
    }
    return false;
}

// Function to search on dienpanasonic.vn directly if not in cache
function searchLiveDienpanasonic($query) {
    $url = "https://dienpanasonic.vn/?s=" . rawurlencode($query) . "&post_type=product";
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_TIMEOUT => 15,
        CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/120.0.0.0 Safari/537.36',
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
    ]);
    $html = curl_exec($ch);
    curl_close($ch);
    if (!$html) return null;

    if (preg_match('/<img[^>]+(?:data-src|src)=["\']([^"\']+\.(?:png|jpg|webp|jpeg)[^"\']*)["\'][^>]*class="[^"]*attachment-woocommerce_thumbnail/i', $html, $m)) {
        return $m[1];
    }
    if (preg_match('/<div class="product-small[^>]*>.*?<img[^>]+(?:data-src|src)=["\']([^"\']+\.(?:png|jpg|webp|jpeg)[^"\']*)["\']/is', $html, $m)) {
        return $m[1];
    }
    return null;
}

$uploadDir = ROOT_PATH . '/public/uploads/products/';
$updatedCount = 0;
$skippedCount = 0;

$stmtUpdate = $pdo->prepare('UPDATE products SET thumbnail = :thumb WHERE id = :id');

echo "--- Resolving and replacing duplicate images ---\n";

foreach ($prods as $p) {
    $id = $p['id'];
    if (!isset($dupProductIds[$id])) {
        continue;
    }

    $mc = strtoupper(trim($p['model_code']));
    $cleanMc = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $mc));
    
    // Look up in catalog
    $foundImg = $cacheCatalog[$mc] ?? ($cacheCatalog[$cleanMc] ?? null);

    // If not found, try searching by stripped model code prefixes/suffixes
    if (!$foundImg) {
        // e.g. WMT501-VN -> WMT501
        $baseMc = preg_replace('/(-VN|\/VN|-SP|-7|-7K)$/i', '', $mc);
        $cleanBase = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $baseMc));
        $foundImg = $cacheCatalog[$baseMc] ?? ($cacheCatalog[$cleanBase] ?? null);
    }

    // If still not found and model_code is valid, try live search on dienpanasonic.vn
    if (!$foundImg && strlen($mc) >= 4 && !in_array($mc, ['PANASONIC', 'PANASONICWIDE', 'PANASONICHALUMI'])) {
        $foundImg = searchLiveDienpanasonic($mc);
        if ($foundImg) {
            usleep(150000); // polite pause
        }
    }

    if ($foundImg) {
        $ext = pathinfo(parse_url($foundImg, PHP_URL_PATH), PATHINFO_EXTENSION) ?: 'jpg';
        if (!in_array(strtolower($ext), ['jpg', 'jpeg', 'png', 'webp'])) {
            $ext = 'jpg';
        }
        $newFilename = strtolower(preg_replace('/[^a-zA-Z0-9_\-]/', '', "p{$id}_{$cleanMc}")) . '.' . $ext;
        $savePath = $uploadDir . $newFilename;

        if (downloadProductImg($foundImg, $savePath)) {
            $relativeThumb = 'uploads/products/' . $newFilename;
            $stmtUpdate->execute(['thumb' => $relativeThumb, 'id' => $id]);
            $updatedCount++;
            echo "[UPDATED #{$id}] {$mc} -> {$relativeThumb} (from {$foundImg})\n";
        } else {
            $skippedCount++;
            echo "[FAIL DL #{$id}] {$mc} -> Could not download {$foundImg}\n";
        }
    } else {
        $skippedCount++;
        echo "[NOT FOUND #{$id}] {$mc} : {$p['name']}\n";
    }
}

echo "\n============================================\n";
echo "Successfully updated: {$updatedCount} products with unique authentic images!\n";
echo "Skipped / not found: {$skippedCount}\n";
