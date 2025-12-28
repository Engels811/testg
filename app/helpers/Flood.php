<?php

class Flood
{
    public static function check(string $key, int $seconds): void
    {
        $now = time();

        if (!isset($_SESSION['_flood'])) {
            $_SESSION['_flood'] = [];
        }

        if (
            isset($_SESSION['_flood'][$key]) &&
            ($now - $_SESSION['_flood'][$key]) < $seconds
        ) {
            http_response_code(429);
            View::render('errors/429', [
                'title' => 'Zu viele Anfragen',
                'message' => 'Bitte warte einen Moment, bevor du erneut postest.'
            ]);
            exit;
        }

        $_SESSION['_flood'][$key] = $now;
    }
}
