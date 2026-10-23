<?php
define('ROOT_PATH', dirname(__DIR__));
require_once ROOT_PATH . '/core/Database.php';

$pdo = \Core\Database::getInstance()->pdo();

$sql = "
CREATE TABLE IF NOT EXISTS `users` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(150) NOT NULL,
    `email` VARCHAR(150) NOT NULL UNIQUE,
    `phone` VARCHAR(20) NULL,
    `password` VARCHAR(255) NULL,
    `provider` VARCHAR(50) DEFAULT 'local',
    `provider_id` VARCHAR(191) NULL,
    `avatar` VARCHAR(255) NULL,
    `address` VARCHAR(255) NULL,
    `role` ENUM('customer', 'contractor', 'admin') DEFAULT 'customer',
    `status` ENUM('active', 'inactive', 'banned') DEFAULT 'active',
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_provider` (`provider`, `provider_id`),
    INDEX `idx_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
";

$pdo->exec($sql);
echo "Table `users` created or already exists.\n";

// Add user_id to orders table if not exists
try {
    $colCheck = $pdo->query("SHOW COLUMNS FROM `orders` LIKE 'user_id'")->fetch();
    if (!$colCheck) {
        $pdo->exec("ALTER TABLE `orders` ADD COLUMN `user_id` INT UNSIGNED NULL AFTER `id`, ADD INDEX `idx_orders_user` (`user_id`)");
        echo "Added user_id column to orders table.\n";
    } else {
        echo "Column user_id already exists in orders table.\n";
    }
} catch (Exception $e) {
    echo "Notice: " . $e->getMessage() . "\n";
}

$cols = $pdo->query("DESCRIBE users")->fetchAll(PDO::FETCH_ASSOC);
print_r($cols);
