<?php

class TwitchAutoDetector
{
    public function detect(): void
    {
        $twitch = new TwitchService();
        $stream = $twitch->getLiveStream();

        if (!$stream || empty($stream['game_name'])) {
            return;
        }

        $gameName = trim($stream['game_name']);

        $resolver = new GameAliasResolver();
        $game = $resolver->resolve($gameName);

        // ✔ Spiel existiert → last_played aktualisieren
        if ($game) {
            Database::execute(
                "UPDATE games SET last_played = ? WHERE id = ?",
                [time(), $game['id']]
            );
            return;
        }

        // ❗ Neues Spiel → unbestätigt anlegen
        Database::execute(
            "INSERT IGNORE INTO games
             (name, slug, provider, hours, confirmed, is_top)
             VALUES (?, ?, 'custom', 0, 0, 0)",
            [
                $gameName,
                $this->slugify($gameName)
            ]
        );
    }

    private function slugify(string $name): string
    {
        return strtolower(
            trim(
                preg_replace(
                    '/[^a-z0-9]+/',
                    '-',
                    iconv('UTF-8', 'ASCII//TRANSLIT', $name)
                ),
                '-'
            )
        );
    }
}
