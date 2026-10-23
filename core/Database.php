<?php
declare(strict_types=1);

namespace Core;

use PDO;
use PDOException;
use PDOStatement;
use RuntimeException;

/**
 * PDO Database wrapper – Singleton.
 * Mọi truy vấn có tham số đều đi qua Prepared Statement (emulate prepares = false).
 */
final class Database
{
    private static ?Database $instance = null;
    private PDO $pdo;

    private function __construct()
    {
        $cfg = require ROOT_PATH . '/config/database.php';

        $dsn = sprintf(
            'mysql:host=%s;port=%s;dbname=%s;charset=%s',
            $cfg['host'],
            $cfg['port'],
            $cfg['database'],
            $cfg['charset']
        );

        try {
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,   // Prepared statement thật phía server
                PDO::ATTR_STRINGIFY_FETCHES  => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES {$cfg['charset']} COLLATE {$cfg['collation']}",
            ];

            // Tự động kích hoạt SSL khi kết nối tới các dịch vụ Cloud MySQL (Aiven, TiDB, PlanetScale...)
            if (!empty($cfg['ssl']) || str_contains((string)$cfg['host'], 'aivencloud.com') || str_contains((string)$cfg['host'], 'tidbcloud.com')) {
                $options[PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT] = false;
            }

            $this->pdo = new PDO($dsn, $cfg['username'], $cfg['password'], $options);
        } catch (PDOException $e) {
            // Không lộ thông tin đăng nhập DB ra ngoài
            error_log('[DB] Connection failed: ' . $e->getMessage());
            throw new RuntimeException('Không thể kết nối cơ sở dữ liệu. Vui lòng kiểm tra cấu hình config/database.php.');
        }
    }

    private function __clone() {}

    public function __wakeup(): void
    {
        throw new RuntimeException('Cannot unserialize singleton');
    }

    public static function getInstance(): Database
    {
        return self::$instance ??= new self();
    }

    public function pdo(): PDO
    {
        return $this->pdo;
    }

    /**
     * Chuẩn bị & thực thi câu lệnh với binding đúng kiểu dữ liệu.
     */
    public function query(string $sql, array $params = []): PDOStatement
    {
        $stmt = $this->pdo->prepare($sql);

        foreach ($params as $key => $value) {
            $param = is_int($key) ? $key + 1 : (str_starts_with((string) $key, ':') ? $key : ':' . $key);
            $type = match (true) {
                is_int($value)  => PDO::PARAM_INT,
                is_bool($value) => PDO::PARAM_BOOL,
                is_null($value) => PDO::PARAM_NULL,
                default         => PDO::PARAM_STR,
            };
            $stmt->bindValue($param, $value, $type);
        }

        $stmt->execute();
        return $stmt;
    }

    public function fetch(string $sql, array $params = []): ?array
    {
        $row = $this->query($sql, $params)->fetch();
        return $row === false ? null : $row;
    }

    public function fetchAll(string $sql, array $params = []): array
    {
        return $this->query($sql, $params)->fetchAll();
    }

    public function fetchColumn(string $sql, array $params = []): mixed
    {
        return $this->query($sql, $params)->fetchColumn();
    }

    public function lastInsertId(): int
    {
        return (int) $this->pdo->lastInsertId();
    }

    public function beginTransaction(): bool
    {
        return $this->pdo->beginTransaction();
    }

    public function commit(): bool
    {
        return $this->pdo->commit();
    }

    public function rollBack(): bool
    {
        return $this->pdo->inTransaction() ? $this->pdo->rollBack() : false;
    }

    /**
     * Thực thi callback trong transaction, tự rollback khi có lỗi.
     */
    public function transaction(callable $callback): mixed
    {
        $this->beginTransaction();
        try {
            $result = $callback($this);
            $this->commit();
            return $result;
        } catch (\Throwable $e) {
            $this->rollBack();
            throw $e;
        }
    }
}
