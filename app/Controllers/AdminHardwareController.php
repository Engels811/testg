<?php
declare(strict_types=1);

class AdminHardwareController
{
    public function index(): void
    {
        Security::requireAdmin();

        $setups = Database::fetchAll(
            "SELECT * FROM hardware_setups ORDER BY is_active DESC, name ASC"
        ) ?? [];

        View::render('admin/hardware/index', [
            'title'  => 'Hardware-Setups',
            'setups' => $setups
        ]);
    }

    public function create(): void
    {
        Security::requireAdmin();

        View::render('admin/hardware/form', [
            'title' => 'Setup erstellen',
            'setup' => null
        ]);
    }

    public function store(): void
    {
        Security::requireAdmin();
        Security::checkCsrf();

        $name        = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $isActive    = isset($_POST['is_active']) ? 1 : 0;

        if ($name === '') {
            $_SESSION['flash_error'] = 'Name ist erforderlich.';
            header('Location: /admin/hardware/create');
            exit;
        }

        Database::execute(
            "INSERT INTO hardware_setups (name, description, is_active)
             VALUES (?, ?, ?)",
            [$name, $description, $isActive]
        );

        $_SESSION['flash_success'] = 'Setup wurde erstellt.';
        header('Location: /admin/hardware');
        exit;
    }

    public function edit(int $id): void
    {
        Security::requireAdmin();

        $setup = Database::fetch(
            "SELECT * FROM hardware_setups WHERE id = ?",
            [$id]
        );

        if (!$setup) {
            http_response_code(404);
            View::render('errors/404');
            return;
        }

        View::render('admin/hardware/form', [
            'title' => 'Setup bearbeiten',
            'setup' => $setup
        ]);
    }

    public function update(): void
    {
        Security::requireAdmin();
        Security::checkCsrf();

        $id          = (int)($_POST['id'] ?? 0);
        $name        = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $isActive    = isset($_POST['is_active']) ? 1 : 0;

        if ($id <= 0 || $name === '') {
            http_response_code(400);
            exit;
        }

        Database::execute(
            "UPDATE hardware_setups
             SET name = ?, description = ?, is_active = ?
             WHERE id = ?",
            [$name, $description, $isActive, $id]
        );

        $_SESSION['flash_success'] = 'Setup wurde aktualisiert.';
        header('Location: /admin/hardware');
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
            "DELETE FROM hardware_items WHERE setup_id = ?",
            [$id]
        );

        Database::execute(
            "DELETE FROM hardware_setups WHERE id = ?",
            [$id]
        );

        $_SESSION['flash_success'] = 'Setup wurde gelöscht.';
        header('Location: /admin/hardware');
        exit;
    }

    public function items(int $setupId): void
    {
        Security::requireAdmin();

        $setup = Database::fetch(
            "SELECT * FROM hardware_setups WHERE id = ?",
            [$setupId]
        );

        if (!$setup) {
            http_response_code(404);
            View::render('errors/404');
            return;
        }

        $items = Database::fetchAll(
            "SELECT * FROM hardware_items
             WHERE setup_id = ?
             ORDER BY category, name",
            [$setupId]
        ) ?? [];

        View::render('admin/hardware/items', [
            'title' => 'Items: ' . $setup['name'],
            'setup' => $setup,
            'items' => $items
        ]);
    }

    public function createItem(int $setupId): void
    {
        Security::requireAdmin();

        $setup = Database::fetch(
            "SELECT * FROM hardware_setups WHERE id = ?",
            [$setupId]
        );

        if (!$setup) {
            http_response_code(404);
            View::render('errors/404');
            return;
        }

        View::render('admin/hardware/item-form', [
            'title' => 'Item erstellen',
            'setup' => $setup,
            'item'  => null
        ]);
    }

    public function storeItem(): void
    {
        Security::requireAdmin();
        Security::checkCsrf();

        $setupId  = (int)($_POST['setup_id'] ?? 0);
        $category = trim($_POST['category'] ?? '');
        $name     = trim($_POST['name'] ?? '');
        $specs    = trim($_POST['specs'] ?? '');

        if ($setupId <= 0 || $category === '' || $name === '') {
            http_response_code(400);
            exit;
        }

        Database::execute(
            "INSERT INTO hardware_items (setup_id, category, name, specs)
             VALUES (?, ?, ?, ?)",
            [$setupId, $category, $name, $specs]
        );

        $_SESSION['flash_success'] = 'Item wurde erstellt.';
        header('Location: /admin/hardware/' . $setupId . '/items');
        exit;
    }

    public function editItem(int $id): void
    {
        Security::requireAdmin();

        $item = Database::fetch(
            "SELECT * FROM hardware_items WHERE id = ?",
            [$id]
        );

        if (!$item) {
            http_response_code(404);
            View::render('errors/404');
            return;
        }

        $setup = Database::fetch(
            "SELECT * FROM hardware_setups WHERE id = ?",
            [(int)$item['setup_id']]
        );

        View::render('admin/hardware/item-form', [
            'title' => 'Item bearbeiten',
            'setup' => $setup,
            'item'  => $item
        ]);
    }

    public function updateItem(): void
    {
        Security::requireAdmin();
        Security::checkCsrf();

        $id       = (int)($_POST['id'] ?? 0);
        $setupId  = (int)($_POST['setup_id'] ?? 0);
        $category = trim($_POST['category'] ?? '');
        $name     = trim($_POST['name'] ?? '');
        $specs    = trim($_POST['specs'] ?? '');

        if ($id <= 0 || $category === '' || $name === '') {
            http_response_code(400);
            exit;
        }

        Database::execute(
            "UPDATE hardware_items
             SET category = ?, name = ?, specs = ?
             WHERE id = ?",
            [$category, $name, $specs, $id]
        );

        $_SESSION['flash_success'] = 'Item wurde aktualisiert.';
        header('Location: /admin/hardware/' . $setupId . '/items');
        exit;
    }

    public function deleteItem(): void
    {
        Security::requireAdmin();
        Security::checkCsrf();

        $id      = (int)($_POST['id'] ?? 0);
        $setupId = (int)($_POST['setup_id'] ?? 0);

        if ($id <= 0) {
            http_response_code(400);
            exit;
        }

        Database::execute(
            "DELETE FROM hardware_items WHERE id = ?",
            [$id]
        );

        $_SESSION['flash_success'] = 'Item wurde gelöscht.';
        header('Location: /admin/hardware/' . $setupId . '/items');
        exit;
    }
}
