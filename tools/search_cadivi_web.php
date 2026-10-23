<?php
function checkSite($url) {
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_TIMEOUT => 15,
        CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/120.0.0.0 Safari/537.36',
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
    ]);
    $html = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    echo "[$code] $url -> " . strlen($html) . " bytes\n";
    if ($html) {
        preg_match_all('/<img[^>]+(?:data-src|src)=["\']([^"\']+\.(?:png|jpg|webp|jpeg)[^"\']*)["\']/i', $html, $m);
        echo "Found " . count($m[1]) . " images.\n";
        foreach (array_slice($m[1], 0, 5) as $im) {
            echo "   -> $im\n";
        }
    }
}

checkSite('https://haidangthietbidien.vn/?s=cadivi&post_type=product');
checkSite('https://dienpanasonic.vn/?s=d%C3%A2y+%C4%91i%E1%BB%87n&post_type=product');
