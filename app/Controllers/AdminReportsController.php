<?php
declare(strict_types=1);

class AdminReportsController
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
       INDEX – REPORTS
    ========================================================= */

    public function index(): void
    {
        $this->guard('admin.reports.manage');

        View::render('admin/reports/index', [
            'title' => 'Reports'
        ]);
    }
}
