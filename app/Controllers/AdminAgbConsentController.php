<?php
declare(strict_types=1);

class AdminAgbConsentController
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
       INDEX – AGB-ZUSTIMMUNGEN
    ========================================================= */

    public function index(): void
    {
        $this->guard('admin.agb.consents.view');

        $consents = Database::fetchAll(
            "SELECT
                username,
                email,
                agb_version AS version,
                agb_accepted_at AS accepted_at
             FROM users
             WHERE agb_accepted_at IS NOT NULL
             ORDER BY agb_accepted_at DESC"
        ) ?? [];

        View::render('admin/agb/consents', [
            'title'    => 'AGB-Zustimmungen',
            'consents' => $consents
        ]);
    }
}
