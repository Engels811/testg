<?php
final class Permission
{
    /* =========================
       ROLE LEVEL MAP
    ========================= */
    private const ROLE_LEVELS = [
        'user'       => 10,
        'moderator'  => 50,
        'admin'      => 80,
        'superadmin' => 100,
    ];

    /* =========================
       PERMISSION MAP
    ========================= */
    private const PERMISSIONS = [
        'admin.access' => 80,
        'admin.users'  => 80,
        'admin.forum'  => 50,
    ];

    /* =========================
       CHECK
    ========================= */
    public static function has(string $permission): bool
    {
        if (empty($_SESSION['user']['role_level'])) {
            return false;
        }

        $requiredLevel = self::PERMISSIONS[$permission] ?? null;
        if ($requiredLevel === null) {
            return false;
        }

        return $_SESSION['user']['role_level'] >= $requiredLevel;
    }
}
