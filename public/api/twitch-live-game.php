<?php
require_once BASE_PATH . '/app/Services/TwitchLiveService.php';

header('Content-Type: application/json');

$game = (new TwitchLiveService())->getLiveGame();

echo json_encode([
    'live' => (bool)$game,
    'game' => $game
]);
