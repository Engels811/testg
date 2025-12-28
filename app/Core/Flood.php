<?php

class Flood
{
    /**
     * Prüft Flooding (z. B. Spam-Posts)
     *
     * @param string $key     Eindeutiger Schlüssel (z. B. reply_12)
     * @param int    $seconds Mindestabstand in Sekunden
     */
    public static function check(string $key, int $seconds): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        $now = time();
        $sessionKey = 'flood_' . $key;

        if (isset($_SESSION[$sessionKey])) {
            $diff = $now - (int)$_SESSION[$sessionKey];

            if ($diff < $seconds) {
                http_response_code(429);
                View::render('errors/429', [
                    'title'   => 'Zu viele Anfragen',
                    'message' => 'Bitte warte einen Moment, bevor du erneut postest.'
                ]);
                exit;
            }
        }

        $_SESSION[$sessionKey] = $now;
    }
}
