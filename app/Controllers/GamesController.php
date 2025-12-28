<?php
declare(strict_types=1);

class GamesController
{
    /**
     * GET /games
     * Übersicht aller Spiele
     */
    public function index(): void
    {
        // Spiele aus Datenbank laden
        try {
            $games = Database::fetchAll(
                "SELECT
                    id, name, slug, provider, provider_id, category,
                    CAST(COALESCE(hours_override, hours, 0) AS DECIMAL(10,1)) AS hours,
                    cover, is_top, confirmed, last_played
                 FROM games
                 WHERE confirmed = 1
                 ORDER BY is_top DESC, hours DESC, name ASC"
            );
        } catch (Throwable $e) {
            error_log('Games Loading Error: ' . $e->getMessage());
            $games = [];
        }

        View::render(
            'games/index',
            [
                'title' => 'Alle Spiele',
                'games' => $games  // ← WICHTIG: Daten an View übergeben
            ]
        );
    }

    /**
     * GET /games/{slug}
     * Detailseite eines Spiels (mit externen Daten)
     */
    public function show(string $slug): void
    {
        // ===============================
        // SPIEL LADEN (NUR BESTÄTIGTE)
        // ===============================
        $game = Database::fetch(
            "SELECT * FROM games WHERE slug = ? AND confirmed = 1",
            [$slug]
        );

        if (!$game) {
            http_response_code(404);
            View::render('errors/404', [
                'title' => 'Spiel nicht gefunden'
            ]);
            return;
        }

        // ===============================
        // EXTERNE DATEN (CACHE)
        // ===============================
        $needsSync = empty($game['last_synced'])
            || strtotime($game['last_synced']) < strtotime('-7 days');

        if ($needsSync) {
            try {
                $this->syncExternalGameData($game);
                $game = Database::fetch(
                    "SELECT * FROM games WHERE id = ?",
                    [$game['id']]
                );
            } catch (Throwable $e) {
                // Fehler ignorieren – Seite MUSS weiterlaufen
            }
        }

        // ===============================
        // TWITCH LIVE STATUS (SAFE)
        // ===============================
        $isLive = false;

        if (class_exists('TwitchService')) {
            try {
                $isLive = (new TwitchService())->isLive();
            } catch (Throwable $e) {
                $isLive = false;
            }
        }

        // ===============================
        // VIEW
        // ===============================
        View::render(
            'games/show',
            [
                'title'  => $game['name'],
                'game'   => $game,
                'isLive' => $isLive
            ]
        );
    }

    /**
     * Holt externe Spieldaten (RAWG)
     * Wird gecached (last_synced)
     */
    private function syncExternalGameData(array $game): void
    {
        if (!defined('RAWG_API_KEY') || !RAWG_API_KEY) {
            return;
        }

        $url = 'https://api.rawg.io/api/games?search='
             . urlencode($game['name'])
             . '&page_size=1'
             . '&key=' . RAWG_API_KEY;

        $context = stream_context_create([
            'http' => [
                'timeout' => 4
            ]
        ]);

        $json = @file_get_contents($url, false, $context);
        if (!$json) {
            return;
        }

        $data = json_decode($json, true);
        if (empty($data['results'][0])) {
            return;
        }

        $g = $data['results'][0];

        Database::execute(
            "UPDATE games SET
                description   = ?,
                genres        = ?,
                release_date  = ?,
                metacritic    = ?,
                last_synced   = NOW()
             WHERE id = ?",
            [
                $g['description_raw'] ?? null,
                !empty($g['genres'])
                    ? implode(', ', array_column($g['genres'], 'name'))
                    : null,
                $g['released'] ?? null,
                $g['metacritic'] ?? null,
                $game['id']
            ]
        );
    }
}