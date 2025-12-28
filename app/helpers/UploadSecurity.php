<?php

class UploadSecurity
{
    /** Maximale Dateigröße (5 MB) */
    private const MAX_SIZE = 5 * 1024 * 1024;

    /** Erlaubte MIME-Typen */
    private const ALLOWED_MIME = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp',
        'image/gif'  => 'gif',
        'video/mp4'  => 'mp4',
        'video/webm' => 'webm',
    ];

    /**
     * Validiert und speichert einen Upload
     */
    public static function handle(array $file, string $targetDir): ?array
    {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return null;
        }

        if ($file['size'] > self::MAX_SIZE) {
            http_response_code(413);
            View::render('errors/413', [
                'title' => 'Datei zu groß',
                'message' => 'Maximal erlaubt sind 5 MB.'
            ]);
            exit;
        }

        $mime = mime_content_type($file['tmp_name']);

        if (!isset(self::ALLOWED_MIME[$mime])) {
            http_response_code(415);
            View::render('errors/415', [
                'title' => 'Dateityp nicht erlaubt',
                'message' => 'Dieser Dateityp darf nicht hochgeladen werden.'
            ]);
            exit;
        }

        $extension = self::ALLOWED_MIME[$mime];
        $type      = str_starts_with($mime, 'video') ? 'video' : 'image';

        // Sicherer Dateiname (keine Userdaten)
        $filename = uniqid('forum_', true) . '.' . $extension;
        $path     = rtrim($targetDir, '/') . '/' . $filename;

        if (!is_dir(PUBLIC_PATH . $targetDir)) {
            mkdir(PUBLIC_PATH . $targetDir, 0755, true);
        }

        if (!move_uploaded_file($file['tmp_name'], PUBLIC_PATH . $path)) {
            return null;
        }

        return [
            'path' => $path,
            'type' => $type,
            'mime' => $mime,
            'size' => $file['size']
        ];
    }
}
