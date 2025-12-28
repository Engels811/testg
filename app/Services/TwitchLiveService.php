<?php

class TwitchLiveService
{
    public function getLiveGame(): ?string
    {
        if (!class_exists('TwitchService')) {
            return null;
        }

        $twitch = new TwitchService();
        $stream = $twitch->getLiveStream();

        if (!$stream || empty($stream['game_name'])) {
            return null;
        }

        return $stream['game_name'];
    }
}
