<?php
declare(strict_types=1);

class AuditService
{
    public static function log(
        string $action,
        string $targetType,
        int $targetId,
        ?array $before = null,
        ?array $after = null
    ): void {
        Database::execute(
            'INSERT INTO audit_logs
             (user_id, action, target_type, target_id, old_data, new_data, ip_address)
             VALUES (?, ?, ?, ?, ?, ?, ?)',
            [
                $_SESSION['user']['id'] ?? null,
                $action,
                $targetType,
                $targetId,
                json_encode($before),
                json_encode($after),
                $_SERVER['REMOTE_ADDR'] ?? null
            ]
        );
    }
}
