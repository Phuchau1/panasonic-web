<?php
$sites = [
    'https://thietbidienpanasonic.com/',
    'https://nanoco.com.vn/',
];

foreach ($sites as $site) {
    echo "=== CHECKING {$site} ===\n";
    $ctx = stream_context_create(['http' => [
        'timeout' => 10,
        'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)'
    ]]);
    $html = @file_get_contents($site, false, $ctx);
    if (!$html) {
        echo "Failed to fetch {$site}\n";
        continue;
    }
    preg_match_all('/https?:\/\/[^\s"\'<>]+\.(?:jpg|jpeg|png|webp)/i', $html, $matches);
    $urls = array_unique($matches[0]);
    $found = [];
    foreach ($urls as $u) {
        if (preg_match('/banner|slider|slide|home/i', $u)) {
            $found[] = $u;
        }
    }
    print_r(array_slice($found, 0, 15));
}
