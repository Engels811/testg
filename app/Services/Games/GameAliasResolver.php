<?php

class GameAliasResolver
{
    public function resolve(string $name): ?array
    {
        // 1. Direkter Match
        $game = Database::fetch(
            "SELECT * FROM games WHERE LOWER(name) = LOWER(?) AND confirmed = 1",
            [$name]
        );
        if ($game) {
            return $game;
        }

        // 2. Alias Match
        $alias = Database::fetch(
            "SELECT g.*
             FROM game_aliases a
             JOIN games g ON g.id = a.game_id
             WHERE LOWER(a.alias) = LOWER(?) AND g.confirmed = 1",
            [$name]
        );
        if ($alias) {
            return $alias;
        }

        return null;
    }
}
