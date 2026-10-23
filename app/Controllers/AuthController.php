<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Models\User;
use Core\Controller;

class AuthController extends Controller
{
    private User $users;

    public function __construct()
    {
        $this->users = new User();
    }

    private function isApiRequest(): bool
    {
        return $this->isAjax()
            || str_contains(strtolower($_SERVER['HTTP_ACCEPT'] ?? ''), 'application/json')
            || str_contains(strtolower($_SERVER['CONTENT_TYPE'] ?? ''), 'application/json');
    }

    /**
     * API & Web: Đăng nhập bằng Email/SĐT và Mật khẩu
     * POST /api/auth/login hoặc POST /dang-nhap
     */
    public function login(): void
    {
        $input = $this->getJsonInput();
        $identifier = trim((string)($input['email'] ?? $input['identifier'] ?? ''));
        $password = (string)($input['password'] ?? '');
        $isApi = $this->isApiRequest();

        if (empty($identifier) || empty($password)) {
            if (!$isApi) {
                flash('error', 'Vui lòng nhập đầy đủ Email/Số điện thoại và Mật khẩu.');
                $this->redirect('/dang-nhap');
            }
            $this->json([
                'success' => false,
                'message' => 'Vui lòng nhập đầy đủ Email/Số điện thoại và Mật khẩu.'
            ], 422);
        }

        $user = $this->users->attempt($identifier, $password);

        if (!$user) {
            if (!$isApi) {
                flash('error', 'Thông tin đăng nhập hoặc mật khẩu không chính xác.');
                $this->redirect('/dang-nhap');
            }
            $this->json([
                'success' => false,
                'message' => 'Thông tin đăng nhập hoặc mật khẩu không chính xác.'
            ], 401);
        }

        // Tạo phiên đăng nhập
        $_SESSION['user'] = [
            'id'       => (int) $user['id'],
            'name'     => $user['name'],
            'email'    => $user['email'],
            'phone'    => $user['phone'] ?? '',
            'avatar'   => $user['avatar'] ?? null,
            'role'     => $user['role'] ?? 'customer',
            'provider' => $user['provider'] ?? 'local',
        ];

        // Tạo Remember Token vĩnh viễn (30 ngày)
        $rememberToken = bin2hex(random_bytes(32));
        $this->users->update((int) $user['id'], ['remember_token' => $rememberToken]);
        $base = base_path() ?: '/';
        setcookie('panas_remember', $rememberToken, time() + (30 * 24 * 3600), $base, '', false, true);

        // Khôi phục giỏ hàng đã lưu trước đó của user
        if (!empty($user['cart_data'])) {
            $savedCart = json_decode((string)$user['cart_data'], true);
            if (is_array($savedCart) && !empty($savedCart)) {
                $_SESSION['cart'] = array_replace((array)($_SESSION['cart'] ?? []), $savedCart);
            }
        }

        $redirect = !empty($input['redirect']) ? (string)$input['redirect'] : url('/');

        if (!$isApi) {
            flash('success', 'Đăng nhập thành công! Chào mừng bạn quay lại.');
            $this->redirect($redirect ?: '/');
        }

        $this->json([
            'success'  => true,
            'message'  => 'Đăng nhập thành công! Chào mừng bạn quay lại.',
            'user'     => $_SESSION['user'],
            'redirect' => $redirect
        ]);
    }

    /**
     * API & Web: Đăng ký tài khoản mới
     * POST /api/auth/register hoặc POST /dang-ky
     */
    public function register(): void
    {
        $input = $this->getJsonInput();
        $name = trim((string)($input['name'] ?? ''));
        $email = trim((string)($input['email'] ?? ''));
        $phone = trim((string)($input['phone'] ?? ''));
        $password = (string)($input['password'] ?? '');
        $passwordConfirm = (string)($input['password_confirmation'] ?? $input['passwordConfirm'] ?? '');
        $isApi = $this->isApiRequest();

        if (mb_strlen($name) < 2) {
            if (!$isApi) {
                flash('error', 'Họ và tên phải có ít nhất 2 ký tự.');
                $this->redirect('/dang-ky');
            }
            $this->json([
                'success' => false,
                'message' => 'Họ và tên phải có ít nhất 2 ký tự.'
            ], 422);
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            if (!$isApi) {
                flash('error', 'Địa chỉ email không đúng định dạng.');
                $this->redirect('/dang-ky');
            }
            $this->json([
                'success' => false,
                'message' => 'Địa chỉ email không đúng định dạng.'
            ], 422);
        }

        if (strlen($password) < 6) {
            if (!$isApi) {
                flash('error', 'Mật khẩu phải có độ dài tối thiểu 6 ký tự.');
                $this->redirect('/dang-ky');
            }
            $this->json([
                'success' => false,
                'message' => 'Mật khẩu phải có độ dài tối thiểu 6 ký tự.'
            ], 422);
        }

        if ($passwordConfirm !== '' && $password !== $passwordConfirm) {
            if (!$isApi) {
                flash('error', 'Xác nhận mật khẩu không trùng khớp.');
                $this->redirect('/dang-ky');
            }
            $this->json([
                'success' => false,
                'message' => 'Xác nhận mật khẩu không trùng khớp.'
            ], 422);
        }

        try {
            $user = $this->users->register([
                'name'     => $name,
                'email'    => $email,
                'phone'    => $phone,
                'password' => $password,
            ]);

            // Tự động đăng nhập sau khi đăng ký
            $_SESSION['user'] = [
                'id'       => (int) $user['id'],
                'name'     => $user['name'],
                'email'    => $user['email'],
                'phone'    => $user['phone'] ?? '',
                'avatar'   => $user['avatar'] ?? null,
                'role'     => $user['role'] ?? 'customer',
                'provider' => 'local',
            ];

            // Tạo Remember Token vĩnh viễn (30 ngày)
            $rememberToken = bin2hex(random_bytes(32));
            $this->users->update((int) $user['id'], ['remember_token' => $rememberToken]);
            $base = base_path() ?: '/';
            setcookie('panas_remember', $rememberToken, time() + (30 * 24 * 3600), $base, '', false, true);

            if (!$isApi) {
                flash('success', 'Đăng ký tài khoản thành công! Bạn đã được tự động đăng nhập.');
                $this->redirect('/');
            }

            $this->json([
                'success'  => true,
                'message'  => 'Đăng ký tài khoản thành công! Bạn đã được tự động đăng nhập.',
                'user'     => $_SESSION['user'],
                'redirect' => url('/')
            ]);
        } catch (\InvalidArgumentException $e) {
            if (!$isApi) {
                flash('error', $e->getMessage());
                $this->redirect('/dang-ky');
            }
            $this->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        } catch (\Exception $e) {
            if (!$isApi) {
                flash('error', 'Không thể tạo tài khoản. Vui lòng thử lại sau.');
                $this->redirect('/dang-ky');
            }
            $this->json([
                'success' => false,
                'message' => 'Không thể tạo tài khoản. Vui lòng thử lại sau.'
            ], 500);
        }
    }

    /**
     * API: Đăng nhập qua Mạng xã hội Google & Facebook
     * POST /api/auth/social-login
     */
    public function socialLogin(): void
    {
        $input = $this->getJsonInput();
        $provider = strtolower(trim((string)($input['provider'] ?? '')));

        if (!in_array($provider, ['google', 'facebook'], true)) {
            $this->json([
                'success' => false,
                'message' => 'Phương thức đăng nhập không hợp lệ (chỉ hỗ trợ Google hoặc Facebook).'
            ], 422);
        }

        $profile = [];

        // 1. Nếu là Google với ID Token / Credential JWT
        if ($provider === 'google' && !empty($input['credential'])) {
            $jwt = (string) $input['credential'];
            $parts = explode('.', $jwt);
            if (count($parts) === 3) {
                $payloadJson = base64_decode(str_replace(['-', '_'], ['+', '/'], $parts[1]));
                $payload = json_decode($payloadJson, true);
                if (is_array($payload) && !empty($payload['sub'])) {
                    $profile = [
                        'id'     => $payload['sub'],
                        'email'  => $payload['email'] ?? null,
                        'name'   => $payload['name'] ?? ($payload['given_name'] ?? 'Google User'),
                        'avatar' => $payload['picture'] ?? null,
                    ];
                }
            }
        }

        // 2. Nếu client gửi trực tiếp object profile (từ FB SDK hoặc Google User Profile)
        if (empty($profile['id']) && !empty($input['id'])) {
            $profile = [
                'id'     => (string) $input['id'],
                'email'  => !empty($input['email']) ? (string) $input['email'] : null,
                'name'   => !empty($input['name']) ? (string) $input['name'] : (ucfirst($provider) . ' User'),
                'avatar' => !empty($input['avatar']) ? (string) $input['avatar'] : null,
                'phone'  => !empty($input['phone']) ? (string) $input['phone'] : null,
            ];
        }

        if (empty($profile['id'])) {
            $this->json([
                'success' => false,
                'message' => 'Không thể nhận diện thông tin tài khoản từ ' . ucfirst($provider) . '.'
            ], 422);
        }

        try {
            $user = $this->users->loginOrCreateSocial($provider, $profile);

            $_SESSION['user'] = [
                'id'       => (int) $user['id'],
                'name'     => $user['name'],
                'email'    => $user['email'],
                'phone'    => $user['phone'] ?? '',
                'avatar'   => $user['avatar'] ?? null,
                'role'     => $user['role'] ?? 'customer',
                'provider' => $provider,
            ];

            // Lưu remember token 30 ngày cho Social Login
            $rememberToken = bin2hex(random_bytes(32));
            $this->users->update((int) $user['id'], ['remember_token' => $rememberToken]);
            $base = base_path() ?: '/';
            setcookie('panas_remember', $rememberToken, time() + (30 * 24 * 3600), $base, '', false, true);

            // Khôi phục giỏ hàng
            if (!empty($user['cart_data'])) {
                $savedCart = json_decode((string)$user['cart_data'], true);
                if (is_array($savedCart) && !empty($savedCart)) {
                    $_SESSION['cart'] = array_replace((array)($_SESSION['cart'] ?? []), $savedCart);
                }
            }

            $this->json([
                'success'  => true,
                'message'  => 'Đăng nhập ' . ucfirst($provider) . ' thành công!',
                'user'     => $_SESSION['user'],
                'redirect' => url('/')
            ]);
        } catch (\Exception $e) {
            $this->json([
                'success' => false,
                'message' => 'Lỗi kết nối tài khoản mạng xã hội: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * API: Lấy thông tin phiên người dùng hiện tại
     * GET /api/auth/me
     */
    public function me(): void
    {
        $user = auth_user();
        $this->json([
            'success'       => true,
            'authenticated' => ($user !== null),
            'user'          => $user
        ]);
    }

    /**
     * API & View: Đăng xuất
     * POST /api/auth/logout hoặc GET /dang-xuat
     */
    public function logout(): void
    {
        if (!empty($_SESSION['user']['id'])) {
            try {
                $this->users->update((int)$_SESSION['user']['id'], ['remember_token' => null]);
            } catch (\Throwable $e) {}
        }

        unset($_SESSION['user']);
        $base = base_path() ?: '/';
        setcookie('panas_remember', '', time() - 3600, $base);

        if ($this->isAjax() || str_contains($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json')) {
            $this->json([
                'success' => true,
                'message' => 'Đã đăng xuất thành công.',
                'redirect' => url('/')
            ]);
        }

        flash('success', 'Bạn đã đăng xuất tài khoản.');
        $this->redirect('/');
    }

    /**
     * Trang xem / quản lý tài khoản người dùng
     * GET /tai-khoan
     */
    public function profile(): void
    {
        $userSession = auth_user();
        if (!$userSession) {
            flash('warning', 'Vui lòng đăng nhập để xem thông tin tài khoản.');
            $this->redirect('/dang-nhap');
        }

        $user = $this->users->find((int) $userSession['id']);
        $orders = $this->users->getOrders((int) $userSession['id']);

        $this->render('auth/profile', [
            'title'  => 'Tài khoản của tôi - ' . config('name'),
            'user'   => $user,
            'orders' => $orders,
        ]);
    }

    /**
     * API: Cập nhật thông tin cá nhân
     * POST /api/auth/update-profile
     */
    public function updateProfile(): void
    {
        $userSession = auth_user();
        if (!$userSession) {
            $this->json(['success' => false, 'message' => 'Chưa đăng nhập'], 401);
        }

        $input = $this->getJsonInput();
        $name = trim((string)($input['name'] ?? ''));
        $phone = trim((string)($input['phone'] ?? ''));
        $address = trim((string)($input['address'] ?? ''));

        if (mb_strlen($name) < 2) {
            $this->json(['success' => false, 'message' => 'Họ tên phải có ít nhất 2 ký tự.'], 422);
        }

        $this->users->update((int) $userSession['id'], [
            'name'    => $name,
            'phone'   => $phone,
            'address' => $address,
        ]);

        $_SESSION['user']['name'] = $name;
        $_SESSION['user']['phone'] = $phone;

        $this->json([
            'success' => true,
            'message' => 'Cập nhật thông tin thành công!',
            'user'    => $_SESSION['user']
        ]);
    }

    /**
     * Trang đăng nhập độc lập (fallback nếu không dùng modal)
     * GET /dang-nhap
     */
    public function loginPage(): void
    {
        if (is_logged_in()) {
            $this->redirect('/');
        }

        $this->render('auth/login', [
            'title' => 'Đăng nhập tài khoản - ' . config('name'),
        ]);
    }

    /**
     * Trang đăng ký độc lập
     * GET /dang-ky
     */
    public function registerPage(): void
    {
        if (is_logged_in()) {
            $this->redirect('/');
        }

        $this->render('auth/register', [
            'title' => 'Đăng ký tài khoản - ' . config('name'),
        ]);
    }
}
