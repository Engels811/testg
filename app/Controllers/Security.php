<?php
declare(strict_types=1);

class Security
{
    /* =========================================================
       AUTH GUARDS (RBAC-KONFORM)
    ========================================================= */

    public static function requireAuth(): void
    {
        if (empty($_SESSION['user'])) {
            header('Location: /login');
            exit;
        }
    }

    /**
     * Admin-Bereich
     * role_level >= 100
     */
    public static function requireAdmin(): void
    {
        self::requireAuth();

        if (
            !isset($_SESSION['user']['role_level'])
            || $_SESSION['user']['role_level'] < 100
        ) {
            self::forbidden();
        }
    }

    /**
     * Team-Bereich (Moderator + Admin)
     * role_level >= 50
     */
    public static function requireTeam(): void
    {
        self::requireAuth();

        if (
            !isset($_SESSION['user']['role_level'])
            || $_SESSION['user']['role_level'] < 50
        ) {
            self::forbidden();
        }
    }

    /**
     * Owner (hart, nur User-ID 1)
     */
    public static function requireOwner(): void
    {
        self::requireAuth();

        if ((int)($_SESSION['user']['id'] ?? 0) !== 1) {
            self::forbidden();
        }
    }

    /* =========================================================
       CSRF – SINGLE SOURCE OF TRUTH
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
            empty($_SESSION['_csrf'])
            || !hash_equals($_SESSION['_csrf'], $token)
        ) {
            http_response_code(419);
            View::render('errors/419', [
                'title' => 'Sicherheitsüberprüfung fehlgeschlagen'
            ]);
            exit;
        }

        // Einmal-Token
        unset($_SESSION['_csrf']);
    }

    /* =========================================================
       INTERNAL
    ========================================================= */

    private static function forbidden(): void
    {
        http_response_code(403);
        View::render('errors/403', [
            'title' => 'Zugriff verweigert'
        ]);
        exit;
    }
}
