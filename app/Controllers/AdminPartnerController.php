<?php
declare(strict_types=1);

class AdminPartnerController
{
    public function index(): void
    {
        Security::requireAdmin();

        $partners = Database::fetchAll(
            "SELECT * FROM partners ORDER BY priority DESC, name ASC"
        ) ?? [];

        View::render('admin/partners/index', [
            'title'    => 'Partner verwalten',
            'partners' => $partners
        ]);
    }

    public function create(): void
    {
        Security::requireAdmin();

        View::render('admin/partners/form', [
            'title'   => 'Partner erstellen',
            'partner' => null
        ]);
    }

    public function store(): void
    {
        Security::requireAdmin();
        Security::checkCsrf();

        $name        = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $url         = trim($_POST['url'] ?? '');
        $logo        = trim($_POST['logo'] ?? '');
        $priority    = (int)($_POST['priority'] ?? 0);
        $isActive    = isset($_POST['is_active']) ? 1 : 0;

        if ($name === '' || $url === '') {
            $_SESSION['flash_error'] = 'Name und URL sind erforderlich.';
            header('Location: /admin/partners/create');
            exit;
        }

        Database::execute(
            "INSERT INTO partners (name, description, url, logo, priority, is_active)
             VALUES (?, ?, ?, ?, ?, ?)",
            [$name, $description, $url, $logo, $priority, $isActive]
        );

        $_SESSION['flash_success'] = 'Partner wurde erstellt.';
        header('Location: /admin/partners');
        exit;
    }

    public function edit(int $id): void
    {
        Security::requireAdmin();

        $partner = Database::fetch(
            "SELECT * FROM partners WHERE id = ?",
            [$id]
        );

        if (!$partner) {
            http_response_code(404);
            View::render('errors/404');
            return;
        }

        View::render('admin/partners/form', [
            'title'   => 'Partner bearbeiten',
            'partner' => $partner
        ]);
    }

    public function update(): void
    {
        Security::requireAdmin();
        Security::checkCsrf();

        $id          = (int)($_POST['id'] ?? 0);
        $name        = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $url         = trim($_POST['url'] ?? '');
        $logo        = trim($_POST['logo'] ?? '');
        $priority    = (int)($_POST['priority'] ?? 0);
        $isActive    = isset($_POST['is_active']) ? 1 : 0;

        if ($id <= 0 || $name === '' || $url === '') {
            http_response_code(400);
            exit;
        }

        Database::execute(
            "UPDATE partners
             SET name = ?, description = ?, url = ?, logo = ?, priority = ?, is_active = ?
             WHERE id = ?",
            [$name, $description, $url, $logo, $priority, $isActive, $id]
        );

        $_SESSION['flash_success'] = 'Partner wurde aktualisiert.';
        header('Location: /admin/partners');
        exit;
    }

    public function delete(): void
    {
        Security::requireAdmin();
        Security::checkCsrf();

        $id = (int)($_POST['id'] ?? 0);
        if ($id <= 0) {
            http_response_code(400);
            exit;
        }

        Database::execute(
            "DELETE FROM partners WHERE id = ?",
            [$id]
        );

        $_SESSION['flash_success'] = 'Partner wurde gelöscht.';
        header('Location: /admin/partners');
        exit;
    }
}
