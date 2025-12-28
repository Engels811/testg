<?php
declare(strict_types=1);

/**
 * Engels811 Network – Games API (FINAL & STABIL)
 */

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

define('BASE_PATH', dirname(__DIR__, 2));
require_once BASE_PATH . '/app/Core/Database.php';

try {
    $rows = Database::fetchAll(
        "SELECT
            id,
            name,
            slug,
            provider,
            provider_id,
            category,
            CAST(COALESCE(hours_override, hours, 0) AS DECIMAL(10,1)) AS hours,
            cover,
            is_top,
            confirmed,
            last_played
         FROM games
         WHERE confirmed = 1
         ORDER BY is_top DESC, hours DESC, name ASC"
    );

    $games = [];

    foreach ($rows as $g) {
        $games[] = [
            'id'          => (int)$g['id'],
            'name'        => (string)$g['name'],
            'slug'        => (string)$g['slug'],
            'provider'    => (string)$g['provider'],
            'provider_id' => $g['provider_id'] !== null ? (int)$g['provider_id'] : null,
            'category'    => $g['category'],
            'hours'       => (float)$g['hours'],
            'cover'       => $g['cover'] ?: null,
            'is_top'      => (int)$g['is_top'],
            'confirmed'   => (int)$g['confirmed'],
            'last_played' => $g['last_played'] !== null ? (int)$g['last_played'] : null
        ];
    }

    echo json_encode([
        'success' => true,
        'data'    => $games
    ], JSON_UNESCAPED_UNICODE);

} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error'   => 'Games API Fehler'
    ]);
}

exit;
