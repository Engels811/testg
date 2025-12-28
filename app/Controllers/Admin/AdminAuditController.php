<?php

class AdminAuditController
{
    public function index(): void
    {
        Security::requireAdmin();

        $logs = Database::fetchAll(
            "SELECT a.*, u.username
             FROM audit_logs a
             LEFT JOIN users u ON u.id = a.user_id
             ORDER BY a.created_at DESC
             LIMIT 100"
        );

        View::render('admin/audit/index', [
            'title' => 'Audit-Log',
            'logs'  => $logs
        ]);
    }
}
