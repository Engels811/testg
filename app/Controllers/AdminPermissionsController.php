<?php
declare(strict_types=1);

class AdminPermissionsController
{
    public function index(): void
    {
        Security::requireSuperadmin();

        $permissions = Database::fetchAll(
            "SELECT * FROM permissions ORDER BY resource, action"
        ) ?? [];

        View::render('admin/permissions/index', [
            'title'       => 'Berechtigungen',
            'permissions' => $permissions
        ]);
    }

    public function assign(): void
    {
        Security::requireSuperadmin();

        $roles = Database::fetchAll(
            "SELECT * FROM roles ORDER BY level DESC"
        ) ?? [];

        $permissions = Database::fetchAll(
            "SELECT * FROM permissions ORDER BY resource, action"
        ) ?? [];

        $assignments = Database::fetchAll(
            "SELECT * FROM role_permissions"
        ) ?? [];

        $map = [];
        foreach ($assignments as $a) {
            $map[$a['role_id'] . '_' . $a['permission_id']] = true;
        }

        View::render('admin/permissions/assign', [
            'title'       => 'Berechtigungen zuweisen',
            'roles'       => $roles,
            'permissions' => $permissions,
            'map'         => $map
        ]);
    }

    public function saveAssignments(): void
    {
        Security::requireSuperadmin();
        Security::checkCsrf();

        Database::execute("DELETE FROM role_permissions");

        $assignments = $_POST['assignments'] ?? [];

        foreach ($assignments as $assignment) {
            [$roleId, $permissionId] = explode('_', $assignment);

            Database::execute(
                "INSERT INTO role_permissions (role_id, permission_id)
                 VALUES (?, ?)",
                [(int)$roleId, (int)$permissionId]
            );
        }

        $_SESSION['flash_success'] = 'Berechtigungen wurden gespeichert.';
        header('Location: /admin/permissions/assign');
        exit;
    }
}
