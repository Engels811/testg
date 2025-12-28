<?php
/**
 * Steam Games API – cURL SAFE
 * Liefert ALLE Steam-Spiele (keine Filter)
 * Funktioniert auch wenn allow_url_fopen = OFF
 */

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json; charset=utf-8');

// ===============================
// KONFIGURATION (HIER SETZEN)
// ===============================
$STEAM_API_KEY = '755830B344EE975F9A4378219EC2705D';
$STEAM_ID     = '76561198413304736';

// ===============================
// VALIDIERUNG
// ===============================
if (!$STEAM_API_KEY || strpos($STEAM_API_KEY, 'HIER_') === 0) {
    http_response_code(500);
    echo json_encode(['error' => 'Steam API Key fehlt']);
    exit;
}

if (!$STEAM_ID || strpos($STEAM_ID, 'HIER_') === 0) {
    http_response_code(500);
    echo json_encode(['error' => 'SteamID64 fehlt']);
    exit;
}

// ===============================
// STEAM API URL
// ===============================
$url =
    'https://api.steampowered.com/IPlayerService/GetOwnedGames/v1/?' .
    'key=' . urlencode($STEAM_API_KEY) .
    '&steamid=' . urlencode($STEAM_ID) .
    '&include_appinfo=true' .
    '&include_played_free_games=1';

// ===============================
// cURL REQUEST
// ===============================
$ch = curl_init($url);

curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT => 15,
    CURLOPT_CONNECTTIMEOUT => 10,
    CURLOPT_SSL_VERIFYPEER => true,
    CURLOPT_SSL_VERIFYHOST => 2,
    CURLOPT_USERAGENT => 'Engels811-Steam-API'
]);

$response = curl_exec($ch);

if ($response === false) {
    http_response_code(500);
    echo json_encode([
        'error' => 'cURL Fehler',
        'detail' => curl_error($ch)
    ]);
    curl_close($ch);
    exit;
}

$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode !== 200) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Steam API HTTP Fehler',
        'http_code' => $httpCode
    ]);
    exit;
}

// ===============================
// JSON PARSEN
// ===============================
$data = json_decode($response, true);

if (!isset($data['response']['games'])) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Ungültige Steam-Antwort',
        'raw' => $data
    ]);
    exit;
}

// ===============================
// ERFOLG
// ===============================
echo json_encode(
    $data['response']['games'],
    JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT
);
