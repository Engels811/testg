<?php
declare(strict_types=1);

class AdminPermissionsController
{
    private function guard(): void
    {
        if (
            empty($_SESSION['user']) ||
            !Permission::has('admin.permissions.manage')
        ) {
            http_response_code(403);
            View::render('errors/403');
            exit;
        }
    }

    public function index(): void
    {
        $this->guard();

        $permissions = Database::fetchAll(
            'SELECT * FROM permissions ORDER BY key_name'
        );

        View::render('admin/permissions/index', [
            'title' => 'Permissions',
            'permissions' => $permissions
        ]);
    }

    public function store(): void
    {
        $this->guard();
        Security::checkCsrf();

        Database::execute(
            'INSERT INTO permissions (key_name, description)
             VALUES (?, ?)',
            [
                trim($_POST['key_name']),
                trim($_POST['description'] ?? '')
            ]
        );

        header('Location: /admin/permissions');
        exit;
    }

    public function delete(): void
    {
        $this->guard();
        Security::checkCsrf();

        Database::execute(
            'DELETE FROM permissions WHERE id = ?',
            [(int)$_POST['id']]
        );

        header('Location: /admin/permissions');
        exit;
    }
}
