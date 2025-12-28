<?php
declare(strict_types=1);

class AdminDashboardController
{
    public function moderation(): void
    {
        Security::requireTeam();

        /* =========================
           REPORTS PRO TAG (14 TAGE)
        ========================= */
        $reportsPerDay = Database::fetchAll(
            "SELECT
                DATE(created_at) AS day,
                COUNT(*) AS count
             FROM reports
             GROUP BY day
             ORDER BY day DESC
             LIMIT 14"
        ) ?: [];

        /* =========================
           MOD-AKTIONEN PRO TEAMMITGLIED
        ========================= */
        $modActions = Database::fetchAll(
            "SELECT
                created_by,
                COUNT(*) AS count
             FROM user_actions
             GROUP BY created_by
             ORDER BY count DESC"
        ) ?: [];

        /* =========================
           OFFENE COUNTER
        ========================= */
        $openReports = (int) Database::fetchColumn(
            "SELECT COUNT(*) FROM reports WHERE status = 'open'"
        );

        $openAppeals = (int) Database::fetchColumn(
            "SELECT COUNT(*) FROM user_appeals WHERE status = 'open'"
        );

        /* =========================
           CRON STATUS (moderation_cleanup)
        ========================= */
        $cronStatus = CronLogger::lastRun('moderation_cleanup');

        /* =========================
           VIEW
        ========================= */
        View::render('admin/dashboard/moderation', [
            'title'           => 'Moderations-Dashboard',
            'reportsPerDay'   => $reportsPerDay,
            'modActions'      => $modActions,
            'openReports'     => $openReports,
            'openAppeals'     => $openAppeals,
            'cronStatus'      => $cronStatus
        ]);
    }
}
