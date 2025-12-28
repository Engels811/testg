<?php

class VideoController
{
    /* =========================================================
       VIDEOS – ÜBERSICHT + KATEGORIE-FILTER
       (ALLE VIDEOS)
    ========================================================= */

    public function index(): void
    {
        $categories = Database::fetchAll(
            "SELECT * FROM video_categories ORDER BY title ASC"
        );

        $activeCategory = $_GET['cat'] ?? null;

        if ($activeCategory) {
            $videos = Database::fetchAll(
                "SELECT v.*
                 FROM videos v
                 JOIN video_categories c ON c.id = v.category_id
                 WHERE c.slug = ?
                 ORDER BY v.created_at DESC",
                [$activeCategory]
            );
        } else {
            $videos = Database::fetchAll(
                "SELECT * FROM videos
                 ORDER BY created_at DESC"
            );
        }

        View::render('videos/index', [
            'title'          => 'Videos',
            'videos'         => $videos,
            'categories'     => $categories,
            'activeCategory' => $activeCategory
        ]);
    }

    /* =========================================================
       STREAMS – NUR TWITCH VODS (AUTOMATISCH)
    ========================================================= */

    public function streams(): void
    {
        $videos = Database::fetchAll(
            "SELECT *
             FROM videos
             WHERE source = 'twitch'
             ORDER BY published_at DESC"
        );

        View::render('videos/streams', [
            'title'  => 'Streams',
            'videos' => $videos
        ]);
    }

    /* =========================================================
       VIDEO UPLOAD (MANUELL)
       - YouTube / Embed
       - KEINE Twitch-VODs hier!
    ========================================================= */

    public function upload(): void
    {
        if (empty($_SESSION['user'])) {
            http_response_code(403);
            View::render('errors/403', ['title' => 'Login erforderlich']);
            return;
        }

        $title      = trim($_POST['title'] ?? '');
        $urlRaw     = trim($_POST['url'] ?? '');
        $categoryId = (int)($_POST['category_id'] ?? 0);

        if ($title === '' || $urlRaw === '') {
            header('Location: /videos');
            exit;
        }

        // ❌ Twitch-VODs dürfen NICHT manuell hinzugefügt werden
        if (preg_match('~twitch\.tv/videos/\d+~', $urlRaw)) {
            header('Location: /videos?error=twitch_auto');
            exit;
        }

        $embedUrl = $this->toEmbedUrl($urlRaw);

        Database::execute(
            "INSERT INTO videos
                (user_id, title, url, category_id, source, created_at)
             VALUES (?, ?, ?, ?, 'manual', NOW())",
            [
                $_SESSION['user']['id'],
                $title,
                $embedUrl,
                $categoryId ?: null
            ]
        );

        header('Location: /videos');
        exit;
    }

    /* =========================================================
       VIDEO LÖSCHEN
       - NUR MANUELLE VIDEOS
       - KEINE TWITCH VODS
    ========================================================= */

    public function delete(): void
    {
        if (empty($_SESSION['user'])) {
            http_response_code(403);
            View::render('errors/403', ['title' => 'Login erforderlich']);
            return;
        }

        $id = (int)($_POST['id'] ?? 0);
        if ($id <= 0) {
            http_response_code(400);
            exit('Ungültige Anfrage');
        }

        $video = Database::fetch(
            "SELECT * FROM videos WHERE id = ?",
            [$id]
        );

        if (!$video) {
            http_response_code(404);
            exit('Video nicht gefunden');
        }

        if ($video['source'] === 'twitch') {
            http_response_code(403);
            exit('Twitch-Streams werden automatisch verwaltet');
        }

        if ((int)$video['user_id'] !== (int)$_SESSION['user']['id']) {
            http_response_code(403);
            exit('Keine Berechtigung');
        }

        Database::execute(
            "DELETE FROM videos WHERE id = ?",
            [$id]
        );

        header('Location: /videos');
        exit;
    }

    /* =========================================================
       HELPER – URL → EMBED URL
    ========================================================= */

    private function toEmbedUrl(string $url): string
    {
        // ▶ YouTube
        if (preg_match(
            '~(youtube\.com/watch\?v=|youtu\.be/)([a-zA-Z0-9_-]{11})~',
            $url,
            $m
        )) {
            return 'https://www.youtube.com/embed/' . $m[2];
        }

        // ▶ Twitch (Fallback, eigentlich nicht genutzt)
        if (preg_match('~twitch\.tv/videos/(\d+)~', $url, $m)) {
            return 'https://player.twitch.tv/?video=' . $m[1] .
                   '&parent=' . $_SERVER['HTTP_HOST'];
        }

        return $url;
    }
}
