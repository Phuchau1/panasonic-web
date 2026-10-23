<?php
$banners = [
    'banner-thiet-bi-dien-panasonic.jpg' => 'https://thietbidiennhapkhau.com.vn/wp-content/uploads/banner-thiet-bi-dien-panasonic.jpg',
    'banner-moderva-halumie.jpg' => 'https://thietbidiennhapkhau.com.vn/wp-content/uploads/banner-thiet-bi-dien-panasonic-1.jpeg',
    'banner-phan-phoi-chinh-hang.jpg' => 'https://thietbidiennhapkhau.com.vn/wp-content/uploads/phan-phoi-thiet-bi-dien-panasonic.jpg',
    'banner-tong-kho-panasonic.jpg' => 'https://quangducsupply.vn/wp-content/uploads/2025/06/banner-thiet-bi-dien-panasonic.jpg',
    'banner-dien-xay-dung.jpg' => 'https://thietbidienchanhung.com/wp-content/uploads/2023/09/thiet-bi-dien-xay-dung-banner-1536x602.jpeg',
];

$ctx = stream_context_create([
    'http' => ['timeout' => 10, 'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)'],
    'ssl' => ['verify_peer' => false, 'verify_peer_name' => false]
]);

$dir = __DIR__ . '/../public/uploads/banners/';
foreach ($banners as $file => $url) {
    echo "Downloading {$url} -> {$file}...\n";
    $content = @file_get_contents($url, false, $ctx);
    if ($content) {
        file_put_contents($dir . $file, $content);
        echo "Saved {$file} (" . strlen($content) . " bytes)\n";
    } else {
        echo "Failed {$url}\n";
    }
}
