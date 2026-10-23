<?php
$pdo = new PDO('mysql:host=localhost;dbname=panasonic_shop;charset=utf8mb4', 'root', '');
$stmt = $pdo->query('SELECT id, name, description FROM products WHERE description IS NOT NULL AND description != "" LIMIT 5');
while ($r = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo "=== ID: " . $r['id'] . " | " . $r['name'] . " ===\n";
    echo substr($r['description'], 0, 250) . "...\n\n";
}
