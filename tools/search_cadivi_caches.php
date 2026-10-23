<?php
$vn = json_decode(file_get_contents(__DIR__ . '/dienpanasonic_vn_cache.json'), true);
$com = json_decode(file_get_contents(__DIR__ . '/dienpanasonic_cache.json'), true);

echo "Searching in dienpanasonic_vn_cache.json:\n";
$foundVn = 0;
foreach ($vn as $it) {
    if (stripos($it['name'], 'cadivi') !== false || stripos($it['name'], 'dây điện') !== false || stripos($it['name'], 'cáp điện') !== false) {
        $foundVn++;
        echo "   * {$it['name']} => " . ($it['images'][0] ?? 'no img') . "\n";
    }
}
echo "Total in VN cache: $foundVn\n\n";

echo "Searching in dienpanasonic_cache.json:\n";
$foundCom = 0;
foreach ($com as $it) {
    if (stripos($it['name'], 'cadivi') !== false || stripos($it['name'], 'dây điện') !== false || stripos($it['name'], 'cáp điện') !== false) {
        $foundCom++;
        if ($foundCom <= 10) {
            echo "   * {$it['name']} => " . ($it['images'][0] ?? 'no img') . "\n";
        }
    }
}
echo "Total in COM cache: $foundCom\n";
