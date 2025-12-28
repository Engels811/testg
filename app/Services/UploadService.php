<?php
declare(strict_types=1);

/**
 * UploadService
 * -----------------------------
 * Zentrale Upload-Logik für das Forum
 * - Bilder & Videos
 * - MIME-Validierung
 * - sichere Dateinamen
 * - saubere Rückgabe für DB
 */

class UploadService
{
    /**
     * Erlaubte MIME-Typen
     */
    private const ALLOWED_IMAGE_MIME = [
        'image/jpeg',
        'image/png',
        'image/webp',
        'image/gif',
    ];

    private const ALLOWED_VIDEO_MIME = [
        'video/mp4',
        'video/webm',
        'video/ogg',
    ];

    /**
     * Maximale Dateigröße (20 MB)
     */
    private const MAX_FILE_SIZE = 20 * 1024 * 1024;

    /**
     * Forum Attachment Upload
     *
     * @param array $file  $_FILES['attachment']
     * @return array|null
     *   [
     *     'path' => '/uploads/forum/att_xxx.ext',
     *     'type' => 'image'|'video'
     *   ]
     */
    public static function handleForumAttachment(array $file): ?array
    {
        if (
            empty($file['tmp_name']) ||
            !is_uploaded_file($file['tmp_name'])
        ) {
            return null;
        }

        if (($file['size'] ?? 0) > self::MAX_FILE_SIZE) {
            return null;
        }

        $mime = mime_content_type($file['tmp_name']);

        $type = null;

        if (in_array($mime, self::ALLOWED_IMAGE_MIME, true)) {
            $type = 'image';
        }

        if (in_array($mime, self::ALLOWED_VIDEO_MIME, true)) {
            $type = 'video';
        }

        if ($type === null) {
            return null;
        }

        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $ext = strtolower($ext);

        // Sicherheitsfallback
        if ($ext === '') {
            $ext = $type === 'image' ? 'jpg' : 'mp4';
        }

        $filename = uniqid('att_', true) . '.' . $ext;
        $relativePath = '/uploads/forum/' . $filename;
        $absolutePath = PUBLIC_PATH . $relativePath;

        // Zielordner sicherstellen
        $dir = dirname($absolutePath);
        if (!is_dir($dir)) {
            mkdir($dir, 0775, true);
        }

        if (!move_uploaded_file($file['tmp_name'], $absolutePath)) {
            return null;
        }

        // Rechte setzen (shared hosting safe)
        chmod($absolutePath, 0644);

        return [
            'path' => $relativePath,
            'type' => $type,
        ];
    }
}
