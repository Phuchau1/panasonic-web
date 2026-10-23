<?php
set_time_limit(600);

$cacheFile = __DIR__ . '/dienpanasonic_cache.json';
if (file_exists($cacheFile) && filesize($cacheFile) > 100000) {
    echo "Cache already exists: " . round(filesize($cacheFile) / 1024) . " KB\n";
    $data = json_decode(file_get_contents($cacheFile), true);
    echo "Total items in cache: " . count($data) . "\n";
    exit(0);
}

function fetchPage($page) {
    $url = "https://dienpanasonic.com.vn/wp-json/wc/store/v1/products?per_page=100&page=" . $page;
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_TIMEOUT => 25,
        CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
        CURLOPT_ENCODING => '',
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
    ]);
    $raw = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if (!$raw || $code !== 200) {
        return null;
    }
    
    $pos = strpos($raw, '[');
    if ($pos === false) return null;
    $clean = substr($raw, $pos);
    return json_decode($clean, true);
}

$allProducts = [];
$page = 1;
echo "Starting fetch from dienpanasonic.com.vn...\n";

while ($page <= 26) {
    echo "Fetching page $page... ";
    $items = fetchPage($page);
    if (!$items || empty($items)) {
        echo "Empty or finished.\n";
        break;
    }
    $count = count($items);
    echo "got $count items.\n";
    foreach ($items as $item) {
        $images = [];
        if (!empty($item['images'])) {
            foreach ($item['images'] as $img) {
                if (!empty($img['src'])) {
                    $images[] = $img['src'];
                }
            }
        }
        $allProducts[] = [
            'id' => $item['id'] ?? null,
            'name' => html_entity_decode($item['name'] ?? '', ENT_QUOTES | ENT_HTML5, 'UTF-8'),
            'sku' => $item['sku'] ?? '',
            'permalink' => $item['permalink'] ?? '',
            'images' => $images,
        ];
    }
    $page++;
    usleep(200000); // 0.2s pause
}

echo "Total products fetched: " . count($allProducts) . "\n";
file_put_contents($cacheFile, json_encode($allProducts, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
echo "Saved to $cacheFile\n";
