<?php
define('ROOT_PATH', dirname(__DIR__));
require ROOT_PATH . '/core/Database.php';

function stripVnAccents(string $str): string {
    $str = preg_replace('/[àáạảãâầấậẩẫăằắặẳẵ]/u', 'a', $str);
    $str = preg_replace('/[ÀÁẠẢÃÂẦẤẬẨẪĂẰẮẶẲẴ]/u', 'A', $str);
    $str = preg_replace('/[èéẹẻẽêềếệểễ]/u', 'e', $str);
    $str = preg_replace('/[ÈÉẸẺẼÊỀẾỆỂỄ]/u', 'E', $str);
    $str = preg_replace('/[ìíịỉĩ]/u', 'i', $str);
    $str = preg_replace('/[ÌÍỊỈĨ]/u', 'I', $str);
    $str = preg_replace('/[òóọỏõôồốộổỗơờớợởỡ]/u', 'o', $str);
    $str = preg_replace('/[ÒÓỌỎÕÔỒỐỘỔỖƠỜỚỢỞỠ]/u', 'O', $str);
    $str = preg_replace('/[ùúụủũưừứựửữ]/u', 'u', $str);
    $str = preg_replace('/[ÙÚỤỦŨƯỪỨỰỬỮ]/u', 'U', $str);
    $str = preg_replace('/[ỳýỵỷỹ]/u', 'y', $str);
    $str = preg_replace('/[ỲÝỴỶỸ]/u', 'Y', $str);
    $str = preg_replace('/[đ]/u', 'd', $str);
    $str = preg_replace('/[Đ]/u', 'D', $str);
    return mb_strtolower($str);
}

$pdo = Core\Database::getInstance()->pdo();

// Check if search_index column exists, if not add it
$colExists = $pdo->query("SHOW COLUMNS FROM products LIKE 'search_index'")->fetch();
if (!$colExists) {
    $pdo->query("ALTER TABLE products ADD COLUMN search_index TEXT NULL AFTER specs");
    echo "Added search_index column to products.\n";
}

// Build search_index for all products
$rows = $pdo->query("
    SELECT p.id, p.name, p.model_code, p.sku, p.short_desc, p.rated_current, p.breaking_capacity, p.poles,
           c.name as category_name, s.name as series_name
    FROM products p
    INNER JOIN categories c ON c.id = p.category_id
    LEFT JOIN product_series s ON s.id = p.series_id
")->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->prepare("UPDATE products SET search_index = :idx WHERE id = :id");

foreach ($rows as $r) {
    $parts = [];
    $parts[] = $r['name'];
    $parts[] = stripVnAccents($r['name']);
    $parts[] = $r['model_code'];
    $parts[] = str_replace(['-', ' ', '.', '/'], '', $r['model_code']); // normalized model code (e.g. WEV5001SW, F60UFN)
    $parts[] = $r['sku'];
    $parts[] = $r['category_name'];
    $parts[] = stripVnAccents($r['category_name']);
    if (!empty($r['series_name'])) {
        $parts[] = $r['series_name'];
        $parts[] = stripVnAccents($r['series_name']);
    }
    if (!empty($r['short_desc'])) {
        $parts[] = $r['short_desc'];
        $parts[] = stripVnAccents($r['short_desc']);
    }
    if (!empty($r['rated_current'])) {
        $parts[] = $r['rated_current'] . 'A';
        $parts[] = $r['rated_current'] . ' A';
        $parts[] = (int)$r['rated_current'] . 'A';
    }
    if (!empty($r['breaking_capacity'])) {
        $parts[] = $r['breaking_capacity'] . 'kA';
        $parts[] = $r['breaking_capacity'] . ' kA';
    }
    if (!empty($r['poles'])) {
        $parts[] = $r['poles'] . 'P';
        $parts[] = $r['poles'] . ' P';
        $parts[] = $r['poles'] . ' cuc';
        $parts[] = $r['poles'] . ' cực';
    }

    // Add smart synonyms based on keywords in name / category
    $lower = mb_strtolower($r['name'] . ' ' . $r['category_name']);
    if (str_contains($lower, 'công tắc') || str_contains($lower, 'cong tac')) {
        $parts[] = 'cong tac cong-tac switch hạt công tắc phím bấm 1 chiều 2 chiều đảo chiều cầu thang';
    }
    if (str_contains($lower, 'ổ cắm') || str_contains($lower, 'o cam')) {
        $parts[] = 'o cam o-cam socket phích cắm chấu cắm cắm điện đôi đơn 2 chấu 3 chấu';
    }
    if (str_contains($lower, 'mcb') || str_contains($lower, 'aptomat') || str_contains($lower, 'cầu dao') || str_contains($lower, 'cb')) {
        $parts[] = 'cb aptomat attomat ap to mat cau dao cau-dao mcb ngắt điện tép chống chập ngắn mạch';
    }
    if (str_contains($lower, 'mccb')) {
        $parts[] = 'mccb aptomat khoi cau dao khoi cb khoi cong nghiep';
    }
    if (str_contains($lower, 'chống giật') || str_contains($lower, 'rcbo') || str_contains($lower, 'elcb') || str_contains($lower, 'rò')) {
        $parts[] = 'chong giat chong-giat chong ro dong ro rcbo elcb an toan bao ve';
    }
    if (str_contains($lower, 'đèn') || str_contains($lower, 'led') || str_contains($lower, 'downlight')) {
        $parts[] = 'den den-led chieu sang downlight am tran op tran roi trang vang trung tinh';
    }
    if (str_contains($lower, 'quạt') || str_contains($lower, 'quat')) {
        $parts[] = 'quat quat-tran quat-hut thong gio hut mui quat tran lam mat lam-mat';
    }
    if (str_contains($lower, 'tủ điện') || str_contains($lower, 'tu dien')) {
        $parts[] = 'tu dien hop dien am tuong chua aptomat bqdx bqdf';
    }
    if (str_contains($lower, 'bơm') || str_contains($lower, 'bom')) {
        $parts[] = 'may bom bom nuoc tang ap day cao gia dinh';
    }
    if (str_contains($lower, 'nước nóng') || str_contains($lower, 'nong lanh')) {
        $parts[] = 'may nuoc nong binh nong lanh tam tam nuoc nong';
    }
    if (str_contains($lower, 'cảm ứng') || str_contains($lower, 'hồng ngoại') || str_contains($lower, 'sensor')) {
        $parts[] = 'cam ung hong ngoai cam bien sensor tu dong bat tat';
    }

    $idxText = mb_strtolower(implode(' ', array_unique($parts)));
    $stmt->execute(['idx' => $idxText, 'id' => $r['id']]);
}

echo "Search index successfully populated for " . count($rows) . " products.\n";
