<?php
$pdo = new PDO('mysql:host=localhost;dbname=panasonic_shop;charset=utf8mb4', 'root', '');
$stmt = $pdo->query('SELECT description FROM products WHERE id = 21');
echo $stmt->fetchColumn();
