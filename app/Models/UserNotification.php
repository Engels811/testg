<?php
declare(strict_types=1);

class UserNotification
{
    public static function create(
        string $username,
        string $title,
        string $message,
        string $type = 'info'
    ): void {
        Database::execute(
            "INSERT INTO user_notifications
             (username, title, message, type)
             VALUES (?, ?, ?, ?)",
            [$username, $title, $message, $type]
        );
    }

    public static function unread(string $username): array
    {
        return Database::fetchAll(
            "SELECT *
             FROM user_notifications
             WHERE username = ?
             AND is_read = 0
             ORDER BY created_at DESC",
            [$username]
        ) ?: [];
    }

    public static function markRead(int $id): void
    {
        Database::execute(
            "UPDATE user_notifications
             SET is_read = 1
             WHERE id = ?",
            [$id]
        );
    }
}
