<?php
declare(strict_types=1);

namespace Core;

/**
 * Router hỗ trợ:
 *  - GET / POST
 *  - Tham số động {slug}, {id} (id chỉ nhận số), tiền tố tĩnh: /panasonic-{slug}
 *  - Middleware dạng callable trước khi gọi controller
 */
final class Router
{
    /** @var array<string, array<int, array{regex:string, params:array, handler:array|callable, middleware:array}>> */
    private array $routes = ['GET' => [], 'POST' => []];

    private string $basePath;

    public function __construct(string $basePath = '')
    {
        $this->basePath = rtrim($basePath, '/');
    }

    public function get(string $pattern, array|callable $handler, array $middleware = []): self
    {
        return $this->add('GET', $pattern, $handler, $middleware);
    }

    public function post(string $pattern, array|callable $handler, array $middleware = []): self
    {
        return $this->add('POST', $pattern, $handler, $middleware);
    }

    public function add(string $method, string $pattern, array|callable $handler, array $middleware = []): self
    {
        [$regex, $params] = $this->compile($pattern);
        $this->routes[strtoupper($method)][] = [
            'regex'      => $regex,
            'params'     => $params,
            'handler'    => $handler,
            'middleware' => $middleware,
        ];
        return $this;
    }

    /**
     * Biên dịch pattern thành regex.
     */
    private function compile(string $pattern): array
    {
        $pattern = '/' . trim($pattern, '/');
        $params  = [];

        $regex   = '';
        $offset  = 0;

        preg_match_all('/\{([a-zA-Z_][a-zA-Z0-9_]*)\}/', $pattern, $found, PREG_OFFSET_CAPTURE | PREG_SET_ORDER);

        foreach ($found as $match) {
            [$placeholder, $position] = $match[0];
            $name = $match[1][0];

            // Phần tĩnh trước placeholder được escape an toàn
            $regex   .= preg_quote(substr($pattern, $offset, $position - $offset), '#');
            $params[] = $name;
            $regex   .= match ($name) {
                'id', 'page' => '(\d+)',
                'code'       => '([A-Za-z0-9\-]+)',
                default      => '([a-z0-9\-]+)',
            };
            $offset = $position + strlen($placeholder);
        }
        $regex .= preg_quote(substr($pattern, $offset), '#');

        return ['#^' . $regex . '$#u', $params];
    }

    public function dispatch(string $method, string $uri): void
    {
        $path = parse_url($uri, PHP_URL_PATH) ?: '/';
        $path = rawurldecode($path);

        if ($this->basePath !== '' && str_starts_with($path, $this->basePath)) {
            $path = substr($path, strlen($this->basePath));
        }
        // Khi truy cập trực tiếp kèm /public: /panasonic/public/san-pham
        if (str_starts_with($path, '/public')) {
            $path = substr($path, 7);
        }
        // Khi chạy không có mod_rewrite: /index.php/san-pham
        if (str_starts_with($path, '/index.php')) {
            $path = substr($path, 10);
        }
        $path = '/' . trim($path, '/');

        $method = strtoupper($method);
        if ($method === 'HEAD') {
            $method = 'GET';
        }

        foreach ($this->routes[$method] ?? [] as $route) {
            if (!preg_match($route['regex'], $path, $matches)) {
                continue;
            }
            array_shift($matches);
            $args = array_combine($route['params'], $matches) ?: [];

            foreach ($route['middleware'] as $mw) {
                if ($mw() === false) {
                    return;
                }
            }

            $this->invoke($route['handler'], $args);
            return;
        }

        // Có route khác method -> 405
        $other = $method === 'GET' ? 'POST' : 'GET';
        foreach ($this->routes[$other] as $route) {
            if (preg_match($route['regex'], $path)) {
                http_response_code(405);
                echo 'Method Not Allowed';
                return;
            }
        }

        $this->notFound();
    }

    private function invoke(array|callable $handler, array $args): void
    {
        $castedArgs = array_map(static function (mixed $val): mixed {
            return (is_string($val) && ctype_digit($val)) ? (int) $val : $val;
        }, array_values($args));

        if (is_callable($handler) && !is_array($handler)) {
            $handler(...$castedArgs);
            return;
        }

        [$class, $action] = $handler;
        if (!class_exists($class) || !method_exists($class, $action)) {
            throw new \RuntimeException("Handler {$class}::{$action} không tồn tại");
        }

        $controller = new $class();
        $controller->$action(...$castedArgs);
    }

    public function notFound(): void
    {
        http_response_code(404);
        (new class extends Controller {})->render('errors/404', ['title' => 'Không tìm thấy trang']);
    }
}
