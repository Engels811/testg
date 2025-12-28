<?php
declare(strict_types=1);

final class Security
{
    /* =========================================================
       ROLLEN-DEFINITIONEN
    ========================================================= */

    private const TEAM_ROLES = [
        'support',
        'moderator',
        'admin',
        'superadmin',
        'owner',
    ];

    private const ADMIN_ROLES = [
        'admin',
        'superadmin',
        'owner',
    ];

    private const MODERATOR_ROLES = [
        'moderator',
        'admin',
        'superadmin',
        'owner',
    ];

    private const SUPERADMIN_ROLES = [
        'superadmin',
        'owner',
    ];

    /* =========================================================
       AUTH
    ========================================================= */

    public static function requireAuth(): void
    {
        if (empty($_SESSION['user'])) {
            header('Location: /login');
            exit;
        }
    }

    public static function requireTeam(): void
    {
        self::requireRole(self::TEAM_ROLES);
    }

    public static function requireAdmin(): void
    {
        self::requireRole(self::ADMIN_ROLES);
    }

    public static function requireModerator(): void
    {
        self::requireRole(self::MODERATOR_ROLES);
    }

    public static function requireSuperadmin(): void
    {
        self::requireRole(self::SUPERADMIN_ROLES);
    }

    public static function requireOwner(): void
    {
        self::requireAuth();

        if ((int)($_SESSION['user']['id'] ?? 0) !== 1) {
            self::forbidden();
        }
    }

    /* =========================================================
       ROLE CHECK (INTERN)
    ========================================================= */

    private static function requireRole(array $allowedRoles): void
    {
        self::requireAuth();

        $role = $_SESSION['user']['role'] ?? null;

        if (!$role || !in_array($role, $allowedRoles, true)) {
            self::forbidden();
        }
    }

    /* =========================================================
       CSRF
    ========================================================= */

    public static function csrf(): string
    {
        if (empty($_SESSION['_csrf'])) {
            $_SESSION['_csrf'] = bin2hex(random_bytes(32));
        }

        return $_SESSION['_csrf'];
    }

    public static function csrfField(): string
    {
        return '<input type="hidden" name="csrf" value="' . self::csrf() . '">';
    }

    public static function checkCsrf(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }

        $token = $_POST['csrf'] ?? '';

        if (
            empty($_SESSION['_csrf']) ||
            !hash_equals($_SESSION['_csrf'], $token)
        ) {
            http_response_code(419);
            View::render('errors/419', [
                'title' => 'Sicherheitsüberprüfung fehlgeschlagen',
            ]);
            exit;
        }

        unset($_SESSION['_csrf']);
    }

    /* =========================================================
       FORBIDDEN
    ========================================================= */

    private static function forbidden(): void
    {
        http_response_code(403);
        View::render('errors/403', [
            'title' => 'Zugriff verweigert',
        ]);
        exit;
    }
}
