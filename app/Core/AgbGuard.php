<?php
declare(strict_types=1);

class AgbGuard
{
    public static function check(): void
    {
        // 🧠 Nicht eingeloggt → kein Zwang
        if (empty($_SESSION['user'])) {
            return;
        }

        $uri = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');

        // ✅ Erlaubte Routen (WICHTIG!)
        $allowed = [
            'login',
            'register',
            'logout',
            'agb',
            'agb/accept',
            'datenschutz'
        ];

        // Unterpfade erlauben (z. B. agb/…)
        foreach ($allowed as $path) {
            if ($uri === $path || str_starts_with($uri, $path . '/')) {
                return;
            }
        }

        // 🔎 User AGB-Status prüfen
        $user = Database::fetch(
            'SELECT agb_accepted_at, agb_version FROM users WHERE id = ?',
            [$_SESSION['user']['id']]
        );

        if (!$user || empty($user['agb_accepted_at'])) {
            header('Location: /agb/accept');
            exit;
        }

        // 📄 Aktuelle AGB-Version
        $current = Database::fetch(
            "SELECT version FROM cms_pages WHERE slug = 'agb'"
        );

        if (
            $current &&
            $user['agb_version'] !== $current['version']
        ) {
            header('Location: /agb/accept');
            exit;
        }
    }
}
