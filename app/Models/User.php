<?php
declare(strict_types=1);

namespace App\Models;

class User extends BaseModel
{
    protected string $table = 'users';
    protected array $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'provider',
        'provider_id',
        'avatar',
        'address',
        'role',
        'status',
    ];

    /**
     * Tìm theo email
     */
    public function findByEmail(string $email): ?array
    {
        return $this->findBy('email', mb_strtolower(trim($email)));
    }

    /**
     * Tìm theo provider và provider_id (Google / Facebook)
     */
    public function findByProvider(string $provider, string $providerId): ?array
    {
        return $this->db->fetch(
            "SELECT * FROM `{$this->table}` WHERE `provider` = :p AND `provider_id` = :pid LIMIT 1",
            ['p' => $provider, 'pid' => $providerId]
        );
    }

    /**
     * Xác thực đăng nhập email hoặc số điện thoại + mật khẩu
     */
    public function attempt(string $identifier, string $password): ?array
    {
        $identifier = trim($identifier);
        
        $user = $this->db->fetch(
            "SELECT * FROM `{$this->table}` WHERE `email` = :id_e OR `phone` = :id_p LIMIT 1",
            ['id_e' => mb_strtolower($identifier), 'id_p' => $identifier]
        );

        $hash = $user['password'] ?? '$2y$10$usesomesillystringfore7hnbRJHxXVLeakoG8K30oukPsA.ztMG';
        if (!$user || empty($user['password']) || !password_verify($password, $hash)) {
            return null;
        }

        if ($user['status'] !== 'active') {
            return null;
        }

        if (password_needs_rehash($user['password'], PASSWORD_DEFAULT)) {
            $this->update((int) $user['id'], ['password' => password_hash($password, PASSWORD_DEFAULT)]);
        }

        unset($user['password']);
        return $user;
    }

    /**
     * Đăng ký tài khoản thường
     */
    public function register(array $data): array
    {
        $email = mb_strtolower(trim($data['email'] ?? ''));
        if (empty($email)) {
            throw new \InvalidArgumentException('Email không được để trống');
        }

        if ($this->findByEmail($email)) {
            throw new \InvalidArgumentException('Email này đã được sử dụng');
        }

        $passwordHash = password_hash($data['password'], PASSWORD_DEFAULT);

        $insertData = [
            'name'        => trim($data['name'] ?? 'Khách hàng'),
            'email'       => $email,
            'phone'       => trim($data['phone'] ?? ''),
            'password'    => $passwordHash,
            'provider'    => 'local',
            'provider_id' => null,
            'avatar'      => $data['avatar'] ?? null,
            'role'        => 'customer',
            'status'      => 'active',
        ];

        $userId = $this->create($insertData);
        $user = $this->find($userId);
        unset($user['password']);
        return $user;
    }

    /**
     * Đăng nhập hoặc Tạo tài khoản qua Google / Facebook
     */
    public function loginOrCreateSocial(string $provider, array $profile): array
    {
        $providerId = (string) ($profile['id'] ?? '');
        $email = !empty($profile['email']) ? mb_strtolower(trim($profile['email'])) : null;
        $name = trim($profile['name'] ?? ($provider === 'google' ? 'Google User' : 'Facebook User'));
        $avatar = $profile['avatar'] ?? null;

        // 1. Tìm theo provider + provider_id
        $user = null;
        if (!empty($providerId)) {
            $user = $this->findByProvider($provider, $providerId);
        }

        // 2. Nếu không thấy nhưng có email, tìm theo email để liên kết
        if (!$user && !empty($email)) {
            $user = $this->findByEmail($email);
            if ($user) {
                // Cập nhật provider_id và avatar nếu chưa có
                $updates = ['provider' => $provider, 'provider_id' => $providerId];
                if (empty($user['avatar']) && $avatar) {
                    $updates['avatar'] = $avatar;
                }
                $this->update((int) $user['id'], $updates);
                $user = $this->find((int) $user['id']);
            }
        }

        // 3. Nếu chưa có tài khoản, tạo mới
        if (!$user) {
            $dummyEmail = $email ?: ($provider . '_' . $providerId . '@panasonic-social.local');
            $insertData = [
                'name'        => $name,
                'email'       => $dummyEmail,
                'phone'       => $profile['phone'] ?? null,
                'password'    => null,
                'provider'    => $provider,
                'provider_id' => $providerId,
                'avatar'      => $avatar,
                'role'        => 'customer',
                'status'      => 'active',
            ];
            $userId = $this->create($insertData);
            $user = $this->find($userId);
        }

        unset($user['password']);
        return $user;
    }

    /**
     * Danh sách đơn hàng của người dùng
     */
    public function getOrders(int $userId): array
    {
        return $this->db->fetchAll(
            "SELECT * FROM `orders` WHERE `user_id` = :uid ORDER BY `id` DESC",
            ['uid' => $userId]
        );
    }

    /**
     * Phân trang danh sách thành viên cho Admin
     */
    public function paginate(string $keyword = '', string $provider = '', int $page = 1, int $perPage = 15): array
    {
        $where = ['1=1'];
        $params = [];

        if ($keyword !== '') {
            $where[] = '(name LIKE :kw OR email LIKE :kw OR phone LIKE :kw)';
            $params['kw'] = "%{$keyword}%";
        }

        if ($provider !== '') {
            $where[] = 'provider = :provider';
            $params['provider'] = $provider;
        }

        $whereClause = implode(' AND ', $where);
        $total = (int) $this->db->fetchColumn("SELECT COUNT(*) FROM `{$this->table}` WHERE {$whereClause}", $params);

        $pages = max(1, (int) ceil($total / $perPage));
        $page = max(1, min($page, $pages));
        $offset = ($page - 1) * $perPage;

        $items = $this->db->fetchAll(
            "SELECT u.*, (SELECT COUNT(*) FROM `orders` o WHERE o.user_id = u.id) as order_count 
             FROM `{$this->table}` u 
             WHERE {$whereClause} 
             ORDER BY u.id DESC 
             LIMIT {$perPage} OFFSET {$offset}",
            $params
        );

        return [
            'items' => $items,
            'total' => $total,
            'page'  => $page,
            'pages' => $pages,
        ];
    }
}
