<?php
declare(strict_types=1);

class UserModeration
{
    /**
     * Moderations-Historie eines Users
     */
    public static function history(string $username): array
    {
        return Database::fetchAll(
            "SELECT
                action,
                reason,
                created_by,
                created_at,
                expires_at,
                active
             FROM user_actions
             WHERE username = ?
             ORDER BY created_at DESC",
            [$username]
        ) ?: [];
    }

    /**
     * Prüfen ob User aktuell gesperrt ist
     */
    public static function isBlocked(string $username): bool
    {
        return (bool) Database::fetchColumn(
            "SELECT 1
             FROM user_actions
             WHERE username = ?
             AND active = 1
             AND action IN ('ban','suspend')
             AND (expires_at IS NULL OR expires_at > NOW())
             LIMIT 1",
            [$username]
        );
    }
}
