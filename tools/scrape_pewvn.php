<?php
$urls = [
    'https://panasonic.net/electricworks/vn/',
    'https://panasonic.net/electricworks/en_vn/',
    'https://panasonic.net/electricworks/en_vn/products/',
    'https://panasonic.net/electricworks/vn/wiring-device/',
    'https://panasonic.net/electricworks/vn/lighting/',
    'https://panasonic.net/electricworks/vn/indoor-air-quality/',
];

$ctx = stream_context_create([
    'http' => ['timeout' => 8, 'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)'],
    'ssl' => ['verify_peer' => false, 'verify_peer_name' => false]
]);

foreach ($urls as $u) {
    echo "Checking {$u}...\n";
    $html = @file_get_contents($u, false, $ctx);
    if (!$html) continue;
    preg_match_all('/(?:https?:\/\/[^\s"\'<>]+|\/[^\s"\'<>]+)\.(?:jpg|jpeg|png|webp)/i', $html, $m);
    foreach (array_unique($m[0]) as $img) {
        if (!preg_match('/icon|logo|100x|150x|btn/i', $img)) {
            $fullUrl = str_starts_with($img, 'http') ? $img : 'https://panasonic.net' . $img;
            echo " - {$fullUrl}\n";
        }
    }
}
