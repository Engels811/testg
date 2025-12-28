<?php
declare(strict_types=1);

final class AdminRoleController
{
    private function requirePermission(): void
    {
        if (!Permission::has('admin.roles.manage')) {
            http_response_code(403);
            exit;
        }
    }

    public function edit(int $roleId): void
    {
        $this->requirePermission();

        $role = Database::fetch("SELECT * FROM roles WHERE id = ?", [$roleId]);
        $perms = Database::fetchAll("SELECT * FROM permissions");

        $assigned = Database::fetchAll(
            "SELECT permission_id FROM role_permissions WHERE role_id = ?",
            [$roleId]
        );

        View::render('admin/role_permissions', [
            'role'      => $role,
            'perms'     => $perms,
            'assigned'  => array_column($assigned, 'permission_id')
        ]);
    }

    public function save(): void
    {
        $this->requirePermission();

        $roleId = (int)$_POST['role_id'];
        Database::execute("DELETE FROM role_permissions WHERE role_id = ?", [$roleId]);

        foreach ($_POST['permissions'] ?? [] as $permId) {
            Database::execute(
                "INSERT INTO role_permissions (role_id, permission_id) VALUES (?, ?)",
                [$roleId, (int)$permId]
            );
        }

        header('Location: /admin/roles');
        exit;
    }
}
