<?php
declare(strict_types=1);

class AdminAgbController
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
       EDIT – AGB BEARBEITEN
    ========================================================= */

    public function edit(): void
    {
        $this->guard('admin.agb.manage');

        $sections = Database::fetchAll(
            "SELECT * FROM agb_sections ORDER BY id ASC"
        ) ?? [];

        View::render('admin/agb/edit', [
            'title'    => 'AGB bearbeiten',
            'sections' => $sections
        ]);
    }

    /* =========================================================
       UPDATE – AGB SPEICHERN
    ========================================================= */

    public function update(): void
    {
        $this->guard('admin.agb.manage');
        Security::checkCsrf();

        $id      = (int)($_POST['id'] ?? 0);
        $content = trim($_POST['content'] ?? '');

        if ($id <= 0) {
            http_response_code(400);
            exit('Ungültige Anfrage');
        }

        Database::execute(
            "UPDATE agb_sections
             SET content = ?, updated_at = NOW()
             WHERE id = ?",
            [$content, $id]
        );

        $_SESSION['flash_success'] = 'AGB-Abschnitt wurde gespeichert.';
        header('Location: /admin/agb/edit');
        exit;
    }
}
