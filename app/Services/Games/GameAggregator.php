<?php

class GameAggregator
{
    public function sync(): void
    {
        $sources = array_merge(
            (new SteamProvider())->getGames(),
            (new TwitchDetector())->detect()
        );

        foreach ($sources as $game) {
            $exists = Database::fetch(
                "SELECT id, confirmed FROM games WHERE name = ? AND provider = ?",
                [$game['name'], $game['provider']]
            );

            if ($exists && $exists['confirmed']) {
                continue;
            }

            Database::execute(
                "INSERT INTO games
                 (name, provider, provider_id, hours, last_played, cover, is_auto, confirmed)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?)
                 ON DUPLICATE KEY UPDATE
                 hours = VALUES(hours),
                 last_played = VALUES(last_played),
                 cover = COALESCE(VALUES(cover), cover)",
                [
                    $game['name'],
                    $game['provider'],
                    $game['provider_id'],
                    $game['hours'],
                    $game['last_played'],
                    $game['cover'],
                    $game['is_auto'],
                    $game['confirmed']
                ]
            );
        }
    }
}
