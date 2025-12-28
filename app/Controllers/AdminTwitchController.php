<?php

class AdminTwitchController
{
    public function index(): void
    {
        $logs = Database::fetchAll(
            "SELECT *
             FROM twitch_import_logs
             ORDER BY created_at DESC
             LIMIT 50"
        );

        View::render('admin/twitch/index', [
            'title' => 'Twitch Import Logs',
            'logs'  => $logs
        ]);
    }
}
