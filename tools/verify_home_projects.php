<?php
$html = file_get_contents('http://localhost/panasonic/');
preg_match_all('/<h3 class="project-title">(.*?)<\/h3>/is', $html, $titles);
echo "=== PROJECTS ON HOMEPAGE (" . count($titles[1]) . ") ===\n";
foreach ($titles[1] as $t) {
    echo "• " . trim($t) . "\n";
}
