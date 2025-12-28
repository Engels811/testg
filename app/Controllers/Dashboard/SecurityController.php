<?php
declare(strict_types=1);

final class SecurityController
{
    /**
     * GET /dashboard/security
     * Sicherheitsübersicht des Users
     */
    public function index(): void
    {
        Security::requireLogin();

        if (!isset($_SESSION['user']['id'])) {
            http_response_code(403);
            View::render('errors/403', [
                'title' => 'Zugriff verweigert'
            ]);
            return;
        }

        $userId = (int)$_SESSION['user']['id'];

        // Letzte erfolgreiche Logins
        $logins = Database::fetchAll(
            'SELECT
                ip_address,
                user_agent,
                created_at
             FROM login_logs
             WHERE user_id = ?
               AND success = 1
             ORDER BY created_at DESC
             LIMIT 15',
            [$userId]
        );

        View::render('dashboard/security', [
            'title'  => 'Sicherheit',
            'logins' => $logins
        ]);
    }
}
