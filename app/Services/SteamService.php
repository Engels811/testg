<?php

class SteamService
{
    private string $apiKey = '755830B344EE975F9A4378219EC2705D';
    private string $steamId = '76561198413304736';

    // Top-Spiele per AppID (sprachunabhängig!)
    private $priorityAppIds = [
        218,     // FiveM (Source SDK Base 2007)
        526870,  // Satisfactory
        578080,  // PUBG
        2139460, // Once Human
        1812450, // Bellwright
        346110,  // ARK
    ];

    // Manuell erzwungene Spiele
    private $forcedGames = [
        218 => [
            'appid' => 218,
            'name'  => 'FiveM',
            'playtime_forever' => 3916 * 60,
            'custom_logo' => 'https://i.ibb.co/6JqqF6C3/Chat-GPT-Image-22-Dez-2025-21-02-44.png'
        ]
    ];

    public function getGamesOver10Hours()
    {
        $url =
            'https://api.steampowered.com/IPlayerService/GetOwnedGames/v1/?' .
            'key=' . $this->apiKey .
            '&steamid=' . $this->steamId .
            '&include_appinfo=true' .
            '&include_played_free_games=1';

        $json = file_get_contents($url);
        $data = json_decode($json, true);

        $games = [];

        foreach ($data['response']['games'] ?? [] as $game) {
            if (($game['playtime_forever'] ?? 0) <= 600) continue;

            $games[$game['appid']] = [
                'appid' => $game['appid'],
                'name'  => $game['name'],
                'playtime_forever' => $game['playtime_forever'],
                'custom_logo' => null
            ];
        }

        // FiveM erzwingen
        foreach ($this->forcedGames as $appid => $forced) {
            $games[$appid] = $forced;
        }

        // Aufteilen
        $priority = [];
        $rest = [];

        foreach ($games as $appid => $game) {
            if (in_array($appid, $this->priorityAppIds, true)) {
                $priority[] = $game;
            } else {
                $rest[] = $game;
            }
        }

        // Sortieren
        $sort = fn($a,$b) => $b['playtime_forever'] <=> $a['playtime_forever'];
        usort($priority, $sort);
        usort($rest, $sort);

        return array_merge($priority, $rest);
    }
}
