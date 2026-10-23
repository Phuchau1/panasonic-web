<?php
$urls = [
    'pew-bnr01.jpg' => 'https://panasonic.net/electricworks/assets/img/bnr01.jpg',
    'pew-bnr03.jpg' => 'https://panasonic.net/electricworks/assets/img/bnr03.jpg',
    'pew-bnr04.jpg' => 'https://panasonic.net/electricworks/assets/img/bnr04.jpg',
    'pew-ogp.jpg'   => 'https://panasonic.net/electricworks/common/img/PEW_ogp_EN.jpg',
];

$ctx = stream_context_create([
    'http' => ['timeout' => 8, 'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)'],
    'ssl' => ['verify_peer' => false, 'verify_peer_name' => false]
]);

foreach ($urls as $file => $u) {
    $data = @file_get_contents($u, false, $ctx);
    if ($data) {
        file_put_contents(__DIR__ . '/../public/uploads/banners/' . $file, $data);
        $size = getimagesizefromstring($data);
        echo "Saved {$file}: {$size[0]} x {$size[1]}\n";
    } else {
        echo "Failed: {$u}\n";
    }
}
