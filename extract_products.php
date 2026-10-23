<?php
$raw = file_get_contents('C:/Users/THIS PC/.gemini/antigravity/brain/c664354d-f1ee-4451-b5dc-d05dd1e07e7c/.system_generated/steps/3852/content.md');

preg_match_all('#<div class="product-small[^"]*">([\s\S]*?)</div>\s*</div>\s*</div>#iu', $raw, $matches);

if (!empty($matches[0])) {
    echo "Sample box:\n";
    echo substr($matches[0][0], 0, 1000) . "\n";
}
