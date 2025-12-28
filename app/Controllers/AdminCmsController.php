<?php
declare(strict_types=1);

class AdminCmsController
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
       INDEX – CMS SEITEN
    ========================================================= */

    public function index(): void
    {
        $this->guard('admin.cms.manage');

        $pages = Database::fetchAll(
            "SELECT * FROM cms_pages ORDER BY slug ASC"
        ) ?? [];

        View::render('admin/cms/index', [
            'title' => 'CMS Seiten',
            'pages' => $pages
        ]);
    }

    /* =========================================================
       EDIT – CMS SEITE
    ========================================================= */

    public function edit(string $slug): void
    {
        $this->guard('admin.cms.manage');

        $page = Database::fetch(
            "SELECT * FROM cms_pages WHERE slug = ?",
            [$slug]
        );

        if (!$page) {
            http_response_code(404);
            View::render('errors/404', [
                'title' => 'Seite nicht gefunden'
            ]);
            return;
        }

        View::render('admin/cms/edit', [
            'title' => 'CMS bearbeiten',
            'page'  => $page
        ]);
    }

    /* =========================================================
       SAVE – CMS SPEICHERN
    ========================================================= */

    public function save(): void
    {
        $this->guard('admin.cms.manage');
        Security::checkCsrf();

        $content = $_POST['content'] ?? '';
        $version = $_POST['version'] ?? '';
        $slug    = $_POST['slug'] ?? '';

        if ($slug === '') {
            http_response_code(400);
            exit('Ungültiger Slug');
        }

        Database::execute(
            "UPDATE cms_pages
             SET content = ?, version = ?, updated_at = NOW()
             WHERE slug = ?",
            [$content, $version, $slug]
        );

        $_SESSION['flash_success'] = 'CMS-Seite wurde gespeichert.';
        header('Location: /admin/cms');
        exit;
    }
}
