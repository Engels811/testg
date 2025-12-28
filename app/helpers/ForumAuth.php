<?php

class ForumAuth
{
    public static function isOwner(array $post, array $user): bool
    {
        return (int)$post['user_id'] === (int)$user['id'];
    }

    public static function isStaff(array $user): bool
    {
        return in_array($user['role'] ?? '', ['admin', 'moderator'], true);
    }

    public static function canEdit(array $post, array $user): bool
    {
        return self::isOwner($post, $user) || self::isStaff($user);
    }

    public static function canDelete(array $post, array $user): bool
    {
        return self::isStaff($user);
    }
}
