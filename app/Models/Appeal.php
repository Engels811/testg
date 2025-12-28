<?php
declare(strict_types=1);

class Appeal
{
    public static function create(
        string $username,
        int $actionId,
        string $message
    ): void {
        Database::execute(
            "INSERT INTO user_appeals
             (username, action_id, message)
             VALUES (?, ?, ?)",
            [$username, $actionId, $message]
        );
    }

    public static function byUser(string $username): array
    {
        return Database::fetchAll(
            "SELECT a.*, ua.action, ua.reason
             FROM user_appeals a
             JOIN user_actions ua ON ua.id = a.action_id
             WHERE a.username = ?
             ORDER BY a.created_at DESC",
            [$username]
        ) ?: [];
    }

    public static function all(): array
    {
        return Database::fetchAll(
            "SELECT *
             FROM user_appeals
             WHERE status = 'open'
             ORDER BY created_at ASC"
        ) ?: [];
    }

    public static function resolve(
        int $id,
        string $status,
        string $actorUsername
    ): void {
        Database::execute(
            "UPDATE user_appeals
             SET status = ?, handled_by = ?, handled_at = NOW()
             WHERE id = ?",
            [$status, $actorUsername, $id]
        );
    }
}
