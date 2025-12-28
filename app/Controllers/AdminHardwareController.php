<?php
declare(strict_types=1);

class AdminHardwareController
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
       ÜBERSICHT
    ========================================================= */

    public function index(): void
    {
        $this->guard('admin.hardware.manage');

        $setups = Database::fetchAll(
            "SELECT *
             FROM hardware_setups
             ORDER BY id ASC"
        ) ?? [];

        if (empty($setups)) {
            View::render('admin/hardware/index', [
                'title'        => 'Hardware verwalten',
                'setups'       => [],
                'items'        => [],
                'activeSetup'  => 0
            ]);
            return;
        }

        $activeSetupId = (int)($_GET['setup'] ?? $setups[0]['id']);

        $items = Database::fetchAll(
            "SELECT *
             FROM hardware_items
             WHERE setup_id = ?
             ORDER BY category ASC, sort ASC",
            [$activeSetupId]
        ) ?? [];

        View::render('admin/hardware/index', [
            'title'        => 'Hardware verwalten',
            'setups'       => $setups,
            'activeSetup'  => $activeSetupId,
            'items'        => $items
        ]);
    }

    /* =========================================================
       SPEICHERN (CREATE / UPDATE)
    ========================================================= */

    public function save(): void
    {
        $this->guard('admin.hardware.manage');
        Security::checkCsrf();

        $id = (int)($_POST['id'] ?? 0);

        $data = [
            (int)($_POST['setup_id'] ?? 0),
            trim($_POST['category'] ?? ''),
            trim($_POST['icon'] ?? ''),
            trim($_POST['title'] ?? ''),
            trim($_POST['name'] ?? ''),
            trim($_POST['details'] ?? ''),
            (int)($_POST['sort'] ?? 0)
        ];

        if ($id > 0) {
            Database::execute(
                "UPDATE hardware_items
                 SET setup_id = ?, category = ?, icon = ?, title = ?, name = ?, details = ?, sort = ?
                 WHERE id = ?",
                [...$data, $id]
            );
        } else {
            Database::execute(
                "INSERT INTO hardware_items
                 (setup_id, category, icon, title, name, details, sort)
                 VALUES (?, ?, ?, ?, ?, ?, ?)",
                $data
            );
        }

        $_SESSION['flash_success'] = 'Hardware-Eintrag gespeichert.';
        header('Location: /admin/hardware?setup=' . $data[0]);
        exit;
    }

    /* =========================================================
       LÖSCHEN
    ========================================================= */

    public function delete(): void
    {
        $this->guard('admin.hardware.manage');
        Security::checkCsrf();

        $id = (int)($_POST['id'] ?? 0);
        if ($id <= 0) {
            http_response_code(400);
            exit('Ungültige Anfrage');
        }

        Database::execute(
            "DELETE FROM hardware_items WHERE id = ?",
            [$id]
        );

        $_SESSION['flash_success'] = 'Hardware-Eintrag gelöscht.';
        header('Location: /admin/hardware');
        exit;
    }

    /* =========================================================
       SORTIERUNG (AJAX)
    ========================================================= */

    public function sort(): void
    {
        $this->guard('admin.hardware.manage');
        Security::checkCsrf();

        $order = $_POST['order'] ?? [];

        foreach ($order as $sort => $id) {
            Database::execute(
                "UPDATE hardware_items
                 SET sort = ?
                 WHERE id = ?",
                [(int)$sort, (int)$id]
            );
        }

        header('Content-Type: application/json');
        echo json_encode(['status' => 'ok']);
        exit;
    }
}
