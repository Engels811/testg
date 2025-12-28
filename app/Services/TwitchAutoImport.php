<?php
declare(strict_types=1);

class TwitchAutoImport
{
    private string $lockFile;
    private int $interval = 300; // 5 Minuten

    public function __construct()
    {
        $this->lockFile = BASE_PATH . '/storage/cache/twitch_auto_import.json';
    }

    public function run(): void
    {
        // ==============================
        // ⏱ Zeit-Lock (Anti-Spam)
        // ==============================
        if (file_exists($this->lockFile)) {
            $last = json_decode(file_get_contents($this->lockFile), true);

            if (
                is_array($last) &&
                isset($last['time']) &&
                time() - (int)$last['time'] < $this->interval
            ) {
                return;
            }
        }

        // Lock aktualisieren
        file_put_contents($this->lockFile, json_encode([
            'time' => time()
        ], JSON_PRETTY_PRINT));

        // ==============================
        // 🎮 Twitch VODs holen
        // ==============================
        try {
            $twitch = new TwitchService();
            $vods   = $twitch->getVods(10);
        } catch (Throwable $e) {
            return; // Twitch nicht erreichbar → still abbrechen
        }

        if (empty($vods)) {
            return;
        }

        // ==============================
        // 📥 In DB importieren
        // ==============================
        foreach ($vods as $vod) {

            if (empty($vod['id'])) {
                continue;
            }

            // Duplikat prüfen
            $exists = Database::fetch(
                "SELECT id FROM videos WHERE twitch_video_id = ?",
                [$vod['id']]
            );

            if ($exists) {
                continue;
            }

            // Thumbnail auflösen
            $thumbnail = '';
            if (!empty($vod['thumbnail_url'])) {
                $thumbnail = str_replace(
                    ['%{width}', '%{height}'],
                    ['640', '360'],
                    $vod['thumbnail_url']
                );
            }

            // Twitch Embed URL
            $embedUrl =
                'https://player.twitch.tv/?video=' . $vod['id'] .
                '&parent=engels811-ttv.de';

            // Insert
            Database::execute(
                "INSERT INTO videos
                    (title, url, thumbnail, source, twitch_video_id,
                     duration_seconds, view_count, is_pinned,
                     published_at, created_at)
                 VALUES
                    (?, ?, ?, 'twitch', ?, 0, ?, 1, ?, NOW())",
                [
                    $vod['title'] ?? 'Twitch Video',
                    $embedUrl,
                    $thumbnail,
                    $vod['id'],
                    (int)($vod['view_count'] ?? 0),
                    date('Y-m-d H:i:s', strtotime($vod['created_at'] ?? 'now'))
                ]
            );
        }
    }
}
