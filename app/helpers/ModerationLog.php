<?php

class ModerationLog
{
    public static function log(
        string $action,
        string $targetType,
        int $targetId,
        int $moderatorId,
        ?string $reason = null
    ): void {
        Database::execute(
            'INSERT INTO forum_moderation_log
             (action, target_type, target_id, moderator_id, reason)
             VALUES (?, ?, ?, ?, ?)',
            [$action, $targetType, $targetId, $moderatorId, $reason]
        );
    }
}
