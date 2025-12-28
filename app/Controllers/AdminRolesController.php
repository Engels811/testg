<?php
declare(strict_types=1);

class AdminRolesController
{
    public function index(): void
    {
        Security::requireSuperadmin();

        $roles = Database::fetchAll(
            "SELECT
                r.*,
                (SELECT COUNT(*) FROM users WHERE role = r.name) AS user_count
             FROM roles r
             ORDER BY r.level DESC"
        ) ?? [];

        View::render('admin/roles/index', [
            'title' => 'Rollen verwalten',
            'roles' => $roles
        ]);
    }

    public function create(): void
    {
        Security::requireSuperadmin();

        View::render('admin/roles/form', [
            'title' => 'Rolle erstellen',
            'role'  => null
        ]);
    }

    public function store(): void
    {
        Security::requireSuperadmin();
        Security::checkCsrf();

        $name  = trim($_POST['name'] ?? '');
        $level = (int)($_POST['level'] ?? 0);

        if ($name === '' || $level <= 0) {
            $_SESSION['flash_error'] = 'Name und Level sind erforderlich.';
            header('Location: /admin/roles/create');
            exit;
        }

        $exists = Database::fetch(
            "SELECT id FROM roles WHERE name = ?",
            [$name]
        );

        if ($exists) {
            $_SESSION['flash_error'] = 'Rolle existiert bereits.';
            header('Location: /admin/roles/create');
            exit;
        }

        Database::execute(
            "INSERT INTO roles (name, level) VALUES (?, ?)",
            [$name, $level]
        );

        $_SESSION['flash_success'] = 'Rolle wurde erstellt.';
        header('Location: /admin/roles');
        exit;
    }

    public function edit(int $id): void
    {
        Security::requireSuperadmin();

        $role = Database::fetch(
            "SELECT * FROM roles WHERE id = ?",
            [$id]
        );

        if (!$role) {
            http_response_code(404);
            View::render('errors/404');
            return;
        }

        View::render('admin/roles/form', [
            'title' => 'Rolle bearbeiten',
            'role'  => $role
        ]);
    }

    public function update(): void
    {
        Security::requireSuperadmin();
        Security::checkCsrf();

        $id    = (int)($_POST['id'] ?? 0);
        $name  = trim($_POST['name'] ?? '');
        $level = (int)($_POST['level'] ?? 0);

        if ($id <= 0 || $name === '' || $level <= 0) {
            http_response_code(400);
            exit;
        }

        $exists = Database::fetch(
            "SELECT id FROM roles WHERE name = ? AND id != ?",
            [$name, $id]
        );

        if ($exists) {
            $_SESSION['flash_error'] = 'Rolle existiert bereits.';
            header('Location: /admin/roles/edit/' . $id);
            exit;
        }

        Database::execute(
            "UPDATE roles SET name = ?, level = ? WHERE id = ?",
            [$name, $level, $id]
        );

        $_SESSION['flash_success'] = 'Rolle wurde aktualisiert.';
        header('Location: /admin/roles');
        exit;
    }

    public function delete(): void
    {
        Security::requireSuperadmin();
        Security::checkCsrf();

        $id = (int)($_POST['id'] ?? 0);
        if ($id <= 0) {
            http_response_code(400);
            exit;
        }

        $userCount = Database::fetch(
            "SELECT COUNT(*) AS count
             FROM users u
             JOIN roles r ON r.name = u.role
             WHERE r.id = ?",
            [$id]
        )['count'] ?? 0;

        if ($userCount > 0) {
            $_SESSION['flash_error'] = 'Rolle wird noch von Benutzern verwendet.';
            header('Location: /admin/roles');
            exit;
        }

        Database::execute(
            "DELETE FROM role_permissions WHERE role_id = ?",
            [$id]
        );

        Database::execute(
            "DELETE FROM roles WHERE id = ?",
            [$id]
        );

        $_SESSION['flash_success'] = 'Rolle wurde gelöscht.';
        header('Location: /admin/roles');
        exit;
    }
}