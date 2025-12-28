<?php
declare(strict_types=1);

final class Database
{
    private static ?PDO $pdo = null;

    /* =========================================================
       CONNECT
    ========================================================= */
    private static function connect(): PDO
    {
        if (self::$pdo instanceof PDO) {
            return self::$pdo;
        }

        // 🔐 CONFIG LADEN
        $configFile = BASE_PATH . '/app/config/config.php';

        if (!is_file($configFile)) {
            throw new RuntimeException('Database config.php nicht gefunden');
        }

        $config = require $configFile;

        if (!isset($config['db']) || !is_array($config['db'])) {
            throw new RuntimeException('Ungültige Datenbank-Konfiguration');
        }

        $host    = $config['db']['host']    ?? '';
        $db      = $config['db']['name']    ?? '';
        $user    = $config['db']['user']    ?? '';
        $pass    = $config['db']['pass']    ?? '';
        $charset = $config['db']['charset'] ?? '';

        if ($host === '' || $db === '' || $user === '') {
            throw new RuntimeException('Datenbank-Zugangsdaten unvollständig');
        }

        $dsn = "mysql:host={$host};dbname={$db};charset={$charset}";

        try {
            self::$pdo = new PDO(
                $dsn,
                $user,
                $pass,
                [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                    PDO::ATTR_PERSISTENT         => false,
                ]
            );
        } catch (PDOException) {
            // ❗ Keine Credentials leaken
            throw new RuntimeException('Datenbankverbindung fehlgeschlagen');
        }

        return self::$pdo;
    }

    /* =========================
       FETCH ONE
    ========================= */
    public static function fetch(string $sql, array $params = []): ?array
    {
        $stmt = self::connect()->prepare($sql);
        $stmt->execute($params);
        $row = $stmt->fetch();
        return $row !== false ? $row : null;
    }

    /* =========================
       FETCH ALL
    ========================= */
    public static function fetchAll(string $sql, array $params = []): array
    {
        $stmt = self::connect()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /* =========================
       FETCH COLUMN
    ========================= */
    public static function fetchColumn(string $sql, array $params = []): mixed
    {
        $stmt = self::connect()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchColumn();
    }

    /* =========================
       EXECUTE
    ========================= */
    public static function execute(string $sql, array $params = []): bool
    {
        $stmt = self::connect()->prepare($sql);
        return $stmt->execute($params);
    }

    /* =========================
       LAST INSERT ID
    ========================= */
    public static function lastInsertId(): int
    {
        return (int) self::connect()->lastInsertId();
    }

    /* =========================
       TRANSACTIONS
    ========================= */
    public static function begin(): void
    {
        self::connect()->beginTransaction();
    }

    public static function commit(): void
    {
        self::connect()->commit();
    }

    public static function rollback(): void
    {
        if (self::connect()->inTransaction()) {
            self::connect()->rollBack();
        }
    }
}