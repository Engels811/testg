<?php
require __DIR__ . '/../bootstrap.php';

$STEAM_ID = '76561198413304736';
$API_KEY = '755830B344EE975F9A4378219EC2705D';

$url = "https://api.steampowered.com/IPlayerService/GetOwnedGames/v1/?
    key=$API_KEY&steamid=$STEAM_ID&include_appinfo=true";

$data = json_decode(file_get_contents($url), true);

foreach ($data['response']['games'] ?? [] as $g) {

    $hours = round(($g['playtime_forever'] ?? 0) / 60, 1);

    Database::execute(
        "INSERT INTO games
            (name, slug, provider, provider_id, hours, last_import, confirmed)
         VALUES
            (?, ?, 'steam', ?, ?, NOW(), 1)
         ON DUPLICATE KEY UPDATE
            hours = VALUES(hours),
            last_import = NOW()",
        [
            $g['name'],
            slugify($g['name']),
            $g['appid'],
            $hours
        ]
    );
}

echo "Steam Import OK\n";
