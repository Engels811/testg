<?php
declare(strict_types=1);

class SteamImportService
{
    private string $apiKey;
    private string $steamId;
    private string $lockFile;
    private int $interval = 1800; // 30 Minuten

    /**
     * Top-Spiele per AppID (sprachunabhängig)
     */
    private array $priorityAppIds = [
        218,     // Source SDK Base 2007 (FiveM Mapping)
        526870,  // Satisfactory
        578080,  // PUBG
        2139460, // Once Human
        1812450, // Bellwright
        346110,  // ARK
    ];

    public function __construct()
    {
        $apiKey  = env('STEAM_API_KEY');
        $steamId = env('STEAM_ID');

        if (empty($apiKey)) {
            throw new Exception(
                'STEAM_API_KEY ist nicht in der .env Datei konfiguriert. ' .
                'Hole ihn hier: https://steamcommunity.com/dev/apikey'
            );
        }

        if (empty($steamId)) {
            throw new Exception(
                'STEAM_ID ist nicht in der .env Datei konfiguriert. ' .
                'Finde sie hier: https://steamid.io/'
            );
        }

        $this->apiKey   = (string) $apiKey;
        $this->steamId  = (string) $steamId;
        $this->lockFile = BASE_PATH . '/storage/cache/steam_import.json';
    }

    /* =========================================================
       MAIN IMPORT
    ========================================================= */
    public function import(): array
    {
        if ($this->isLocked()) {
            return [
                'status'   => 'skipped',
                'reason'   => 'interval_lock',
                'message'  => 'Steam Import läuft nur alle 30 Minuten',
                'next_run' => $this->getNextRunTime()
            ];
        }

        $games = $this->fetchSteamGames();
        if (empty($games)) {
            return [
                'status'  => 'error',
                'message' => 'Keine Spiele von der Steam API empfangen',
            ];
        }

        $inserted = 0;
        $updated  = 0;
        $errors   = [];

        foreach ($games as $game) {
            try {
                $result = $this->upsertGame($game);
                $result === 'insert' ? $inserted++ : $updated++;
            } catch (Throwable $e) {
                $errors[] = [
                    'game'  => $game['name'] ?? 'unknown',
                    'error' => $e->getMessage()
                ];
            }
        }

        // FiveM immer erzwingen
        $this->forceFiveM();

        $this->writeLock();

        return [
            'status'    => 'success',
            'inserted'  => $inserted,
            'updated'   => $updated,
            'total'     => count($games),
            'errors'    => $errors,
            'timestamp' => date('Y-m-d H:i:s')
        ];
    }

    /* =========================================================
       STEAM API
    ========================================================= */
    private function fetchSteamGames(): array
    {
        $url = sprintf(
            'https://api.steampowered.com/IPlayerService/GetOwnedGames/v1/?key=%s&steamid=%s&include_appinfo=1&include_played_free_games=1',
            $this->apiKey,
            $this->steamId
        );

        $json = @file_get_contents($url);
        if (!$json) {
            return [];
        }

        $data = json_decode($json, true);
        if (!isset($data['response']['games'])) {
            return [];
        }

        return $data['response']['games'] ?? [];

    }

    /* =========================================================
       INSERT / UPDATE
    ========================================================= */
    private function upsertGame(array $g): string
    {
        $hours = round(($g['playtime_forever'] ?? 0) / 60, 1);
        $slug  = $this->slugify($g['name']);
        $isTop = in_array($g['appid'], $this->priorityAppIds, true) ? 1 : 0;

        $existing = Database::fetch(
            "SELECT id FROM games WHERE provider = 'steam' AND provider_id = ?",
            [$g['appid']]
        );

        if ($existing) {
            Database::execute(
                "UPDATE games SET
                    name = ?,
                    slug = ?,
                    hours = ?,
                    cover = ?,
                    is_top = ?,
                    last_played = ?,
                    updated_at = NOW()
                 WHERE id = ?",
                [
                    $g['name'],
                    $slug,
                    $hours,
                    $this->steamCover($g['appid']),
                    $isTop,
                    $this->formatTimestamp($g['rtime_last_played'] ?? null),
                    $existing['id']
                ]
            );
            return 'update';
        }

        Database::execute(
            "INSERT INTO games
                (name, slug, provider, provider_id, hours, cover, is_top, last_played, confirmed, created_at)
             VALUES
                (?, ?, 'steam', ?, ?, ?, ?, ?, 1, NOW())",
            [
                $g['name'],
                $slug,
                $g['appid'],
                $hours,
                $this->steamCover($g['appid']),
                $isTop,
                $this->formatTimestamp($g['rtime_last_played'] ?? null)
            ]
        );

        return 'insert';
    }

    /* =========================================================
       FiveM FIX
    ========================================================= */
    private function forceFiveM(): void
    {
        $cover = 'https://i.ibb.co/6JqqF6C3/Chat-GPT-Image-22-Dez-2025-21-02-44.png';

        $existing = Database::fetch(
            "SELECT id FROM games WHERE slug = 'fivem'"
        );

        if ($existing) {
            Database::execute(
                "UPDATE games SET
                    hours = GREATEST(hours, 3916),
                    cover = ?,
                    is_top = 1,
                    provider = 'custom',
                    updated_at = NOW()
                 WHERE id = ?",
                [$cover, $existing['id']]
            );
            return;
        }

        Database::execute(
            "INSERT INTO games
                (name, slug, provider, hours, cover, confirmed, is_top, created_at)
             VALUES
                ('FiveM', 'fivem', 'custom', 3916, ?, 1, 1, NOW())",
            [$cover]
        );
    }

    /* =========================================================
       HELPERS
    ========================================================= */
    private function steamCover(int $appid): string
    {
        return "https://cdn.cloudflare.steamstatic.com/steam/apps/{$appid}/header.jpg";
    }

    private function slugify(string $s): string
    {
        $s = strtolower(trim($s));
        $s = preg_replace('/[^a-z0-9]+/i', '-', $s);
        return trim($s, '-');
    }

    private function formatTimestamp(?int $ts): ?string
    {
        return $ts ? date('Y-m-d H:i:s', $ts) : null;
    }

    private function isLocked(): bool
    {
        if (!file_exists($this->lockFile)) {
            return false;
        }

        $data = json_decode(file_get_contents($this->lockFile), true);
        return $data && (time() - ($data['time'] ?? 0)) < $this->interval;
    }

    private function writeLock(): void
    {
        $dir = dirname($this->lockFile);
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        file_put_contents($this->lockFile, json_encode([
            'time' => time(),
            'date' => date('Y-m-d H:i:s')
        ]));
    }

    private function getNextRunTime(): string
    {
        if (!file_exists($this->lockFile)) {
            return 'now';
        }

        $data = json_decode(file_get_contents($this->lockFile), true);
        return date('Y-m-d H:i:s', ($data['time'] ?? time()) + $this->interval);
    }
}
