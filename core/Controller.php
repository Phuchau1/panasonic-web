<?php
declare(strict_types=1);

namespace Core;

/**
 * Base Controller: render view, JSON response, redirect, CSRF, input helpers.
 */
abstract class Controller
{
    /**
     * Render view kèm layout.
     *
     * @param string      $view   Đường dẫn view tương đối trong app/Views (không kèm .php)
     * @param array       $data   Biến truyền vào view
     * @param string|null $layout 'site' | 'admin' | null (không layout)
     */
    public function render(string $view, array $data = [], ?string $layout = 'site'): void
    {
        $viewFile = VIEW_PATH . '/' . $view . '.php';
        if (!is_file($viewFile)) {
            throw new \RuntimeException("View [{$view}] không tồn tại");
        }

        $data['title'] ??= config('name');
        $data['metaDescription'] ??= config('tagline');

        extract($data, EXTR_SKIP);

        ob_start();
        require $viewFile;
        $content = ob_get_clean();

        if ($layout === null) {
            echo $content;
            return;
        }

        if ($layout === 'admin') {
            require VIEW_PATH . '/admin/layout/header.php';
            echo $content;
            require VIEW_PATH . '/admin/layout/footer.php';
            return;
        }

        require VIEW_PATH . '/layouts/header.php';
        require VIEW_PATH . '/layouts/navbar.php';
        echo '<main id="main-content" class="site-main">' . $content . '</main>';
        require VIEW_PATH . '/layouts/footer.php';
    }

    protected function json(array $payload, int $status = 200): never
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: no-store');
        echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }

    protected function redirect(string $path, int $status = 302): never
    {
        $location = preg_match('#^https?://#', $path) ? $path : url($path);
        header('Location: ' . $location, true, $status);
        exit;
    }

    protected function back(): never
    {
        $ref = $_SERVER['HTTP_REFERER'] ?? '';
        $host = $_SERVER['HTTP_HOST'] ?? '';
        // Chỉ quay lại trong cùng domain – chống open redirect
        if ($ref !== '' && parse_url($ref, PHP_URL_HOST) === parse_url('http://' . $host, PHP_URL_HOST)) {
            header('Location: ' . $ref);
            exit;
        }
        $this->redirect('/');
    }

    protected function isAjax(): bool
    {
        return strtolower($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'xmlhttprequest'
            || str_contains($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json');
    }

    protected function input(string $key, string $default = ''): string
    {
        $value = $_POST[$key] ?? $_GET[$key] ?? $default;
        return is_string($value) ? trim($value) : $default;
    }

    protected function inputInt(string $key, int $default = 0): int
    {
        $value = $_POST[$key] ?? $_GET[$key] ?? null;
        return filter_var($value, FILTER_VALIDATE_INT) !== false ? (int) $value : $default;
    }

    /**
     * Lấy dữ liệu input bất kể gửi qua POST Form-data hay JSON Payload
     */
    protected function getJsonInput(): array
    {
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
        if (str_contains(strtolower($contentType), 'application/json')) {
            $raw = file_get_contents('php://input');
            $data = json_decode($raw, true);
            return is_array($data) ? $data : [];
        }
        return $_POST;
    }

    /**
     * Xác thực CSRF token cho mọi request POST.
     */
    protected function verifyCsrf(): void
    {
        $token = $_POST['_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
        if (!is_string($token) || !hash_equals(csrf_token(), $token)) {
            if ($this->isAjax()) {
                $this->json(['success' => false, 'message' => 'Phiên làm việc đã hết hạn, vui lòng tải lại trang.'], 403);
            }
            http_response_code(403);
            flash('error', 'Phiên làm việc đã hết hạn, vui lòng thử lại.');
            $this->back();
        }
    }

    protected function abort(int $code = 404, string $message = ''): never
    {
        http_response_code($code);
        $this->render('errors/404', [
            'title'   => $code === 404 ? 'Không tìm thấy trang' : 'Lỗi ' . $code,
            'message' => $message,
        ]);
        exit;
    }
}
