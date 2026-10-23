<?php
declare(strict_types=1);

namespace App\Models;

class UserAddress extends BaseModel
{
    protected string $table = 'user_addresses';
    protected array $fillable = [
        'user_id', 'recipient_name', 'phone', 'province', 'district', 'ward',
        'detail_address', 'label', 'is_default', 'created_at', 'updated_at'
    ];

    /**
     * Lấy toàn bộ địa chỉ của một user, địa chỉ mặc định đưa lên đầu
     */
    public function getByUser(int $userId): array
    {
        return $this->db->fetchAll(
            "SELECT * FROM `{$this->table}` WHERE `user_id` = :uid ORDER BY `is_default` DESC, `id` DESC",
            ['uid' => $userId]
        );
    }

    /**
     * Lấy địa chỉ mặc định của user (hoặc địa chỉ đầu tiên nếu chưa có mặc định)
     */
    public function getDefault(int $userId): ?array
    {
        $addr = $this->db->fetch(
            "SELECT * FROM `{$this->table}` WHERE `user_id` = :uid AND `is_default` = 1 LIMIT 1",
            ['uid' => $userId]
        );
        if (!$addr) {
            $addr = $this->db->fetch(
                "SELECT * FROM `{$this->table}` WHERE `user_id` = :uid ORDER BY `id` DESC LIMIT 1",
                ['uid' => $userId]
            );
        }
        return $addr ?: null;
    }

    /**
     * Thêm địa chỉ mới cho user
     */
    public function addAddress(int $userId, array $data): int
    {
        $isDefault = !empty($data['is_default']) ? 1 : 0;
        if ($isDefault) {
            $this->db->query("UPDATE `{$this->table}` SET `is_default` = 0 WHERE `user_id` = :uid", ['uid' => $userId]);
        }

        return (int) $this->create([
            'user_id'        => $userId,
            'recipient_name' => mb_substr(trim($data['recipient_name'] ?? ''), 0, 150),
            'phone'          => preg_replace('/[\s\.\-]/', '', (string)($data['phone'] ?? '')),
            'province'       => mb_substr(trim($data['province'] ?? ''), 0, 100),
            'district'       => mb_substr(trim($data['district'] ?? ''), 0, 100),
            'ward'           => mb_substr(trim($data['ward'] ?? ''), 0, 100),
            'detail_address' => mb_substr(trim($data['detail_address'] ?? ''), 0, 255),
            'label'          => mb_substr(trim($data['label'] ?? 'Chân công trình'), 0, 50),
            'is_default'     => $isDefault,
            'created_at'     => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Đặt địa chỉ làm mặc định
     */
    public function setDefault(int $userId, int $addressId): bool
    {
        $this->db->query("UPDATE `{$this->table}` SET `is_default` = 0 WHERE `user_id` = :uid", ['uid' => $userId]);
        $this->db->query(
            "UPDATE `{$this->table}` SET `is_default` = 1, `updated_at` = NOW() WHERE `id` = :aid AND `user_id` = :uid",
            ['aid' => $addressId, 'uid' => $userId]
        );
        return true;
    }

    /**
     * Xóa địa chỉ của user
     */
    public function deleteAddress(int $userId, int $addressId): bool
    {
        $this->db->query(
            "DELETE FROM `{$this->table}` WHERE `id` = :aid AND `user_id` = :uid",
            ['aid' => $addressId, 'uid' => $userId]
        );
        return true;
    }
}
