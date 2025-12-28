<?php
/**
 * Twitch Schedule API – FINAL (OHNE CONFIG)
 * Engels811 Network
 */

header('Content-Type: application/json');

/* =====================================================
   KONFIGURATION – HIER DIREKT EINTRAGEN
===================================================== */

const TWITCH_CLIENT_ID = '8u1jy3xra4xdubmwidnhml1bx0yu09';
const TWITCH_SECRET   = 'cm0q9af199fhr4f0hyctndjbh95m1x';
const TWITCH_LOGIN    = 'engels811';

/* =====================================================
   ACCESS TOKEN HOLEN
===================================================== */

function getTwitchToken(): string
{
    $url = 'https://id.twitch.tv/oauth2/token';

    $params = http_build_query([
        'client_id'     => TWITCH_CLIENT_ID,
        'client_secret' => TWITCH_SECRET,
        'grant_type'    => 'client_credentials'
    ]);

    $response = file_get_contents($url . '?' . $params);
    $data = json_decode($response, true);

    return $data['access_token'] ?? '';
}

$token = getTwitchToken();

if (!$token) {
    echo json_encode(['error' => 'token_failed']);
    exit;
}

/* =====================================================
   USER ID HOLEN
===================================================== */

$ch = curl_init('https://api.twitch.tv/helix/users?login=' . TWITCH_LOGIN);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => [
        'Client-ID: ' . TWITCH_CLIENT_ID,
        'Authorization: Bearer ' . $token
    ]
]);

$userResponse = curl_exec($ch);
curl_close($ch);

$userData = json_decode($userResponse, true);
$userId = $userData['data'][0]['id'] ?? null;

if (!$userId) {
    echo json_encode(['error' => 'user_not_found']);
    exit;
}

/* =====================================================
   STREAM SCHEDULE HOLEN
===================================================== */

$ch = curl_init('https://api.twitch.tv/helix/schedule?broadcaster_id=' . $userId);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => [
        'Client-ID: ' . TWITCH_CLIENT_ID,
        'Authorization: Bearer ' . $token
    ]
]);

$scheduleResponse = curl_exec($ch);
curl_close($ch);

$data = json_decode($scheduleResponse, true);
$segments = $data['data']['segments'] ?? [];

/* =====================================================
   AUFBEREITEN
===================================================== */

$events = [];
$next   = null;

$now = new DateTime('now', new DateTimeZone('UTC'));

foreach ($segments as $segment) {

    $start = new DateTime($segment['start_time']);
    $end   = new DateTime($segment['end_time']);

    $event = [
        'start' => $start->format(DateTime::ATOM),
        'end'   => $end->format(DateTime::ATOM),
        'title' => $segment['title'] ?: 'Live Stream'
    ];

    $events[] = $event;

    // 🔥 Nächster zukünftiger Stream (für Countdown)
    if ($start > $now && $next === null) {
        $next = $event;
    }
}

/* =====================================================
   RESPONSE
===================================================== */

echo json_encode([
    'count'  => count($events),
    'next'   => $next,
    'events' => $events
]);
