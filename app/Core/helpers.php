<?php
declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Allgemeine Helper-Funktionen
|--------------------------------------------------------------------------
| Zentrale Sammlung kleiner Hilfsfunktionen,
| die global im Projekt verfügbar sind.
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
   CSRF (EINZIGE WAHRHEIT = Security)
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
 * Admin = role_level >= 100
 */
if (!function_exists('is_admin')) {
    function is_admin(): bool
    {
        return
            isset($_SESSION['user']['role_level']) &&
            (int)$_SESSION['user']['role_level'] >= 100;
    }
}

/**
 * Team = Moderator + Admin
 * role_level >= 50
 */
if (!function_exists('is_team')) {
    function is_team(): bool
    {
        return
            isset($_SESSION['user']['role_level']) &&
            (int)$_SESSION['user']['role_level'] >= 50;
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
