<?php

final class AdminAccess
{
    public static function canAccessAdmin(array $user): bool
    {
        return !empty($user['role_is_admin']);
    }

    public static function hasMinLevel(array $user, int $level): bool
    {
        return ($user['role_level'] ?? 0) >= $level;
    }
}
