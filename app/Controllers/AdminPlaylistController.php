<?php
declare(strict_types=1);

class AdminPlaylistController
{
    public function index(): void
    {
        Security::requireAdmin();

        $playlists = Database::fetchAll(
            "SELECT
                p.*,
                u.username,
                (SELECT COUNT(*) FROM playlist_videos WHERE playlist_id = p.id) AS video_count
             FROM playlists p
             LEFT JOIN users u ON u.id = p.user_id
             ORDER BY p.created_at DESC"
        ) ?? [];

        View::render('admin/playlists/index', [
            'title'     => 'Playlists',
            'playlists' => $playlists
        ]);
    }

    public function delete(): void
    {
        Security::requireAdmin();
        Security::checkCsrf();

        $id = (int)($_POST['id'] ?? 0);
        if ($id <= 0) {
            http_response_code(400);
            exit;
        }

        Database::execute(
            "DELETE FROM playlist_videos WHERE playlist_id = ?",
            [$id]
        );

        Database::execute(
            "DELETE FROM playlists WHERE id = ?",
            [$id]
        );

        $_SESSION['flash_success'] = 'Playlist wurde gelöscht.';
        header('Location: /admin/playlists');
        exit;
    }
}
