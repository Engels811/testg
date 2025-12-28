<?php
final class Auth
{
    public static function user(): ?array
    {
        return $_SESSION['user'] ?? null;
    }

    public static function check(array $roles): void
    {
        if (
            empty($_SESSION['user']) ||
            !in_array($_SESSION['user']['role'], $roles, true)
        ) {
            http_response_code(403);
            View::render('errors/403', [
                'title' => 'Zugriff verweigert'
            ]);
            exit;
        }
    }
}
