<?php
declare(strict_types=1);

/**
 * Worker gửi email đơn hàng chạy ngầm (Asynchronous Background Email Worker)
 * Chạy độc lập, không block request của người dùng khi đặt hàng.
 * 
 * Usage: php cli/send_order_email.php <ORDER_CODE>
 */

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('Chỉ thực thi từ CLI.');
}

$orderCode = trim((string)($argv[1] ?? ''));
if (empty($orderCode)) {
    exit("Thiếu mã đơn hàng.\n");
}

define('ROOT_PATH', dirname(__DIR__));
define('APP_PATH', ROOT_PATH . '/app');
define('STORAGE_PATH', ROOT_PATH . '/storage');

spl_autoload_register(static function (string $class): void {
    $prefixes = [
        'App\\'  => APP_PATH . '/',
        'Core\\' => ROOT_PATH . '/core/',
    ];
    foreach ($prefixes as $prefix => $dir) {
        if (str_starts_with($class, $prefix)) {
            $file = $dir . str_replace('\\', '/', substr($class, strlen($prefix))) . '.php';
            if (is_file($file)) {
                require $file;
            }
            return;
        }
    }
});

require ROOT_PATH . '/core/helpers.php';
date_default_timezone_set(config('timezone', 'Asia/Ho_Chi_Minh'));
mb_internal_encoding('UTF-8');

try {
    $orderModel = new \App\Models\Order();
    $order = $orderModel->findByCode($orderCode);
    if (!$order) {
        error_log("[CLI Email Worker] Không tìm thấy đơn hàng #{$orderCode}");
        exit(1);
    }

    $items = $orderModel->items((int)$order['id']);
    $mailer = new \App\Services\Mailer();
    $result = $mailer->sendOrderConfirmation($order, $items);

    $logDir = STORAGE_PATH . '/logs';
    if (!is_dir($logDir)) {
        @mkdir($logDir, 0777, true);
    }
    $logLine = sprintf(
        "[%s] Order #%s | Customer: %s (%s) | Admin: %s | Result: %s\n",
        date('Y-m-d H:i:s'),
        $orderCode,
        $order['email'] ?? 'none',
        $result['sent_customer'] ? 'Sent' : 'Skipped/Fail',
        $result['sent_admin'] ? 'Sent' : 'Skipped/Fail',
        $result['error'] ?: 'OK'
    );
    @file_put_contents($logDir . '/mail.log', $logLine, FILE_APPEND);
    exit(0);
} catch (\Throwable $e) {
    error_log("[CLI Email Worker Error] #{$orderCode}: " . $e->getMessage());
    exit(1);
}
