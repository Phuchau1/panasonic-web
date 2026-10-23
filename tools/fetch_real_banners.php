<?php
$urls = [
    'https://haidangthietbidien.vn/',
    'https://thietbidienpanasonic.com/',
    'https://dienpanasonic.com.vn/',
    'https://thietbidientt.com/',
    'https://thietbidiendandung.vn/',
    'https://khohangpanasonic.com/',
    'https://trangiaelectric.com/',
    'https://thietbidien247.vn/',
    'https://denledpanasonic.vn/',
    'https://panasonic.net/electricworks/',
    'https://www.panasonic.com/vn/consumer/electric-works.html',
];

$foundBanners = [];

$ctx = stream_context_create([
    'http' => [
        'timeout' => 8,
        'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
    ],
    'ssl' => [
        'verify_peer' => false,
        'verify_peer_name' => false,
    ]
]);

foreach ($urls as $site) {
    echo "Scanning: {$site}...\n";
    $html = @file_get_contents($site, false, $ctx);
    if (!$html) continue;

    preg_match_all('/https?:\/\/[^\s"\'<>]+\.(?:jpg|jpeg|png|webp)/i', $html, $m);
    foreach (array_unique($m[0]) as $img) {
        if (preg_match('/banner|slider|slide|hero|carousel/i', $img)) {
            // Loại trừ icon, logo, thumb nhỏ
            if (!preg_match('/100x100|150x150|247x247|logo|icon|mini|avatar/i', $img)) {
                $foundBanners[] = [
                    'source' => $site,
                    'url'    => $img
                ];
            }
        }
    }
}

echo "\nTOTAL BANNERS FOUND: " . count($foundBanners) . "\n";
foreach ($foundBanners as $b) {
    echo " - [{$b['source']}] {$b['url']}\n";
}
