<?php
declare(strict_types=1);

final class App
{
    public function run(): void
    {
        /* =========================================================
           SESSION (ZENTRAL & SICHER)
        ========================================================= */
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start([
                'use_strict_mode' => true,
                'cookie_httponly' => true,
                'cookie_samesite' => 'Lax',
                // 'cookie_secure' => true, // aktivieren, wenn HTTPS
            ]);
        }

        /* =========================================================
           ROUTING
        ========================================================= */
        try {
            $router = new Router();
            $router->dispatch();
        } catch (Throwable $e) {
            // ❗ Keine internen Details ausgeben
            http_response_code(500);
            View::render('errors/500', [
                'title' => 'Interner Serverfehler'
            ]);
        }
    }
}
