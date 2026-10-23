<?php
$html = file_get_contents('http://localhost/panasonic/san-pham/den-ban-hoc-panasonic-19w-hhlt062319-cao-cap');

// Check tabs
preg_match_all('/<button[^>]+class="pbody-tab-btn[^"]*"[^>]*>(.*?)<\/button>/is', $html, $tabMatches);
echo "=== TABS FOUND (" . count($tabMatches[1]) . ") ===\n";
foreach ($tabMatches[1] as $t) {
    echo "• " . trim(strip_tags($t)) . "\n";
}

// Check description snippet
if (preg_match('/<div class="rich-content-article"[^>]*>(.*?)<\/div>\s*<\/div>\s*<!-- Tab:/is', $html, $descMatch)) {
    echo "\n=== DESCRIPTION SNIPPET (First 600 chars) ===\n";
    echo substr(trim($descMatch[1]), 0, 600) . "...\n";
}
