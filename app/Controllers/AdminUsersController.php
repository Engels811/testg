<?php
declare(strict_types=1);

class AdminUsersController
{
    public function index(): void
    {
        Security::requireAdmin();

        $search = trim($_GET['search'] ?? '');
        $role   = $_GET['role'] ?? '';
        $status = $_GET['status'] ?? '';

        $where  = [];
        $params = [];

        if ($search !== '') {
            $where[]  = '(u.username LIKE ? OR u.email LIKE ?)';
            $params[] = "%{$search}%";
            $params[] = "%{$search}%";
        }

        if ($role !== '') {
            $where[]  = 'u.role = ?';
            $params[] = $role;
        }

        if ($status === 'locked') {
            $where[] = 'u.account_locked = 1';
        } elseif ($status === 'active') {
            $where[] = 'u.account_locked = 0';
        }

        $sql = "SELECT
                    u.id,
                    u.username,
                    u.email,
                    u.role,
                    u.account_locked,
                    u.agb_accepted_at,
                    u.created_at,
                    u.last_login
                FROM users u";

        if ($where) {
            $sql .= ' WHERE ' . implode(' AND ', $where);
        }

        $sql .= ' ORDER BY u.created_at DESC LIMIT 200';

        $users = Database::fetchAll($sql, $params) ?? [];

        $roles = Database::fetchAll(
            "SELECT DISTINCT name FROM roles ORDER BY name"
        ) ?? [];

        View::render('admin/users/index', [
            'title'  => 'Benutzer verwalten',
            'users'  => $users,
            'roles'  => $roles,
            'filter' => [
                'search' => $search,
                'role'   => $role,
                'status' => $status
            ]
        ]);
    }

    public function edit(int $id): void
    {
        Security::requireAdmin();

        $user = Database::fetch(
            "SELECT * FROM users WHERE id = ?",
            [$id]
        );

        if (!$user) {
            http_response_code(404);
            View::render('errors/404');
            return;
        }

        $roles = Database::fetchAll(
            "SELECT * FROM roles ORDER BY level DESC"
        ) ?? [];

        View::render('admin/users/edit', [
            'title' => 'Benutzer bearbeiten',
            'user'  => $user,
            'roles' => $roles
        ]);
    }

    public function update(): void
    {
        Security::requireAdmin();
        Security::checkCsrf();

        $id    = (int)($_POST['id'] ?? 0);
        $email = trim($_POST['email'] ?? '');
        $role  = $_POST['role'] ?? 'user';

        if ($id <= 0 || $email === '') {
            http_response_code(400);
            exit;
        }

        Database::execute(
            "UPDATE users SET email = ?, role = ? WHERE id = ?",
            [$email, $role, $id]
        );

        $_SESSION['flash_success'] = 'Benutzer wurde aktualisiert.';
        header('Location: /admin/users');
        exit;
    }

    public function lock(): void
    {
        Security::requireAdmin();
        Security::checkCsrf();

        $id = (int)($_POST['id'] ?? 0);
        if ($id <= 0 || $id === 1) {
            http_response_code(400);
            exit;
        }

        Database::execute(
            "UPDATE users SET account_locked = 1 WHERE id = ?",
            [$id]
        );

        $_SESSION['flash_success'] = 'Benutzer wurde gesperrt.';
        header('Location: /admin/users');
        exit;
    }

    public function unlock(): void
    {
        Security::requireAdmin();
        Security::checkCsrf();

        $id = (int)($_POST['id'] ?? 0);
        if ($id <= 0) {
            http_response_code(400);
            exit;
        }

        Database::execute(
            "UPDATE users SET account_locked = 0 WHERE id = ?",
            [$id]
        );

        $_SESSION['flash_success'] = 'Benutzer wurde entsperrt.';
        header('Location: /admin/users');
        exit;
    }

    public function delete(): void
    {
        Security::requireSuperadmin();
        Security::checkCsrf();

        $id = (int)($_POST['id'] ?? 0);
        if ($id <= 0 || $id === 1) {
            http_response_code(400);
            exit;
        }

        Database::execute(
            "DELETE FROM users WHERE id = ?",
            [$id]
        );

        $_SESSION['flash_success'] = 'Benutzer wurde gelöscht.';
        header('Location: /admin/users');
        exit;
    }
}
