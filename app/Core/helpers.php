<?php
declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Allgemeine Helper-Funktionen
|--------------------------------------------------------------------------
*/

/* =========================================================
   BASE URL
========================================================= */

if (!function_exists('base_url')) {
    function base_url(string $path = ''): string
    {
        $scheme = (
            (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
                ? 'https'
                : 'http'
        );

        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';

        return rtrim($scheme . '://' . $host, '/') . '/' . ltrim($path, '/');
    }
}

/* =========================================================
   CSRF
========================================================= */

if (!function_exists('csrf_field')) {
    function csrf_field(): string
    {
        return Security::csrfField();
    }
}

/* =========================================================
   ESCAPING
========================================================= */

if (!function_exists('e')) {
    function e(?string $value): string
    {
        return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
    }
}

/* =========================================================
   REQUEST / INPUT
========================================================= */

if (!function_exists('old')) {
    function old(string $key, string $default = ''): string
    {
        if (!isset($_SESSION['form_data'])) {
            return e($default);
        }

        return e($_SESSION['form_data'][$key] ?? $default);
    }
}

/* =========================================================
   AUTH / USER
========================================================= */

if (!function_exists('auth')) {
    function auth(): ?array
    {
        return $_SESSION['user'] ?? null;
    }
}

if (!function_exists('is_logged_in')) {
    function is_logged_in(): bool
    {
        return isset($_SESSION['user']);
    }
}

/**
 * Support (support, moderator, admin, superadmin, owner)
 */
if (!function_exists('is_support')) {
    function is_support(): bool
    {
        $role = $_SESSION['user']['role'] ?? '';
        return in_array($role, ['support', 'moderator', 'admin', 'superadmin', 'owner'], true);
    }
}

/**
 * Moderator (moderator, admin, superadmin, owner)
 */
if (!function_exists('is_moderator')) {
    function is_moderator(): bool
    {
        $role = $_SESSION['user']['role'] ?? '';
        return in_array($role, ['moderator', 'admin', 'superadmin', 'owner'], true);
    }
}

/**
 * Admin (admin, superadmin, owner)
 */
if (!function_exists('is_admin')) {
    function is_admin(): bool
    {
        $role = $_SESSION['user']['role'] ?? '';
        return in_array($role, ['admin', 'superadmin', 'owner'], true);
    }
}

/**
 * Superadmin (superadmin, owner)
 */
if (!function_exists('is_superadmin')) {
    function is_superadmin(): bool
    {
        $role = $_SESSION['user']['role'] ?? '';
        return in_array($role, ['superadmin', 'owner'], true);
    }
}

/**
 * Owner (nur User-ID 1)
 */
if (!function_exists('is_owner')) {
    function is_owner(): bool
    {
        return ($_SESSION['user']['id'] ?? 0) === 1;
    }
}

/**
 * Team (support, moderator, admin, superadmin, owner)
 */
if (!function_exists('is_team')) {
    function is_team(): bool
    {
        $role = $_SESSION['user']['role'] ?? '';
        return in_array($role, ['support', 'moderator', 'admin', 'superadmin', 'owner'], true);
    }
}

/* =========================================================
   FLASH MESSAGES
========================================================= */

if (!function_exists('flash')) {
    function flash(string $key): ?string
    {
        if (!isset($_SESSION[$key])) {
            return null;
        }

        $value = $_SESSION[$key];
        unset($_SESSION[$key]);

        return e((string)$value);
    }
}

/* =========================================================
   DATE / TIME
========================================================= */

if (!function_exists('format_datetime')) {
    function format_datetime(?string $datetime, string $format = 'd.m.Y H:i'): string
    {
        if (!$datetime) {
            return '—';
        }

        return date($format, strtotime($datetime));
    }
}

/* =========================================================
   DEBUG (NUR DEVELOPMENT)
========================================================= */

if (!function_exists('dd')) {
    function dd(...$vars): void
    {
        if (!defined('APP_ENV') || APP_ENV !== 'development') {
            return;
        }

        echo '<pre>';
        foreach ($vars as $v) {
            var_dump($v);
        }
        echo '</pre>';
        exit;
    }
}
