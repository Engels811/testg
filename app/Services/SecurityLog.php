<?php
declare(strict_types=1);

/**
 * SecurityLog
 *
 * Zentrales Logging für sicherheitsrelevante Ereignisse:
 * - Login / Logout
 * - Neue IP
 * - Session beendet
 * - Passwort geändert
 * - 2FA aktiviert / deaktiviert
 *
 * Tabelle: security_logs
 */
class SecurityLog
{
    /**
     * Neuen Security-Log-Eintrag schreiben
     */
    public static function log(
        int $userId,
        string $type,
        string $description,
        ?string $ipAddress = null
    ): void {
        Database::execute(
            "INSERT INTO security_logs
             (user_id, type, description, ip_address, created_at)
             VALUES (?, ?, ?, ?, NOW())",
            [
                $userId,
                $type,
                $description,
                $ipAddress ?? self::getIp()
            ]
        );
    }

    /**
     * Letzte Security-Einträge eines Users abrufen
     */
    public static function recent(
        int $userId,
        int $limit = 20
    ): array {
        return Database::fetchAll(
            "SELECT
                type,
                description,
                ip_address,
                created_at
             FROM security_logs
             WHERE user_id = ?
             ORDER BY created_at DESC
             LIMIT {$limit}",
            [$userId]
        );
    }

    /**
     * IP-Adresse sicher ermitteln
     */
    private static function getIp(): string
    {
        if (!empty($_SERVER['HTTP_CF_CONNECTING_IP'])) {
            return $_SERVER['HTTP_CF_CONNECTING_IP']; // Cloudflare
        }

        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            return trim(explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0]);
        }

        return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    }
}
