<?php
set_time_limit(600);
ini_set('memory_limit', '512M');

$cacheFile = __DIR__ . '/dienpanasonic_vn_cache.json';

function fetchPage($page) {
    $url = "https://dienpanasonic.vn/wp-json/wc/store/v1/products?per_page=100&page=" . $page;
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/120.0.0.0 Safari/537.36',
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
    ]);
    $raw = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if (!$raw || $code !== 200) {
        return null;
    }
    return json_decode($raw, true);
}

$all = [];
$page = 1;
echo "Starting fetch from dienpanasonic.vn (total 1158 products)...\n";

while (true) {
    echo "Fetching page $page... ";
    $items = fetchPage($page);
    if (!$items || empty($items)) {
        echo "Done or no items.\n";
        break;
    }
    $cnt = count($items);
    echo "got $cnt items.\n";
    foreach ($items as $item) {
        $images = [];
        if (!empty($item['images'])) {
            foreach ($item['images'] as $img) {
                if (!empty($img['src'])) {
                    $images[] = $img['src'];
                }
            }
        }
        $all[] = [
            'id' => $item['id'],
            'name' => $item['name'] ?? '',
            'sku' => $item['sku'] ?? '',
            'permalink' => $item['permalink'] ?? '',
            'images' => $images,
        ];
    }
    if ($cnt < 100) {
        break;
    }
    $page++;
    usleep(200000); // 200ms delay
}

echo "Total items retrieved: " . count($all) . "\n";
file_put_contents($cacheFile, json_encode($all, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
echo "Saved to $cacheFile\n";
