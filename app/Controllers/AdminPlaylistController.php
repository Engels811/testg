<?php

class AdminPlaylistController
{
    public function index(): void
    {
        Security::requireAuth();

        $playlists = Database::fetchAll(
            "SELECT * FROM playlists ORDER BY sort_order ASC, created_at DESC"
        );

        View::render('admin/playlists/index', [
            'title'     => 'Playlisten verwalten',
            'playlists' => $playlists
        ]);
    }

    public function create(): void
    {
        Security::requireAuth();

        View::render('admin/playlists/create', [
            'title' => 'Playlist hinzufügen'
        ]);
    }

    public function store(): void
    {
        Security::requireAuth();
        Security::checkCsrf();

        Database::execute(
            "INSERT INTO playlists
            (title, description, platform, embed_url, category, sort_order, is_active)
            VALUES (?, ?, ?, ?, ?, ?, ?)",
            [
                trim($_POST['title']),
                trim($_POST['description']),
                $_POST['platform'],
                trim($_POST['embed_url']),
                $_POST['category'] ?? 'general',
                (int)$_POST['sort_order'],
                isset($_POST['is_active']) ? 1 : 0
            ]
        );

        header('Location: /admin/playlists');
        exit;
    }

    public function toggle(): void
    {
        Security::requireAuth();
        Security::checkCsrf();

        Database::execute(
            "UPDATE playlists
             SET is_active = 1 - is_active
             WHERE id = ?",
            [(int)$_POST['id']]
        );

        header('Location: /admin/playlists');
        exit;
    }

    public function delete(): void
    {
        Security::requireAuth();
        Security::checkCsrf();

        Database::execute(
            "DELETE FROM playlists WHERE id = ?",
            [(int)$_POST['id']]
        );

        header('Location: /admin/playlists');
        exit;
    }
}
