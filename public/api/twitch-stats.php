<?php
/**
 * Twitch Stats API – FINAL (KORREKT)
 */

ini_set('display_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

/* ===============================
   CONFIG
================================ */
const TWITCH_CLIENT_ID = '8u1jy3xra4xdubmwidnhml1bx0yu09';
const TWITCH_SECRET   = 'cm0q9af199fhr4f0hyctndjbh95m1x';
const TWITCH_USER     = 'engels811';

/* ===============================
   DB CONNECTION
================================ */
$pdo = new PDO(
    'mysql:host=localhost;dbname=mcdyoxum_engels811;charset=utf8mb4',
    'mcdyoxum_engels811',
    'Schatz@14102013',
    [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]
);

/* ===============================
   CURL HELPER
================================ */
function curlJson(string $url, array $headers = [], array $post = null): array
{
    $ch = curl_init($url);

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);

    if ($post !== null) {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));
    }

    if (!empty($headers)) {
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    }

    $res = curl_exec($ch);

    if ($res === false) {
        throw new Exception('cURL Fehler: ' . curl_error($ch));
    }

    curl_close($ch);
    return json_decode($res, true) ?? [];
}

/* ===============================
   TWITCH TOKEN (POST!)
================================ */
$tokenData = curlJson(
    'https://id.twitch.tv/oauth2/token',
    ['Content-Type: application/x-www-form-urlencoded'],
    [
        'client_id'     => TWITCH_CLIENT_ID,
        'client_secret' => TWITCH_SECRET,
        'grant_type'    => 'client_credentials'
    ]
);

if (empty($tokenData['access_token'])) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Twitch Token Fehler',
        'debug' => $tokenData
    ]);
    exit;
}

$token = $tokenData['access_token'];

$headers = [
    'Client-ID: ' . TWITCH_CLIENT_ID,
    'Authorization: Bearer ' . $token
];

/* ===============================
   USER ID
================================ */
$user = curlJson(
    'https://api.twitch.tv/helix/users?login=' . TWITCH_USER,
    $headers
);

if (empty($user['data'][0]['id'])) {
    http_response_code(404);
    echo json_encode(['error' => 'Twitch User nicht gefunden']);
    exit;
}

$userId = $user['data'][0]['id'];

/* ===============================
   FOLLOWERS
================================ */
$followers = curlJson(
    "https://api.twitch.tv/helix/channels/followers?broadcaster_id=$userId",
    $headers
)['total'] ?? 0;

/* ===============================
   LIVE STATUS
================================ */
$stream = curlJson(
    "https://api.twitch.tv/helix/streams?user_id=$userId",
    $headers
);

$isLive  = !empty($stream['data']);
$viewers = $stream['data'][0]['viewer_count'] ?? 0;

/* ===============================
   VIDEOS
================================ */
$videos = curlJson(
    "https://api.twitch.tv/helix/videos?user_id=$userId&type=archive",
    $headers
);

$videoCount = count($videos['data'] ?? []);

/* ===============================
   STREAM HOURS (DB)
================================ */
$totalMinutes = $pdo->query(
    "SELECT SUM(duration_minutes) AS total FROM stream_logs"
)->fetch()['total'] ?? 0;

$totalHours = floor($totalMinutes / 60);

/* ===============================
   OUTPUT
================================ */
echo json_encode([
    'followers' => $followers,
    'videos'   => $videoCount,
    'live'     => $isLive,
    'viewers'  => $viewers,
    'hours'    => $totalHours . '+'
]);
