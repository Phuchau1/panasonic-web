<?php
declare(strict_types=1);

set_time_limit(600);
ini_set('memory_limit', '512M');

$config = require __DIR__ . '/../config/database.php';
$dsn = "mysql:host={$config['host']};port={$config['port']};dbname={$config['database']};charset=utf8mb4";
$pdo = new PDO($dsn, $config['username'], $config['password'], [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
]);

$dienProducts = json_decode(file_get_contents(__DIR__ . '/dienpanasonic_cache.json'), true);
echo "1. DienPanasonic catalog: " . count($dienProducts) . " items.\n";

$uploadDir = realpath(__DIR__ . '/../public/uploads/products') ?: (__DIR__ . '/../public/uploads/products');

function fetchCleanImage(string $url): ?string {
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL            => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_TIMEOUT        => 25,
        CURLOPT_USERAGENT      => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
        CURLOPT_ENCODING       => '',
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
    ]);
    $raw = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    return ($code === 200 && $raw) ? $raw : null;
}

function cleanStr(?string $s): string {
    if (!$s) return '';
    $s = mb_strtolower($s, 'UTF-8');
    $s = preg_replace('/[àáạảãâầấậẩẫăằắặẳẵ]/u', 'a', $s);
    $s = preg_replace('/[èéẹẻẽêềếệểễ]/u', 'e', $s);
    $s = preg_replace('/[ìíịỉĩ]/u', 'i', $s);
    $s = preg_replace('/[òóọỏõôồốộổỗơờớợởỡ]/u', 'o', $s);
    $s = preg_replace('/[ùúụủũưừứựửữ]/u', 'u', $s);
    $s = preg_replace('/[ỳýỵỷỹ]/u', 'y', $s);
    $s = preg_replace('/[đ]/u', 'd', $s);
    $s = preg_replace('/[^a-z0-9]/', '', $s);
    return $s;
}

// Map specific high-value products to accurate dienpanasonic items
$manualMap = [
    // Quạt trần
    'f60gdsbe' => 'f60gdsb', // Fallback to clean F-60GDS
    '60gdsbe'  => 'f60gdsb',
    'f60gdnlg' => 'f60gdn',
    '60gdnlg'  => 'f60gdn',
    '60gdsb'   => 'f60gdsb',
    '60gdsw'   => 'f60gdsw',
];

$dienByCode = [];
foreach ($dienProducts as $dp) {
    if (empty($dp['images'])) continue;
    $s1 = cleanStr($dp['sku'] ?? '');
    $s2 = cleanStr($dp['name'] ?? '');
    if ($s1) $dienByCode[$s1] = $dp;
    
    // Extract key codes
    if (preg_match_all('/(f60[a-z0-9]+|nnv\d+[a-z0-9]*|nnp\d+[a-z0-9]*|hhglt\d+|hhlt\d+|nfv\d+[a-z0-9]*|hhgbw\d+|bbd\d+[a-z0-9]*|bbsf\d+[a-z0-9]*|fv\d+[a-z0-9]*|fy\d+[a-z0-9]*|gp\d+[a-z0-9]*|a201[a-z0-9]*|ga200[a-z0-9]*|dh\d+[a-z0-9]*|wev\d+[a-z0-9]*|weg\d+[a-z0-9]*|wmt\d+[a-z0-9]*)/i', $s1 . $s2, $m)) {
        foreach ($m[0] as $code) {
            if (!isset($dienByCode[$code])) {
                $dienByCode[$code] = $dp;
            }
        }
    }
}

$ourProducts = $pdo->query("SELECT id, name, model_code, sku, thumbnail FROM products WHERE status = 1")->fetchAll();
$updateStmt = $pdo->prepare("UPDATE products SET thumbnail = :thumb, gallery = :gallery WHERE id = :id");

$matchedCount = 0;
foreach ($ourProducts as $op) {
    $cands = [];
    $mCode = cleanStr($op['model_code'] ?? '');
    $sku = cleanStr($op['sku'] ?? '');
    $nameClean = cleanStr($op['name'] ?? '');
    
    if ($mCode && !str_starts_with($mCode, 'pana')) $cands[] = $mCode;
    if ($sku && !str_starts_with($sku, 'pana')) $cands[] = $sku;
    
    if (preg_match_all('/(f60[a-z0-9]+|nnv\d+[a-z0-9]*|nnp\d+[a-z0-9]*|hhglt\d+|hhlt\d+|nfv\d+[a-z0-9]*|hhgbw\d+|bbd\d+[a-z0-9]*|bbsf\d+[a-z0-9]*|fv\d+[a-z0-9]*|fy\d+[a-z0-9]*|gp\d+[a-z0-9]*|a201[a-z0-9]*|ga200[a-z0-9]*|dh\d+[a-z0-9]*|wev\d+[a-z0-9]*|weg\d+[a-z0-9]*|wmt\d+[a-z0-9]*)/i', $nameClean, $m)) {
        foreach ($m[0] as $code) {
            $cands[] = $code;
        }
    }
    
    $cands = array_unique($cands);
    $targetDp = null;
    $matchedKey = null;
    
    foreach ($cands as $c) {
        if (isset($dienByCode[$c])) {
            $targetDp = $dienByCode[$c];
            $matchedKey = $c;
            break;
        }
        if (isset($manualMap[$c]) && isset($dienByCode[$manualMap[$c]])) {
            $targetDp = $dienByCode[$manualMap[$c]];
            $matchedKey = $c . " (mapped to {$manualMap[$c]})";
            break;
        }
    }
    
    if ($targetDp && !empty($targetDp['images'])) {
        $imgUrl = $targetDp['images'][0];
        $ext = pathinfo(parse_url($imgUrl, PHP_URL_PATH), PATHINFO_EXTENSION) ?: 'jpg';
        $safeName = preg_replace('/[^a-z0-9]+/', '-', strtolower($targetDp['name']));
        $safeName = trim($safeName, '-');
        $fileName = "dp_{$op['id']}_{$safeName}.{$ext}";
        $localPath = $uploadDir . DIRECTORY_SEPARATOR . $fileName;
        $dbPath = "uploads/products/{$fileName}";
        
        $data = fetchCleanImage($imgUrl);
        if ($data && strlen($data) > 500) {
            file_put_contents($localPath, $data);
            
            $galleryArr = [$dbPath];
            foreach (array_slice($targetDp['images'], 1, 3) as $gi => $gUrl) {
                $gExt = pathinfo(parse_url($gUrl, PHP_URL_PATH), PATHINFO_EXTENSION) ?: 'jpg';
                $gFile = "dp_{$op['id']}_g{$gi}.{$gExt}";
                $gLocal = $uploadDir . DIRECTORY_SEPARATOR . $gFile;
                $gData = fetchCleanImage($gUrl);
                if ($gData) {
                    file_put_contents($gLocal, $gData);
                    $galleryArr[] = "uploads/products/{$gFile}";
                }
            }
            
            $updateStmt->execute([
                'thumb' => $dbPath,
                'gallery' => json_encode($galleryArr, JSON_UNESCAPED_SLASHES),
                'id' => $op['id'],
            ]);
            $matchedCount++;
            echo "[ID {$op['id']}] {$op['name']} -> Matched {$matchedKey}\n";
            echo "   Clean image saved: {$dbPath}\n";
        }
    }
}

echo "\nTotal replaced from dienpanasonic: {$matchedCount}\n";
