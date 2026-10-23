<?php
declare(strict_types=1);

namespace App\Models;

use Core\Database;

/**
 * BaseModel – CRUD chung, chỉ cho phép ghi các cột trong $fillable (chống mass-assignment).
 */
abstract class BaseModel
{
    protected Database $db;
    protected string $table = '';
    protected string $primaryKey = 'id';
    /** @var string[] */
    protected array $fillable = [];

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function find(int $id): ?array
    {
        return $this->db->fetch(
            "SELECT * FROM `{$this->table}` WHERE `{$this->primaryKey}` = :id LIMIT 1",
            ['id' => $id]
        );
    }

    public function findBy(string $column, mixed $value): ?array
    {
        $column = $this->safeColumn($column);
        return $this->db->fetch(
            "SELECT * FROM `{$this->table}` WHERE `{$column}` = :value LIMIT 1",
            ['value' => $value]
        );
    }

    public function all(string $orderBy = 'id', string $direction = 'DESC'): array
    {
        $orderBy   = $this->safeColumn($orderBy);
        $direction = strtoupper($direction) === 'ASC' ? 'ASC' : 'DESC';
        return $this->db->fetchAll("SELECT * FROM `{$this->table}` ORDER BY `{$orderBy}` {$direction}");
    }

    public function count(string $where = '1=1', array $params = []): int
    {
        return (int) $this->db->fetchColumn("SELECT COUNT(*) FROM `{$this->table}` WHERE {$where}", $params);
    }

    public function create(array $data): int
    {
        $data = $this->onlyFillable($data);
        if (!$data) {
            throw new \InvalidArgumentException('Không có dữ liệu hợp lệ để thêm mới');
        }
        $columns      = array_keys($data);
        $columnList   = implode(', ', array_map(fn($c) => "`{$c}`", $columns));
        $placeholders = implode(', ', array_map(fn($c) => ':' . $c, $columns));

        $this->db->query("INSERT INTO `{$this->table}` ({$columnList}) VALUES ({$placeholders})", $data);
        return $this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $data = $this->onlyFillable($data);
        if (!$data) {
            return false;
        }
        $set = implode(', ', array_map(fn($c) => "`{$c}` = :{$c}", array_keys($data)));
        $data['__pk'] = $id;

        return $this->db->query(
            "UPDATE `{$this->table}` SET {$set} WHERE `{$this->primaryKey}` = :__pk",
            $data
        )->rowCount() >= 0;
    }

    public function delete(int $id): bool
    {
        return $this->db->query(
            "DELETE FROM `{$this->table}` WHERE `{$this->primaryKey}` = :id",
            ['id' => $id]
        )->rowCount() > 0;
    }

    protected function onlyFillable(array $data): array
    {
        return array_intersect_key($data, array_flip($this->fillable));
    }

    /**
     * Chỉ cho phép tên cột dạng [a-z0-9_] để chèn an toàn vào SQL.
     */
    protected function safeColumn(string $column): string
    {
        if (!preg_match('/^[a-zA-Z0-9_]+$/', $column)) {
            throw new \InvalidArgumentException('Tên cột không hợp lệ');
        }
        return $column;
    }
}
