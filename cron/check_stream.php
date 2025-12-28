<?php

$isLive = $isLiveFromTwitch; // true / false aus API
$now = date('Y-m-d H:i:s');

$last = Database::fetch(
    "SELECT * FROM stream_logs ORDER BY id DESC LIMIT 1"
);

if ($isLive && (!$last || $last['ended_at'] !== null)) {
    // 🔴 Stream START
    Database::insert(
        "INSERT INTO stream_logs (started_at) VALUES (?)",
        [$now]
    );
}

if (!$isLive && $last && $last['ended_at'] === null) {
    // ⚫ Stream ENDE
    $start = strtotime($last['started_at']);
    $end   = time();
    $mins  = floor(($end - $start) / 60);

    Database::update(
        "UPDATE stream_logs 
         SET ended_at = ?, duration_minutes = ?
         WHERE id = ?",
        [$now, $mins, $last['id']]
    );
}
