<?php
declare(strict_types=1);

/**
 * Front Controller – mọi request đều đi qua đây.
 */

define('ROOT_PATH', dirname(__DIR__));
define('PUBLIC_PATH', __DIR__);
define('APP_PATH', ROOT_PATH . '/app');
define('VIEW_PATH', APP_PATH . '/Views');
define('STORAGE_PATH', ROOT_PATH . '/storage');

// Hỗ trợ PHP built-in server: php -S localhost:8000 -t public public/index.php
if (PHP_SAPI === 'cli-server') {
    $file = __DIR__ . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    if (is_file($file)) {
        return false;
    }
}

// ---------------------------------------------------------------------
// Autoload PSR-4 đơn giản: App\ -> app/, Core\ -> core/
// ---------------------------------------------------------------------
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

// ---------------------------------------------------------------------
// Môi trường & bảo mật
// ---------------------------------------------------------------------
date_default_timezone_set(config('timezone', 'Asia/Ho_Chi_Minh'));
mb_internal_encoding('UTF-8');

$debug = (bool) config('debug', false);
ini_set('display_errors', $debug ? '1' : '0');
error_reporting(E_ALL);

$isHttps = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
$cookieLifetime = 30 * 24 * 3600; // Lưu phiên đăng nhập 30 ngày (vĩnh viễn không bị mất khi load lại)
ini_set('session.gc_maxlifetime', (string) $cookieLifetime);
session_name('PANASID');
session_set_cookie_params([
    'lifetime' => $cookieLifetime,
    'path'     => base_path() ?: '/',
    'secure'   => $isHttps,
    'httponly' => true,
    'samesite' => 'Lax',
]);
ini_set('session.use_strict_mode', '1');
session_start();

header('Content-Type: text/html; charset=UTF-8');
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: SAMEORIGIN');
header('Referrer-Policy: strict-origin-when-cross-origin');
header('Permissions-Policy: camera=(), microphone=(), geolocation=()');

use App\Controllers\AdminController;
use App\Controllers\AuthController;
use App\Controllers\CartController;
use App\Controllers\HomeController;
use App\Controllers\PaymentController;
use App\Controllers\ProductController;
use App\Controllers\QuoteController;
use Core\Router;

// ---------------------------------------------------------------------
// Định tuyến
// ---------------------------------------------------------------------
$router = new Router(base_path());

$adminAuth = [static function (): bool {
    if (empty($_SESSION['admin'])) {
        header('Location: ' . url('/admin/login'));
        return false;
    }
    return true;
}];

// Website
$router->get('/', [HomeController::class, 'index']);

$router->get('/san-pham', [ProductController::class, 'index']);
$router->get('/san-pham/{slug}', [ProductController::class, 'detail']);
$router->get('/danh-muc/{slug}', [ProductController::class, 'category']);
$router->get('/panasonic-{slug}', [ProductController::class, 'series']);   // /panasonic-moderva
$router->get('/api/tim-kiem', [ProductController::class, 'search']);        // Live search JSON
$router->post('/api/danh-gia', [ProductController::class, 'addReview']);     // Submit review JSON

// Authentication APIs (Toàn bộ API REST)
$router->post('/api/auth/login', [AuthController::class, 'login']);
$router->post('/api/auth/register', [AuthController::class, 'register']);
$router->post('/api/auth/social-login', [AuthController::class, 'socialLogin']);
$router->get('/api/auth/me', [AuthController::class, 'me']);
$router->post('/api/auth/logout', [AuthController::class, 'logout']);
$router->post('/api/auth/update-profile', [AuthController::class, 'updateProfile']);

// Authentication Web Pages & User Profile
$router->get('/dang-nhap', [AuthController::class, 'loginPage']);
$router->get('/dang-ky', [AuthController::class, 'registerPage']);
$router->get('/dang-xuat', [AuthController::class, 'logout']);
$router->get('/tai-khoan', [AuthController::class, 'profile']);

$router->get('/du-an', [HomeController::class, 'projects']);
$router->get('/du-an/{slug}', [HomeController::class, 'projectDetail']);
$router->get('/lien-he', [HomeController::class, 'contact']);
$router->post('/lien-he', [HomeController::class, 'submitContact']);

$router->get('/tai-catalogue', [QuoteController::class, 'catalogue']);
$router->get('/bao-gia-cong-trinh', [QuoteController::class, 'form']);
$router->post('/bao-gia-cong-trinh', [QuoteController::class, 'submit']);

$router->get('/gio-hang', [CartController::class, 'index']);
$router->get('/api/gio-hang', [CartController::class, 'apiGetCart']);
$router->post('/gio-hang/them', [CartController::class, 'add']);
$router->post('/gio-hang/cap-nhat', [CartController::class, 'update']);
$router->post('/gio-hang/xoa', [CartController::class, 'remove']);
$router->post('/gio-hang/dat-hang', [CartController::class, 'checkout']);
$router->get('/dat-hang-thanh-cong/{code}', [CartController::class, 'success']);
$router->get('/don-hang/xem-email/{code}', [CartController::class, 'viewOrderEmail']);

// APIs địa giới hành chính Việt Nam (Tỉnh/Thành -> Quận/Huyện -> Phường/Xã)
$router->get('/api/locations/provinces', [CartController::class, 'getProvinces']);
$router->get('/api/locations/districts', [CartController::class, 'getDistricts']);
$router->get('/api/locations/wards', [CartController::class, 'getWards']);

// APIs sổ địa chỉ giao hàng nhận hàng chuẩn Shopee / TikTok Shop
$router->get('/api/user/addresses', [CartController::class, 'getUserAddresses']);
$router->post('/api/user/addresses/add', [CartController::class, 'addUserAddress']);
$router->post('/api/user/addresses/set-default', [CartController::class, 'setDefaultAddress']);
$router->post('/api/user/addresses/delete', [CartController::class, 'deleteUserAddress']);

// API xác nhận thanh toán chuyển khoản
$router->post('/api/don-hang/xac-nhan-chuyen-khoan', [CartController::class, 'confirmPayment']);

// Cổng thanh toán VNPAY & MoMo Sandbox Return / IPN
$router->get('/thanh-toan/vnpay-return', [PaymentController::class, 'vnpayReturn']);
$router->get('/thanh-toan/momo-return', [PaymentController::class, 'momoReturn']);
$router->get('/thanh-toan/momo-simulator', [PaymentController::class, 'momoSimulator']);
$router->get('/api/payment/vnpay-ipn', [PaymentController::class, 'vnpayReturn']);
$router->post('/api/payment/momo-ipn', [PaymentController::class, 'momoReturn']);

// Quản trị
$router->get('/admin/login', [AdminController::class, 'loginForm']);
$router->post('/admin/login', [AdminController::class, 'login']);
$router->get('/admin/logout', [AdminController::class, 'logout']);

$router->get('/admin', [AdminController::class, 'dashboard'], $adminAuth);
$router->get('/admin/san-pham', [AdminController::class, 'products'], $adminAuth);
$router->get('/admin/san-pham/them', [AdminController::class, 'createProduct'], $adminAuth);
$router->post('/admin/san-pham/them', [AdminController::class, 'storeProduct'], $adminAuth);
$router->get('/admin/san-pham/sua/{id}', [AdminController::class, 'editProduct'], $adminAuth);
$router->post('/admin/san-pham/sua/{id}', [AdminController::class, 'updateProduct'], $adminAuth);
$router->post('/admin/san-pham/xoa/{id}', [AdminController::class, 'deleteProduct'], $adminAuth);
$router->get('/admin/bao-gia', [AdminController::class, 'quotes'], $adminAuth);
$router->post('/admin/bao-gia/trang-thai/{id}', [AdminController::class, 'updateQuoteStatus'], $adminAuth);
$router->get('/admin/bao-gia/tai-file/{id}', [AdminController::class, 'downloadBom'], $adminAuth);
$router->get('/admin/don-hang', [AdminController::class, 'orders'], $adminAuth);
$router->get('/admin/don-hang/{id}', [AdminController::class, 'orderDetail'], $adminAuth);
$router->post('/admin/don-hang/trang-thai/{id}', [AdminController::class, 'updateOrderStatus'], $adminAuth);

// Quản lý thành viên & Khách hàng
$router->get('/admin/thanh-vien', [AdminController::class, 'users'], $adminAuth);
$router->post('/admin/thanh-vien/trang-thai/{id}', [AdminController::class, 'toggleUserStatus'], $adminAuth);

// Quản lý dự án công trình (TP.HCM & Long An)
$router->get('/admin/du-an', [AdminController::class, 'projects'], $adminAuth);
$router->get('/admin/du-an/them', [AdminController::class, 'createProject'], $adminAuth);
$router->post('/admin/du-an/them', [AdminController::class, 'storeProject'], $adminAuth);
$router->get('/admin/du-an/sua/{id}', [AdminController::class, 'editProject'], $adminAuth);
$router->post('/admin/du-an/sua/{id}', [AdminController::class, 'updateProject'], $adminAuth);
$router->post('/admin/du-an/xoa/{id}', [AdminController::class, 'deleteProject'], $adminAuth);

// Quản lý đánh giá sản phẩm
$router->get('/admin/danh-gia', [AdminController::class, 'reviews'], $adminAuth);
$router->post('/admin/danh-gia/tra-loi/{id}', [AdminController::class, 'replyReview'], $adminAuth);
$router->post('/admin/danh-gia/trang-thai/{id}', [AdminController::class, 'toggleReviewStatus'], $adminAuth);
$router->post('/admin/danh-gia/xoa/{id}', [AdminController::class, 'deleteReview'], $adminAuth);

// Cài đặt hệ thống website & liên hệ
$router->get('/admin/cai-dat', [AdminController::class, 'settings'], $adminAuth);
$router->post('/admin/cai-dat', [AdminController::class, 'updateSettings'], $adminAuth);

// ---------------------------------------------------------------------
// Chạy ứng dụng
// ---------------------------------------------------------------------
try {
    $router->dispatch($_SERVER['REQUEST_METHOD'] ?? 'GET', $_SERVER['REQUEST_URI'] ?? '/');
} catch (Throwable $e) {
    error_log('[APP] ' . $e->getMessage() . ' @ ' . $e->getFile() . ':' . $e->getLine());
    http_response_code(500);
    if ($debug) {
        echo '<pre style="padding:20px;background:#fff1f2;color:#9f1239;font:14px/1.5 monospace;white-space:pre-wrap">';
        echo '<strong>' . e(get_class($e)) . ':</strong> ' . e($e->getMessage()) . "\n\n" . e($e->getTraceAsString());
        echo '</pre>';
    } else {
        echo '<h1 style="font-family:sans-serif;text-align:center;margin-top:15vh">Hệ thống đang bảo trì, vui lòng quay lại sau.</h1>';
    }
}
