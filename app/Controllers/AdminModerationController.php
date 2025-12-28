<?php
declare(strict_types=1);

class AdminModerationController
{
    /* =========================================================
       ACCESS GUARD (PERMISSION-BASIERT)
    ========================================================= */

    private function guard(string $permission): void
    {
        if (
            empty($_SESSION['user']) ||
            !Permission::has($permission)
        ) {
            http_response_code(403);
            View::render('errors/403', [
                'title' => 'Zugriff verweigert'
            ]);
            exit;
        }
    }

    /* =========================================================
       INDEX – MODERATION DASHBOARD
    ========================================================= */

    public function index(): void
    {
        $this->guard('admin.moderation.view');

        $logs = Database::fetchAll(
            'SELECT
                l.*,
                u.username AS moderator
             FROM forum_moderation_log l
             JOIN users u ON u.id = l.moderator_id
             ORDER BY l.created_at DESC
             LIMIT 200'
        ) ?? [];

        $deletedPosts = Database::fetchAll(
            'SELECT
                p.id,
                p.thread_id,
                p.content,
                p.deleted_at,
                u.username
             FROM forum_posts p
             JOIN users u ON u.id = p.user_id
             WHERE p.is_deleted = 1
             ORDER BY p.deleted_at DESC'
        ) ?? [];

        View::render('admin/moderation/index', [
            'title'        => 'Moderations-Panel',
            'logs'         => $logs,
            'deletedPosts' => $deletedPosts
        ]);
    }
}
