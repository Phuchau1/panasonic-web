<?php
$ctx = stream_context_create(['http' => [
    'timeout' => 10,
    'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)'
]]);
$html = @file_get_contents('https://thietbidienpanasonic.com/', false, $ctx);
if ($html) {
    preg_match_all('/<img[^>]+src=[\'"]([^\'"]+)[\'"]/i', $html, $m);
    print_r(array_slice($m[1], 0, 30));
}
