<?php
declare(strict_types=1);

/**
 * Các hàm tiện ích toàn cục dùng trong Controller & View.
 */

function config(string $key, mixed $default = null): mixed
{
    static $config = null;
    $config ??= require ROOT_PATH . '/config/app.php';
    return $config[$key] ?? $default;
}

/**
 * Tự nhận diện base URL: hoạt động khi đặt project trong htdocs/panasonic
 * hoặc khi DocumentRoot trỏ thẳng vào /public.
 */
function base_path(): string
{
    static $base = null;
    if ($base !== null) {
        return $base;
    }
    $configured = (string) config('base_url', '');
    if ($configured !== '') {
        return $base = rtrim($configured, '/');
    }
    $script = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
    $dir = rtrim(dirname($script), '/');
    // Truy cập qua .htaccess gốc -> /panasonic/public  => bỏ /public để URL sạch
    if (str_ends_with($dir, '/public')) {
        $dir = substr($dir, 0, -7);
    }
    return $base = ($dir === '.' ? '' : $dir);
}

function url(string $path = ''): string
{
    $path = '/' . ltrim($path, '/');
    return base_path() . ($path === '/' ? '/' : $path);
}

function full_url(string $path = ''): string
{
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    return $scheme . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost') . url($path);
}

/**
 * URL tài nguyên tĩnh trong /public kèm cache-busting theo thời gian sửa file.
 */
function asset(string $path): string
{
    $path = ltrim($path, '/');
    $file = PUBLIC_PATH . '/' . $path;
    $ver = is_file($file) ? '?v=' . filemtime($file) : '';
    return base_path() . '/' . $path . $ver;
}

/**
 * Ảnh sản phẩm (fallback ảnh mặc định nếu thiếu).
 */
function media(?string $path): string
{
    if (!$path) {
        return asset('uploads/products/placeholder.svg');
    }
    if (preg_match('#^https?://#', $path)) {
        return $path;
    }
    return is_file(PUBLIC_PATH . '/' . ltrim($path, '/'))
        ? asset($path)
        : asset('uploads/products/placeholder.svg');
}

function e(mixed $value): string
{
    return htmlspecialchars((string) ($value ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function money(float|int|string|null $amount, bool $withUnit = true): string
{
    $formatted = number_format((float) $amount, 0, ',', '.');
    return $withUnit ? $formatted . '₫' : $formatted;
}

function csrf_token(): string
{
    if (empty($_SESSION['_csrf'])) {
        $_SESSION['_csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['_csrf'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="_token" value="' . e(csrf_token()) . '">';
}

/**
 * Flash message: set khi có $message, get (và xoá) khi không.
 */
function flash(string $key, ?string $message = null): ?string
{
    if ($message !== null) {
        $_SESSION['_flash'][$key] = $message;
        return null;
    }
    $value = $_SESSION['_flash'][$key] ?? null;
    unset($_SESSION['_flash'][$key]);
    return $value;
}

function old(string $key, string $default = ''): string
{
    return e($_SESSION['_old'][$key] ?? $default);
}

function set_old(array $data): void
{
    $_SESSION['_old'] = $data;
}

function clear_old(): void
{
    unset($_SESSION['_old']);
}

/**
 * Tạo slug tiếng Việt không dấu.
 */
function str_slug(string $text): string
{
    $map = [
        'a' => 'á|à|ả|ã|ạ|ă|ắ|ằ|ẳ|ẵ|ặ|â|ấ|ầ|ẩ|ẫ|ậ',
        'd' => 'đ',
        'e' => 'é|è|ẻ|ẽ|ẹ|ê|ế|ề|ể|ễ|ệ',
        'i' => 'í|ì|ỉ|ĩ|ị',
        'o' => 'ó|ò|ỏ|õ|ọ|ô|ố|ồ|ổ|ỗ|ộ|ơ|ớ|ờ|ở|ỡ|ợ',
        'u' => 'ú|ù|ủ|ũ|ụ|ư|ứ|ừ|ử|ữ|ự',
        'y' => 'ý|ỳ|ỷ|ỹ|ỵ',
    ];
    $text = mb_strtolower($text, 'UTF-8');
    foreach ($map as $ascii => $pattern) {
        $text = preg_replace('/(' . $pattern . ')/u', $ascii, $text);
    }
    $text = preg_replace('/[^a-z0-9]+/', '-', $text);
    return trim($text, '-');
}

function is_active(string $path): string
{
    $current = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
    $target  = url($path);
    if ($path === '/') {
        return rtrim($current, '/') === rtrim($target, '/') ? 'is-active' : '';
    }
    return str_starts_with($current, $target) ? 'is-active' : '';
}

function cart_count(): int
{
    return array_sum(array_map('intval', $_SESSION['cart'] ?? []));
}

function admin_user(): ?array
{
    return $_SESSION['admin'] ?? null;
}

function auth_user(): ?array
{
    if (!empty($_SESSION['user'])) {
        return $_SESSION['user'];
    }

    // Tự động khôi phục phiên đăng nhập từ Remember Cookie nếu session hết hạn
    if (!empty($_COOKIE['panas_remember'])) {
        $token = (string) $_COOKIE['panas_remember'];
        try {
            $user = (new \App\Models\User())->findBy('remember_token', $token);
            if ($user && ($user['status'] ?? 'active') === 'active') {
                $_SESSION['user'] = [
                    'id'       => (int) $user['id'],
                    'name'     => $user['name'],
                    'email'    => $user['email'],
                    'phone'    => $user['phone'] ?? '',
                    'avatar'   => $user['avatar'] ?? null,
                    'role'     => $user['role'] ?? 'customer',
                    'provider' => $user['provider'] ?? 'local',
                ];
                return $_SESSION['user'];
            }
        } catch (\Throwable $e) {}
    }

    return null;
}

function is_logged_in(): bool
{
    return !empty($_SESSION['user']['id']);
}

/**
 * Query string giữ nguyên bộ lọc hiện tại, ghi đè một số khoá.
 */
function query_with(array $override): string
{
    $query = array_merge($_GET, $override);
    $query = array_filter($query, fn($v) => $v !== null && $v !== '');
    return $query ? '?' . http_build_query($query) : '';
}

/**
 * Bộ icon SVG inline (stroke) – không phụ thuộc thư viện ngoài.
 */
function icon(string $name, string $class = ''): string
{
    static $paths = [
        'search'     => '<circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3"/>',
        'cart'       => '<circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.7 13.4a2 2 0 0 0 2 1.6h9.7a2 2 0 0 0 2-1.6L23 6H6"/>',
        'phone'      => '<path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3.1 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.1 4.2 2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7c.1.9.4 1.8.7 2.7a2 2 0 0 1-.5 2.1L8 9.8a16 16 0 0 0 6 6l1.3-1.3a2 2 0 0 1 2.1-.4c.9.3 1.8.6 2.7.7a2 2 0 0 1 1.7 2z"/>',
        'download'   => '<path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><path d="m7 10 5 5 5-5"/><path d="M12 15V3"/>',
        'upload'     => '<path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><path d="m17 8-5-5-5 5"/><path d="M12 3v12"/>',
        'file'       => '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/>',
        'file-text'  => '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6M16 13H8M16 17H8M10 9H8"/>',
        'menu'       => '<path d="M3 6h18M3 12h18M3 18h18"/>',
        'x'          => '<path d="M18 6 6 18M6 6l12 12"/>',
        'chevron-right' => '<path d="m9 18 6-6-6-6"/>',
        'chevron-left'  => '<path d="m15 18-6-6 6-6"/>',
        'chevron-down'  => '<path d="m6 9 6 6 6-6"/>',
        'arrow-right'=> '<path d="M5 12h14M12 5l7 7-7 7"/>',
        'check'      => '<path d="M20 6 9 17l-5-5"/>',
        'check-circle' => '<path d="M22 11.1V12a10 10 0 1 1-5.9-9.1"/><path d="M22 4 12 14l-3-3"/>',
        'shield'     => '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="m9 12 2 2 4-4"/>',
        'zap'        => '<path d="M13 2 3 14h9l-1 8 10-12h-9z"/>',
        'switch'     => '<rect x="4" y="2" width="16" height="20" rx="3"/><rect x="8" y="6" width="8" height="12" rx="1.5"/><path d="M12 9v2"/>',
        'breaker'    => '<rect x="6" y="2" width="12" height="20" rx="2"/><path d="M6 7h12M6 17h12"/><rect x="10" y="9.5" width="4" height="5" rx="1"/>',
        'bulb'       => '<path d="M9 18h6M10 22h4"/><path d="M12 2a7 7 0 0 0-4 12.7c.6.5 1 1.3 1 2.1V17h6v-.2c0-.8.4-1.6 1-2.1A7 7 0 0 0 12 2z"/>',
        'fan'        => '<circle cx="12" cy="12" r="2"/><path d="M12 10c0-4 1-7 4-7s3 4 0 6"/><path d="M14 12c4 0 7 1 7 4s-4 3-6 0"/><path d="M12 14c0 4-1 7-4 7s-3-4 0-6"/><path d="M10 12c-4 0-7-1-7-4s4-3 6 0"/>',
        'plug'       => '<path d="M12 22v-5M9 8V2M15 8V2"/><path d="M18 8v4a6 6 0 0 1-12 0V8z"/>',
        'truck'      => '<path d="M1 3h15v13H1zM16 8h4l3 3v5h-7z"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/>',
        'award'      => '<circle cx="12" cy="8" r="6"/><path d="M15.5 13 17 22l-5-3-5 3 1.5-9"/>',
        'mail'       => '<rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 6-10 7L2 6"/>',
        'map-pin'    => '<path d="M20 10c0 6-8 12-8 12S4 16 4 10a8 8 0 0 1 16 0z"/><circle cx="12" cy="10" r="3"/>',
        'clock'      => '<circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/>',
        'user'       => '<path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>',
        'users'      => '<path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.9M16 3.1a4 4 0 0 1 0 7.8"/>',
        'filter'     => '<path d="M22 3H2l8 9.5V19l4 2v-8.5z"/>',
        'grid'       => '<rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/>',
        'trash'      => '<path d="M3 6h18M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6M10 11v6M14 11v6M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/>',
        'plus'       => '<path d="M12 5v14M5 12h14"/>',
        'minus'      => '<path d="M5 12h14"/>',
        'box'        => '<path d="M21 16V8a2 2 0 0 0-1-1.7l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.7l7 4a2 2 0 0 0 2 0l7-4a2 2 0 0 0 1-1.7z"/><path d="m3.3 7 8.7 5 8.7-5M12 22V12"/>',
        'building'   => '<rect x="4" y="2" width="16" height="20" rx="2"/><path d="M9 22v-4h6v4M8 6h.01M16 6h.01M12 6h.01M12 10h.01M12 14h.01M16 10h.01M16 14h.01M8 10h.01M8 14h.01"/>',
        'calculator' => '<rect x="4" y="2" width="16" height="20" rx="2"/><path d="M8 6h8M8 10h.01M12 10h.01M16 10h.01M8 14h.01M12 14h.01M16 14h.01M8 18h.01M12 18h.01M16 18h.01"/>',
        'eye'        => '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>',
        'edit'       => '<path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4z"/>',
        'logout'     => '<path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4M16 17l5-5-5-5M21 12H9"/>',
        'home'       => '<path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><path d="M9 22V12h6v10"/>',
        'layers'     => '<path d="m12 2 10 5-10 5L2 7z"/><path d="m2 17 10 5 10-5M2 12l10 5 10-5"/>',
        'alert'      => '<circle cx="12" cy="12" r="10"/><path d="M12 8v4M12 16h.01"/>',
        'tag'        => '<path d="M20.6 13.4 13.4 20.6a2 2 0 0 1-2.8 0L2 12V2h10l8.6 8.6a2 2 0 0 1 0 2.8z"/><path d="M7 7h.01"/>',
        'star'       => '<polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>',
        'thumbs-up'  => '<path d="M14 9V5a3 3 0 0 0-3-3l-4 9v11h11.28a2 2 0 0 0 2-1.7l1.38-9a2 2 0 0 0-2-2.3zM7 22H4a2 2 0 0 1-2-2v-7a2 2 0 0 1 2-2h3"/>',
        'settings'   => '<circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.7 1.7 0 0 0 .3 1.8l.1.1a2 2 0 1 1-2.8 2.8l-.1-.1a1.7 1.7 0 0 0-1.8-.3 1.7 1.7 0 0 0-1 1.5V21a2 2 0 1 1-4 0v-.1a1.7 1.7 0 0 0-1.1-1.5 1.7 1.7 0 0 0-1.8.3l-.1.1a2 2 0 1 1-2.8-2.8l.1-.1a1.7 1.7 0 0 0 .3-1.8 1.7 1.7 0 0 0-1.5-1H3a2 2 0 1 1 0-4h.1a1.7 1.7 0 0 0 1.5-1.1 1.7 1.7 0 0 0-.3-1.8l-.1-.1a2 2 0 1 1 2.8-2.8l.1.1a1.7 1.7 0 0 0 1.8.3H9a1.7 1.7 0 0 0 1-1.5V3a2 2 0 1 1 4 0v.1a1.7 1.7 0 0 0 1 1.5 1.7 1.7 0 0 0 1.8-.3l.1-.1a2 2 0 1 1 2.8 2.8l-.1.1a1.7 1.7 0 0 0-.3 1.8V9a1.7 1.7 0 0 0 1.5 1H21a2 2 0 1 1 0 4h-.1a1.7 1.7 0 0 0-1.5 1z"/>',
        'chat'       => '<path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>',
        'leaf'       => '<path d="M11 20A7 7 0 0 1 9.8 6.1C15.5 5 17 4.5 19 2c1 2 2 4.2 2 8 0 5.5-4.8 10-10 10z"/><path d="M2 21c0-3 1.9-5.4 5.1-6"/>',
    ];

    $svg = $paths[$name] ?? $paths['box'];
    return '<svg class="icon ' . e($class) . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' . $svg . '</svg>';
}

/**
 * Định dạng bài viết mô tả & ứng dụng sản phẩm trực quan, thoáng đãng, dễ đọc
 */
function format_product_description(?string $raw, string $productName = ''): string {
    if (empty($raw)) return '';

    $text = html_entity_decode($raw, ENT_QUOTES | ENT_HTML5, 'UTF-8');

    // Loại bỏ boilerplate lặp lại về kho hàng/địa chỉ/hotline
    $text = preg_replace('/Địa chỉ mua hàng\s+KHO PHÂN PHỐI.*$/us', '', $text);
    $text = preg_replace('/KHO PHÂN PHỐI THIẾT BỊ ĐIỆN PANASONIC.*$/us', '', $text);
    $text = preg_replace('/✉\s*Yêu cầu báo giá.*$/us', '', $text);
    $text = preg_replace('/Phone:\s*0798.*$/us', '', $text);
    $text = preg_replace('/Website:\s*https?:\/\/[^\s]+/us', '', $text);

    // Nếu văn bản đã có cấu trúc HTML chuẩn (p, h3, ul)
    if (substr_count($text, '<p>') >= 2 || substr_count($text, '<h3>') >= 1 || substr_count($text, '<ul>') >= 1) {
        $text = preg_replace('/<p>\s*(&nbsp;|\s)*<\/p>/i', '', $text);
        return trim($text);
    }

    $text = preg_replace('/[ \t]+/u', ' ', $text);
    $text = trim($text);

    $headers = [
        'Đặc điểm nổi bật',
        'Ưu điểm nổi bật',
        'Ưu điểm vượt trội',
        'Ánh sáng chất lượng cao',
        'Tác hại của ánh sáng xanh',
        'Với chip LED RG0 – Loại bỏ ánh sáng xanh gây hại',
        'Với chip LED RG0',
        'Cường độ sáng tiêu chuẩn Nhật Bản JIS AA',
        'Phù hợp và tối ưu cho mắt',
        'Sáng dịu êm, không chói mắt',
        'Ánh sáng ổn định không nhấp nháy',
        'CRI90 hiển thị màu sắc trung thực',
        'CRI 95 hiển thị màu sắc trung thực',
        'Tính năng sản phẩm',
        'Đổi độ sáng linh hoạt 0%-100%',
        'Đổi độ sáng linh hoạt',
        'Điều khiển chạm',
        'Điều khiển cảm ứng',
        'Chế độ tắt mỏng thông minh',
        'Chế độ tắt thông minh',
        'Chế độ tắt sau 30 giây',
        'Chế độ 45 phút',
        'Thiết kế sản phẩm',
        'Trục đèn xoay 180 độ',
        'Trục đèn gập/ xoay thông minh',
        'Thân đèn bằng nhôm bền bỉ',
        'Thông số kĩ thuật',
        'Thông số kỹ thuật',
        'Chất liệu cao cấp',
        'Tiếp điểm bền bỉ',
        'Thao tác nhẹ nhàng',
        'An toàn tuyệt đối',
        'Thiết kế viền bạc sang trọng',
        'Tích hợp 3 màu ánh sáng',
        'Công suất mạnh mẽ',
        'Bản vẽ kỹ thuật'
    ];

    usort($headers, fn($a, $b) => mb_strlen($b) - mb_strlen($a));

    $marked = $text;
    foreach ($headers as $h) {
        $pattern = '/(?<=\s|^)(' . preg_quote($h, '/') . ')(?=[:–—\s]|$)/u';
        $marked = preg_replace($pattern, "\n###SECTION### $1\n", $marked);
    }

    $lines = explode("\n", $marked);
    $html = '<div class="product-desc-structured">';
    $inList = false;
    $isFirst = true;
    $prevWasHeading = false;

    foreach ($lines as $line) {
        $line = trim($line);
        if (!$line) continue;

        if (str_starts_with($line, '###SECTION###')) {
            if ($inList) {
                $html .= "</ul>\n";
                $inList = false;
            }
            $title = trim(str_replace('###SECTION###', '', $line));
            if (mb_strlen($title) >= 3) {
                if ($prevWasHeading) {
                    $html = preg_replace('/<\/h4>\n$/', ' – ' . htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . "</h4>\n", $html);
                } else {
                    $html .= '<h4 class="desc-feature-title"><span class="feature-badge">✓</span> ' . htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . "</h4>\n";
                    $prevWasHeading = true;
                }
            }
            continue;
        }

        $prevWasHeading = false;

        if ($isFirst) {
            $html .= '<p class="desc-lead-text">' . htmlspecialchars($line, ENT_QUOTES, 'UTF-8') . "</p>\n";
            $isFirst = false;
            continue;
        }

        $sentences = preg_split('/(?<=[.!?])\s+/u', $line);
        if (count($sentences) >= 2 && (str_contains($line, 'tiêu chuẩn') || str_contains($line, 'CRI') || str_contains($line, 'chế độ') || str_contains($line, 'công suất') || str_contains($line, 'tiết kiệm') || str_contains($line, 'độ bền') || str_contains($line, 'LED') || str_contains($line, 'khả năng'))) {
            if (!$inList) {
                $html .= "<ul class=\"desc-feature-list\">\n";
                $inList = true;
            }
            foreach ($sentences as $s) {
                $s = trim($s);
                if (mb_strlen($s) > 5) {
                    $html .= '  <li>' . htmlspecialchars($s, ENT_QUOTES, 'UTF-8') . "</li>\n";
                }
            }
        } else {
            if ($inList) {
                $html .= "</ul>\n";
                $inList = false;
            }
            $html .= '<p class="desc-body-text">' . htmlspecialchars($line, ENT_QUOTES, 'UTF-8') . "</p>\n";
        }
    }

    if ($inList) {
        $html .= "</ul>\n";
    }

    $html .= '</div>';
    return trim($html);
}

/**
 * Chuyển đổi chuỗi tiếng Việt có dấu thành không dấu chuẩn (hỗ trợ cả đ/Đ)
 */
function strip_vn_accents(string $str): string
{
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

