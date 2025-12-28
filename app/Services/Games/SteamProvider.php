<?php

class SteamProvider
{
    public function getGames(): array
    {
        $steam = new SteamService();
        $games = $steam->getAllGames(); // deine bestehende Methode

        return array_map(fn($g) => [
            'name'        => $g['name'],
            'provider'    => 'steam',
            'provider_id' => (string)$g['appid'],
            'hours'       => round(($g['playtime_forever'] ?? 0) / 60, 1),
            'last_played' => $g['rtime_last_played'] ?? null,
            'cover'       => $g['custom_logo']
                ?? "https://cdn.cloudflare.steamstatic.com/steam/apps/{$g['appid']}/header.jpg",
            'is_auto'     => 1,
            'confirmed'   => 1
        ], $games);
    }
}
