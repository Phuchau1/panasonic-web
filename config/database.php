<?php
/**
 * Cấu hình kết nối cơ sở dữ liệu.
 * Có thể ghi đè bằng biến môi trường (DB_HOST, DB_NAME, DB_USER, DB_PASS) khi deploy.
 */
return [
    'host'     => getenv('DB_HOST') ?: '127.0.0.1',
    'port'     => getenv('DB_PORT') ?: '3306',
    'database' => getenv('DB_NAME') ?: 'panasonic_shop',
    'username' => getenv('DB_USER') ?: 'root',
    'password' => getenv('DB_PASS') !== false ? getenv('DB_PASS') : '',
    'charset'  => 'utf8mb4',
    'collation'=> 'utf8mb4_unicode_ci',
];
