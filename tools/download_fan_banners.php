<?php
$list = [
    'quat-hut-5.jpg' => 'https://haidangthietbidien.vn/wp-content/uploads/2026/02/Quat-hut-gan-tuong-5.jpg',
    'quat-cabinet.jpg' => 'https://haidangthietbidien.vn/wp-content/uploads/2026/02/Quat-hut-cabinet-Panasonic.jpg',
    'quat-tran-dep.jpg' => 'https://mrvu.vn/wp-content/uploads/2026/02/mau-quat-tran-panasonic-dep-7.jpeg',
];

$ctx = stream_context_create([
    'http' => ['timeout' => 8, 'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)'],
    'ssl' => ['verify_peer' => false, 'verify_peer_name' => false]
]);

foreach ($list as $file => $u) {
    $data = @file_get_contents($u, false, $ctx);
    if ($data) {
        file_put_contents(__DIR__ . '/../public/uploads/banners/' . $file, $data);
        $size = getimagesizefromstring($data);
        echo "Saved {$file}: {$size[0]} x {$size[1]}\n";
    } else {
        echo "Failed {$u}\n";
    }
}
