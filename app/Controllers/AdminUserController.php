<?php
declare(strict_types=1);

final class AdminUserController
{
    private function requirePermission(string $perm): void
    {
        if (!Permission::has($perm)) {
            http_response_code(403);
            View::render('errors/403');
            exit;
        }
    }

    public function editRole(int $userId): void
    {
        $this->requirePermission('admin.users.manage');

        $user  = Database::fetch("SELECT * FROM users WHERE id = ?", [$userId]);
        $roles = Database::fetchAll("SELECT * FROM roles ORDER BY level DESC");

        View::render('admin/user_role', [
            'user'  => $user,
            'roles' => $roles
        ]);
    }

    public function saveRole(): void
    {
        $this->requirePermission('admin.users.manage');

        Database::execute(
            "UPDATE users SET role_id = ? WHERE id = ?",
            [(int)$_POST['role_id'], (int)$_POST['user_id']]
        );

        header('Location: /admin/users');
        exit;
    }
}
