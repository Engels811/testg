<?php
header('Content-Type: application/json');

function safe(PDO $pdo, string $sql): int {
    try {
        return (int)($pdo->query($sql)->fetchColumn() ?? 0);
    } catch (Throwable $e) {
        return 0;
    }
}

try {
    $pdo = new PDO(
        'mysql:host=localhost;dbname=mcdyoxum_engels811;charset=utf8mb4',
        'mcdyoxum_engels811',
        'Schatz@14102013',
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]
    );

    echo json_encode([
        'streamHours'       => floor(safe($pdo, "SELECT SUM(minutes) FROM stream_logs") / 60),
        'communityMembers' => safe($pdo, "SELECT COUNT(*) FROM users"),
        'aiImagesCount'    => safe($pdo, "SELECT COUNT(*) FROM ai_images"),
        'videosCount'      => safe($pdo, "SELECT COUNT(*) FROM videos")
    ]);
} catch (Throwable $e) {
    http_response_code(200);
    echo json_encode([
        'streamHours'       => 0,
        'communityMembers'  => 0,
        'aiImagesCount'     => 0,
        'videosCount'       => 0,
        'error'             => 'unavailable'
    ]);
}
