<?php

abstract class BaseController
{
    protected function requireLogin(): void
    {
        if (empty($_SESSION['user'])) {
            header('Location: /login');
            exit;
        }
    }

    protected function requireAdmin(): void
    {
        if (
            empty($_SESSION['user']) ||
            ($_SESSION['user']['role'] ?? null) !== 'admin'
        ) {
            http_response_code(403);
            View::render('errors/403', [
                'title' => 'Zugriff verweigert'
            ]);
            exit;
        }
    }
}
