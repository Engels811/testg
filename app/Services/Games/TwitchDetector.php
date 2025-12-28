<?php

class TwitchDetector
{
    public function detect(): array
    {
        $twitch = new TwitchService();
        $stream = $twitch->getLiveStream();

        if (!$stream || empty($stream['game_name'])) {
            return [];
        }

        return [[
            'name'        => $stream['game_name'],
            'provider'    => 'custom',
            'provider_id' => null,
            'hours'       => 0,
            'last_played' => time(),
            'cover'       => null,
            'is_auto'     => 0,
            'confirmed'   => 0
        ]];
    }
}
