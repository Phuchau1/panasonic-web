<?php
function testSearch($q) {
    $url = "https://dienpanasonic.com.vn/wp-json/wc/store/v1/products?per_page=3&search=" . urlencode($q);
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 15,
        CURLOPT_USERAGENT => 'Mozilla/5.0',
        CURLOPT_SSL_VERIFYPEER => false,
    ]);
    $raw = curl_exec($ch);
    curl_close($ch);
    $pos = strpos($raw, '[');
    if ($pos === false) return "No [ found";
    $items = json_decode(substr($raw, $pos), true);
    if (empty($items)) return "No items";
    return $items[0]['name'] . " (desc length: " . strlen($items[0]['description'] ?? '') . ")";
}

echo "WMF501: " . testSearch("WMF501") . "\n";
echo "WEV1582: " . testSearch("WEV1582") . "\n";
echo "WEV5001: " . testSearch("WEV5001") . "\n";
echo "BBD2161: " . testSearch("BBD2161") . "\n";
echo "F-60UFN: " . testSearch("F-60UFN") . "\n";
echo "Moderva: " . testSearch("Moderva") . "\n";
echo "WEVH5531: " . testSearch("WEVH5531") . "\n";
