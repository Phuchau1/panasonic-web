<?php
$pdo = new PDO('mysql:host=localhost;dbname=panasonic_shop;charset=utf8mb4', 'root', '');
echo $pdo->query('SELECT slug FROM products WHERE id = 132')->fetchColumn();
