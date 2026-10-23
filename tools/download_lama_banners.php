<?php
$list = [
    'lama-den.jpg' => 'https://thietbidienlama.com/wp-content/uploads/2026/07/Bao-Gia-Den-Panasonic-Moi-Nhat.jpg',
    'lama-thietbi.jpg' => 'https://thietbidienlama.com/wp-content/uploads/2026/03/Thiet-Bi-Dien-Panasonic.jpg',
    'lama-moderva.jpg' => 'https://thietbidienlama.com/wp-content/uploads/2026/07/Thiet-Bi-Dien-Panasonic-Dong-Moderva-Mau-Vang-Anh-Kim-Cong-Trinh-Anh-Hung1.jpg',
    'lama-cuahang.jpg' => 'https://thietbidienlama.com/wp-content/uploads/2024/06/cua-hang-thiet-bi-dien-panasonic.jpg',
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
