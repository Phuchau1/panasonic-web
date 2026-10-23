<?php
$html = file_get_contents('https://haidangthietbidien.vn/du-an/');
preg_match_all('/<article[^>]*>(.*?)<\/article>/is', $html, $articles);
echo "Articles found on /du-an/: " . count($articles[0]) . "\n";

if (empty($articles[0])) {
    // try finding post or card blocks
    preg_match_all('/<div[^>]+class=["\'][^"\']*(?:project|post|item)[^"\']*["\'][^>]*>(.*?)<\/div>/is', $html, $articles);
    echo "Div items found: " . count($articles[0]) . "\n";
}

preg_match_all('/<a[^>]+href=["\']([^"\']+)["\'][^>]*>([^<]+)<\/a>/i', $html, $links);
foreach ($links[2] as $idx => $txt) {
    $t = trim($txt);
    if (strlen($t) > 10 && !preg_match('/(Xem thêm|Trang chủ|Liên hệ|Sản phẩm)/i', $t)) {
        echo "Link text: $t -> " . $links[1][$idx] . "\n";
    }
}
