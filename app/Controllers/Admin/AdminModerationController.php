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

    /**
     * GET /admin/moderation
     * Moderations-Dashboard (Statistiken)
     */
    public function index(): void
    {
        $this->guard('admin.moderation.view');

        $openReports = (int) Database::fetchValue(
            "SELECT COUNT(*) FROM reports WHERE status = 'open'"
        );

        $openAppeals = (int) Database::fetchValue(
            "SELECT COUNT(*) FROM user_appeals WHERE status = 'open'"
        );

        $reportsPerDay = Database::fetchAll(
            "SELECT 
                DATE(created_at) AS day,
                COUNT(*) AS count
             FROM reports
             WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 14 DAY)
             GROUP BY day
             ORDER BY day ASC"
        ) ?? [];

        $modActions = Database::fetchAll(
            "SELECT 
                u.username AS moderator,
                COUNT(*) AS count
             FROM reports r
             JOIN users u ON u.id = r.resolved_by
             WHERE r.resolved_by IS NOT NULL
             GROUP BY r.resolved_by
             ORDER BY count DESC"
        ) ?? [];

        $cronStatus = Database::fetch(
            "SELECT *
             FROM cron_logs
             WHERE job = 'moderation_cleanup'
             ORDER BY created_at DESC
             LIMIT 1"
        );

        View::render('admin/moderation/index', [
            'title'         => 'Moderation',
            'reportsPerDay' => $reportsPerDay,
            'modActions'    => $modActions,
            'openReports'   => $openReports,
            'openAppeals'   => $openAppeals,
            'cronStatus'    => $cronStatus
        ]);
    }

    /**
     * GET /admin/moderation/panel
     * Inhalte & Logs
     */
    public function panel(): void
    {
        $this->guard('admin.moderation.manage');

        // 🔴 Ausgeblendete Forum-Beiträge (Soft-Delete)
        $deletedPosts = Database::fetchAll(
            "SELECT 
                p.id,
                p.content,
                p.deleted_at,
                u.username
             FROM forum_posts p
             JOIN users u ON u.id = p.user_id
             WHERE p.deleted_at IS NOT NULL
             ORDER BY p.deleted_at DESC
             LIMIT 50"
        ) ?? [];

        // 📜 Moderations-Logs
        $logs = Database::fetchAll(
            "SELECT 
                ml.created_at,
                u.username AS moderator,
                ml.action,
                ml.target_type,
                ml.target_id,
                ml.reason
             FROM moderation_logs ml
             LEFT JOIN users u ON u.id = ml.moderator_id
             ORDER BY ml.created_at DESC
             LIMIT 100"
        ) ?? [];

        View::render('admin/moderation/panel', [
            'title'        => 'Moderations-Panel',
            'deletedPosts' => $deletedPosts,
            'logs'         => $logs
        ]);
    }
}
