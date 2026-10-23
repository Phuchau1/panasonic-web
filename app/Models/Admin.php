<?php
declare(strict_types=1);

namespace App\Models;

class Admin extends BaseModel
{
    protected string $table = 'admins';
    protected array $fillable = ['username', 'password_hash', 'full_name', 'role', 'last_login'];

    /**
     * Xác thực đăng nhập. Trả về bản ghi admin (không kèm hash) hoặc null.
     */
    public function attempt(string $username, string $password): ?array
    {
        $admin = $this->findBy('username', $username);

        // Luôn gọi password_verify để thời gian phản hồi đồng đều (chống dò username)
        $hash = $admin['password_hash'] ?? '$2y$10$usesomesillystringfore7hnbRJHxXVLeakoG8K30oukPsA.ztMG';
        if (!password_verify($password, $hash) || !$admin) {
            return null;
        }

        if (password_needs_rehash($admin['password_hash'], PASSWORD_DEFAULT)) {
            $this->update((int) $admin['id'], ['password_hash' => password_hash($password, PASSWORD_DEFAULT)]);
        }
        $this->update((int) $admin['id'], ['last_login' => date('Y-m-d H:i:s')]);

        unset($admin['password_hash']);
        return $admin;
    }
}
