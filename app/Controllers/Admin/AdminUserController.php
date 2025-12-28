<?php
declare(strict_types=1);

class AdminUsersController
{
    /**
     * GET /admin/users
     * Übersicht Benutzerverwaltung
     */
    public function index(): void
    {
        Security::requireAdmin();

        $users = Database::fetchAll(
            "SELECT
                u.id,
                u.username,
                u.email,
                r.id   AS role_id,
                r.name AS role_name,
                r.level AS role_level
             FROM users u
             JOIN roles r ON r.id = u.role_id
             ORDER BY u.username ASC"
        );

        $roles = Database::fetchAll(
            "SELECT id, name, level
             FROM roles
             ORDER BY level ASC"
        );

        View::render('admin/users/index', [
            'title' => 'Benutzerverwaltung',
            'users' => $users,
            'roles' => $roles
        ]);
    }

    /**
     * POST /admin/users/update-role
     * Rolle eines Users ändern
     */
    public function updateRole(): void
    {
        Security::requireAdmin();
        Security::checkCsrf();

        $userId = (int)($_POST['user_id'] ?? 0);
        $roleId = (int)($_POST['role_id'] ?? 0);

        if ($userId <= 0 || $roleId <= 0) {
            $_SESSION['flash_error'] = 'Ungültige Anfrage.';
            header('Location: /admin/users');
            exit;
        }

        // Ziel-User inkl. aktueller Rolle laden
        $target = Database::fetch(
            "SELECT
                u.id,
                u.username,
                r.id   AS role_id,
                r.name AS role_name,
                r.level AS role_level
             FROM users u
             JOIN roles r ON r.id = u.role_id
             WHERE u.id = ?
             LIMIT 1",
            [$userId]
        );

        if (!$target) {
            $_SESSION['flash_error'] = 'Benutzer nicht gefunden.';
            header('Location: /admin/users');
            exit;
        }

        // RBAC: keine gleich- oder höhergestellten User ändern
        if (
            ($target['role_level'] ?? 0) >= ($_SESSION['user']['role_level'] ?? 0)
        ) {
            http_response_code(403);
            View::render('errors/403', ['title' => 'Zugriff verweigert']);
            return;
        }

        // Alte Rolle für Audit
        $oldRole = [
            'id'    => (int)$target['role_id'],
            'name'  => $target['role_name'],
            'level' => (int)$target['role_level'],
        ];

        // Neue Rolle laden
        $newRole = Database::fetch(
            "SELECT id, name, level
             FROM roles
             WHERE id = ?
             LIMIT 1",
            [$roleId]
        );

        if (!$newRole) {
            $_SESSION['flash_error'] = 'Ungültige Rolle.';
            header('Location: /admin/users');
            exit;
        }

        // Update durchführen
        Database::execute(
            "UPDATE users SET role_id = ? WHERE id = ?",
            [$roleId, $userId]
        );

        // Audit-Log
        AuditService::log(
            'user.role.changed',
            'user',
            $userId,
            $oldRole,
            [
                'id'    => (int)$newRole['id'],
                'name'  => $newRole['name'],
                'level' => (int)$newRole['level'],
            ]
        );

        $_SESSION['flash_success'] =
            'Rolle von ' . htmlspecialchars($target['username'], ENT_QUOTES, 'UTF-8') . ' geändert.';

        header('Location: /admin/users');
        exit;
    }

    /**
     * POST /admin/users/delete
     * Benutzer löschen
     */
    public function delete(): void
    {
        Security::requireAdmin();
        Security::checkCsrf();

        $userId = (int)($_POST['id'] ?? 0);

        if ($userId <= 0) {
            $_SESSION['flash_error'] = 'Ungültige Anfrage.';
            header('Location: /admin/users');
            exit;
        }

        // Ziel-User laden
        $target = Database::fetch(
            "SELECT
                u.id,
                u.username,
                r.level AS role_level
             FROM users u
             JOIN roles r ON r.id = u.role_id
             WHERE u.id = ?
             LIMIT 1",
            [$userId]
        );

        if (!$target) {
            $_SESSION['flash_error'] = 'Benutzer nicht gefunden.';
            header('Location: /admin/users');
            exit;
        }

        // Selbstschutz & RBAC
        if (
            $userId === ($_SESSION['user']['id'] ?? 0) ||
            ($target['role_level'] ?? 0) >= ($_SESSION['user']['role_level'] ?? 0)
        ) {
            http_response_code(403);
            View::render('errors/403', ['title' => 'Zugriff verweigert']);
            return;
        }

        Database::execute(
            "DELETE FROM users WHERE id = ?",
            [$userId]
        );

        AuditService::log(
            'user.deleted',
            'user',
            $userId,
            ['username' => $target['username']],
            null
        );

        $_SESSION['flash_success'] = 'Benutzer gelöscht.';
        header('Location: /admin/users');
        exit;
    }
}
