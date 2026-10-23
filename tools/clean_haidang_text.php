<?php
$config = require __DIR__ . '/../config/database.php';
$pdo = new PDO("mysql:host={$config['host']};dbname={$config['database']}", $config['username'], $config['password']);

$stmt = $pdo->query("SELECT id, name, description, short_desc FROM products WHERE description LIKE '%haidang%' OR description LIKE '%hải đăng%' OR short_desc LIKE '%haidang%' OR short_desc LIKE '%hải đăng%' OR name LIKE '%hải đăng%'");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "Found " . count($products) . " products with Hai Dang references in text.\n";

$updateStmt = $pdo->prepare("UPDATE products SET name = :name, description = :desc, short_desc = :sdesc WHERE id = :id");

$replacements = [
    'Hải Đăng' => 'Panasonic Chính Hãng',
    'hải đăng' => 'Panasonic chính hãng',
    'HẢI ĐĂNG' => 'PANASONIC CHÍNH HÃNG',
    'haidangthietbidien.vn' => 'dienpanasonic.com.vn',
    'haidang' => 'panasonic',
    'Thiết Bị Điện Hải Đăng' => 'Tổng Kho Phân Phối Thiết Bị Điện Panasonic',
    'thiết bị điện Hải Đăng' => 'Tổng kho phân phối thiết bị điện Panasonic',
    'Điện Hải Đăng' => 'Thiết Bị Điện Panasonic',
    'điện Hải Đăng' => 'thiết bị điện Panasonic',
];

$count = 0;
foreach ($products as $p) {
    $desc = $p['description'] ?? '';
    $sdesc = $p['short_desc'] ?? '';
    $name = $p['name'] ?? '';
    
    foreach ($replacements as $search => $replace) {
        $desc = str_ireplace($search, $replace, $desc);
        $sdesc = str_ireplace($search, $replace, $sdesc);
        $name = str_ireplace($search, $replace, $name);
    }
    
    $updateStmt->execute([
        'name' => $name,
        'desc' => $desc,
        'sdesc' => $sdesc,
        'id' => $p['id']
    ]);
    $count++;
}

echo "Successfully cleaned $count products.\n";

// Double check
$remaining = $pdo->query("SELECT COUNT(*) FROM products WHERE description LIKE '%haidang%' OR description LIKE '%hải đăng%'")->fetchColumn();
echo "Remaining occurrences: $remaining\n";
