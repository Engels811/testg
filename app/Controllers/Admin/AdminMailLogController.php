<?php
declare(strict_types=1);

class AdminMailLogController
{
    public function index(): void
    {
        Security::requireAdmin();

        $logs = Database::fetchAll(
            "SELECT * FROM mail_log
             ORDER BY created_at DESC
             LIMIT 100"
        ) ?? [];

        View::render('admin/mail/index', [
            'title' => 'Mail-Log',
            'logs'  => $logs
        ]);
    }

    public function view(int $id): void
    {
        Security::requireAdmin();

        $log = Database::fetch(
            "SELECT * FROM mail_log WHERE id = ?",
            [$id]
        );

        if (!$log) {
            http_response_code(404);
            View::render('errors/404');
            return;
        }

        View::render('admin/mail/view', [
            'title' => 'Mail-Details',
            'log'   => $log
        ]);
    }

    public function clear(): void
    {
        Security::requireAdmin();
        Security::checkCsrf();

        Database::execute(
            "DELETE FROM mail_log
             WHERE created_at < (NOW() - INTERVAL 30 DAY)"
        );

        $_SESSION['flash_success'] = 'Alte Logs wurden gelöscht.';
        header('Location: /admin/mail');
        exit;
    }
}
