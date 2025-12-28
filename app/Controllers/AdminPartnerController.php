<?php
declare(strict_types=1);

class AdminPartnerController
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
       INDEX – LISTE
    ========================================================= */

    public function index(): void
    {
        $this->guard('admin.partners.view');

        $partners = Database::fetchAll(
            "SELECT *
             FROM partners
             ORDER BY created_at DESC"
        );

        View::render(
            'admin/partners/index',
            [
                'title'    => 'Partner',
                'partners' => $partners
            ],
            'admin'
        );
    }

    /* =========================================================
       CREATE – FORM
    ========================================================= */

    public function create(): void
    {
        $this->guard('admin.partners.manage');

        View::render(
            'admin/partners/create',
            [
                'title' => 'Partner anlegen',
                'csrf'  => Security::csrf()
            ],
            'admin'
        );
    }

    /* =========================================================
       STORE – SAVE
    ========================================================= */

    public function store(): void
    {
        $this->guard('admin.partners.manage');
        Security::checkCsrf();

        Database::execute(
            "INSERT INTO partners
                (name, description, logo, url, is_active, created_at)
             VALUES
                (?, ?, ?, ?, ?, NOW())",
            [
                trim($_POST['name']),
                trim($_POST['description'] ?? ''),
                trim($_POST['logo'] ?? ''),
                trim($_POST['url'] ?? ''),
                isset($_POST['is_active']) ? 1 : 0
            ]
        );

        $_SESSION['flash_success'] = 'Partner wurde angelegt.';
        header('Location: /admin/partners');
        exit;
    }

    /* =========================================================
       TOGGLE – AKTIV / INAKTIV
    ========================================================= */

    public function toggle(): void
    {
        $this->guard('admin.partners.manage');
        Security::checkCsrf();

        $id = (int)($_POST['id'] ?? 0);
        if ($id <= 0) {
            header('Location: /admin/partners');
            exit;
        }

        Database::execute(
            "UPDATE partners
             SET is_active = NOT is_active
             WHERE id = ?",
            [$id]
        );

        header('Location: /admin/partners');
        exit;
    }

    /* =========================================================
       DELETE
    ========================================================= */

    public function delete(): void
    {
        $this->guard('admin.partners.manage');
        Security::checkCsrf();

        $id = (int)($_POST['id'] ?? 0);
        if ($id <= 0) {
            header('Location: /admin/partners');
            exit;
        }

        Database::execute(
            "DELETE FROM partners
             WHERE id = ?",
            [$id]
        );

        $_SESSION['flash_success'] = 'Partner wurde gelöscht.';
        header('Location: /admin/partners');
        exit;
    }
}
