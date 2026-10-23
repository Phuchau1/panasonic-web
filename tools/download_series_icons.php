<?php
function downloadImg($url, $dest) {
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_TIMEOUT => 15,
        CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
    ]);
    $data = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    if ($code === 200 && strlen($data) > 500) {
        file_put_contents($dest, $data);
        echo "Saved $dest (" . strlen($data) . " bytes)\n";
        return true;
    }
    return false;
}

$destDir = __DIR__ . '/../public/uploads/series';
if (!is_dir($destDir)) mkdir($destDir, 0755, true);

downloadImg('https://dienpanasonic.com.vn/wp-content/uploads/2025/05/gen-x-13.png', $destDir . '/gen-x.png');
