<?php
declare(strict_types=1);

final class AdminAuditController
{
    /**
     * GET /admin/audit
     */
    public function index(): void
    {
        Security::requireAdmin();

        $conditions = [];
        $params     = [];

        if (!empty($_GET['user'])) {
            $conditions[] = 'u.username LIKE ?';
            $params[]     = '%' . trim($_GET['user']) . '%';
        }

        if (!empty($_GET['action'])) {
            $conditions[] = 'a.action LIKE ?';
            $params[]     = '%' . trim($_GET['action']) . '%';
        }

        $where = $conditions
            ? 'WHERE ' . implode(' AND ', $conditions)
            : '';

        $logs = Database::fetchAll(
            "
            SELECT
                a.id,
                a.action,
                a.entity       AS target_type,
                a.entity_id    AS target_id,
                a.ip_address,
                a.created_at,
                u.username     AS actor_username
            FROM audit_logs a
            LEFT JOIN users u ON u.id = a.entity_id
            {$where}
            ORDER BY a.created_at DESC
            LIMIT 200
            ",
            $params
        );

        View::render('admin/audit/index', [
            'title' => 'Audit-Log',
            'logs'  => $logs,
        ]);
    }

    /**
     * GET /admin/audit/view/{id}
     */
    public function view(int $id): void
    {
        Security::requireAdmin();

        $log = Database::fetch(
            "
            SELECT
                a.*,
                u.username AS actor_username
            FROM audit_logs a
            LEFT JOIN users u ON u.id = a.entity_id
            WHERE a.id = ?
            LIMIT 1
            ",
            [$id]
        );

        if (!$log) {
            http_response_code(404);
            View::render('errors/404', ['title' => 'Audit-Eintrag nicht gefunden']);
            return;
        }

        View::render('admin/audit/view', [
            'title' => 'Audit-Details',
            'log'   => $log,
        ]);
    }
}
