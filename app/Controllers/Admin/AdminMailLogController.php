<?php
declare(strict_types=1);

class AdminMailLogController
{
    public function index(): void
    {
        $logs = Database::fetchAll(
            'SELECT *
             FROM mail_logs
             ORDER BY created_at DESC
             LIMIT 250'
        );

        View::render('admin/mail_logs/index', [
            'title' => 'Mail-Logs',
            'logs'  => $logs
        ]);
    }
}
