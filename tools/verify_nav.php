<?php
$html = file_get_contents('http://localhost/panasonic/');
preg_match('/<ul class="header-unified__nav">(.*?)<\/ul>/is', $html, $navMatch);
if ($navMatch) {
    echo "=== NAV ITEMS ON HOMEPAGE ===\n";
    preg_match_all('/<li[^>]*>(.*?)<\/li>/is', $navMatch[1], $items);
    foreach ($items[1] as $item) {
        // Only top-level items
        if (str_contains($item, '<a class="hnav-link')) {
            preg_match('/<a[^>]+class="hnav-link[^"]*"[^>]*>(.*?)<\/a>/is', $item, $link);
            echo "• " . trim(strip_tags($link[1] ?? '')) . "\n";
        }
    }
}
