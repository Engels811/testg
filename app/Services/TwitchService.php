<?php

class TwitchService
{
    private string $clientId = 'p32ygw5yhgm9l3oxmpok9tb5b60q2n';
    private string $clientSecret = '';
    private string $username = 'engels811';

    private string $streamCacheFile;
    private string $tokenCacheFile;

    private int $streamCacheTtl = 60;     // Sekunden (Live-Status)
    private int $tokenSafetyGap = 60;     // Sekunden vor Ablauf erneuern

    public function __construct()
    {
        if (!defined('BASE_PATH')) {
            define('BASE_PATH', dirname(__DIR__, 2));
        }

        $cacheDir = BASE_PATH . '/storage/cache';

        if (!is_dir($cacheDir)) {
            mkdir($cacheDir, 0755, true);
        }

        $this->streamCacheFile = $cacheDir . '/twitch_stream.json';
        $this->tokenCacheFile  = $cacheDir . '/twitch_token.json';
    }

    /* =========================================================
       PUBLIC: LIVE STREAM (ZENTRAL)
       Rückgabe:
       [
         live         => bool,
         game_name    => ?string,
         title        => ?string,
         viewer_count => int,
         started_at   => ?string
       ]
    ========================================================= */

    public function getLiveStream(): array
    {
        // Stream-Cache prüfen
        if (file_exists($this->streamCacheFile)) {
            $cache = json_decode(file_get_contents($this->streamCacheFile), true);
            if ($cache && time() - $cache['time'] < $this->streamCacheTtl) {
                return $cache['data'];
            }
        }

        $token = $this->getToken();
        if (!$token) {
            return ['live' => false];
        }

        $context = stream_context_create([
            'http' => [
                'method'  => 'GET',
                'header'  => [
                    'Client-ID: ' . $this->clientId,
                    'Authorization: Bearer ' . $token
                ],
                'timeout' => 5
            ]
        ]);

        $url = 'https://api.twitch.tv/helix/streams?user_login=' . $this->username;
        $response = @file_get_contents($url, false, $context);

        if (!$response) {
            return ['live' => false];
        }

        $json = json_decode($response, true);

        // Offline
        if (empty($json['data'][0])) {
            $data = ['live' => false];
        } else {
            $s = $json['data'][0];

            $data = [
                'live'         => true,
                'game_name'    => $s['game_name']    ?? null,
                'title'        => $s['title']        ?? null,
                'viewer_count' => (int) ($s['viewer_count'] ?? 0),
                'started_at'   => $s['started_at']   ?? null
            ];
        }

        // Cache schreiben
        file_put_contents($this->streamCacheFile, json_encode([
            'time' => time(),
            'data' => $data
        ]));

        return $data;
    }

    /* =========================================================
       PUBLIC: BOOLEAN CHECK
    ========================================================= */

    public function isLive(): bool
    {
        return $this->getLiveStream()['live'] === true;
    }

    /* =========================================================
       PUBLIC: VODS
    ========================================================= */

    public function getVods(int $limit = 20): array
    {
        $token  = $this->getToken();
        $userId = $this->getUserId();

        if (!$token || !$userId) {
            return [];
        }

        $context = stream_context_create([
            'http' => [
                'method'  => 'GET',
                'header'  => [
                    'Client-ID: ' . $this->clientId,
                    'Authorization: Bearer ' . $token
                ],
                'timeout' => 6
            ]
        ]);

        $url = sprintf(
            'https://api.twitch.tv/helix/videos?user_id=%s&type=archive&first=%d',
            $userId,
            $limit
        );

        $response = @file_get_contents($url, false, $context);
        if (!$response) {
            return [];
        }

        $json = json_decode($response, true);
        return $json['data'] ?? [];
    }

    /* =========================================================
       INTERNAL: USER ID
    ========================================================= */

    private function getUserId(): ?string
    {
        $token = $this->getToken();
        if (!$token) {
            return null;
        }

        $context = stream_context_create([
            'http' => [
                'method'  => 'GET',
                'header'  => [
                    'Client-ID: ' . $this->clientId,
                    'Authorization: Bearer ' . $token
                ],
                'timeout' => 4
            ]
        ]);

        $url = 'https://api.twitch.tv/helix/users?login=' . $this->username;
        $response = @file_get_contents($url, false, $context);

        if (!$response) {
            return null;
        }

        $json = json_decode($response, true);
        return $json['data'][0]['id'] ?? null;
    }

    /* =========================================================
       INTERNAL: OAUTH TOKEN (CACHED)
    ========================================================= */

    private function getToken(): ?string
    {
        // Token-Cache prüfen
        if (file_exists($this->tokenCacheFile)) {
            $cache = json_decode(file_get_contents($this->tokenCacheFile), true);
            if ($cache && time() < $cache['expires']) {
                return $cache['token'];
            }
        }

        $url = 'https://id.twitch.tv/oauth2/token';
        $params = http_build_query([
            'client_id'     => $this->clientId,
            'client_secret' => $this->clientSecret,
            'grant_type'    => 'client_credentials'
        ]);

        $response = @file_get_contents($url . '?' . $params);
        if (!$response) {
            return null;
        }

        $json = json_decode($response, true);
        if (empty($json['access_token'])) {
            return null;
        }

        $expiresIn = (int) ($json['expires_in'] ?? 3600);

        file_put_contents($this->tokenCacheFile, json_encode([
            'token'   => $json['access_token'],
            'expires' => time() + $expiresIn - $this->tokenSafetyGap
        ]));

        return $json['access_token'];
    }
}
