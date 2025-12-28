<?php

class PlaylistController
{
    public function index(): void
    {
        $playlists = Database::fetchAll(
            "SELECT *
             FROM playlists
             WHERE is_active = 1
             ORDER BY sort_order ASC, created_at DESC"
        );

        // Sicherheitsnetz: immer ein Array
        if (!is_array($playlists)) {
            $playlists = [];
        }

        View::render('playlists/index', [
            'title'     => 'Playlisten',
            'playlists' => $playlists
        ]);
    }
}
