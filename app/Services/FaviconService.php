<?php
declare(strict_types=1);

final class FaviconService
{
    private const VERSION_FILE = BASE_PATH . '/storage/cache/favicon.version';
    private const ICON_PATH    = BASE_PATH . '/public/favicon.ico';

    /**
     * Verarbeitet den Upload, erzeugt ein Multi-Size ICO
     * und aktualisiert automatisch die Cache-Version
     */
    public static function processUpload(array $file): void
    {
        if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
            throw new RuntimeException('Ungültiger Upload.');
        }

        $tmp  = $file['tmp_name'];
        $info = getimagesize($tmp);

        if ($info === false) {
            throw new RuntimeException('Ungültige Bilddatei.');
        }

        $src = match ($info[2]) {
            IMAGETYPE_PNG  => imagecreatefrompng($tmp),
            IMAGETYPE_JPEG => imagecreatefromjpeg($tmp),
            IMAGETYPE_GIF  => imagecreatefromgif($tmp),
            IMAGETYPE_WEBP => imagecreatefromwebp($tmp),
            IMAGETYPE_BMP  => imagecreatefrombmp($tmp),
            default        => null,
        };

        if (!$src) {
            throw new RuntimeException('Nicht unterstütztes Bildformat.');
        }

        $sizes = [16, 32, 48, 64, 128, 256];
        $icons = [];

        foreach ($sizes as $size) {
            $img = imagecreatetruecolor($size, $size);
            imagealphablending($img, false);
            imagesavealpha($img, true);

            imagecopyresampled(
                $img,
                $src,
                0,
                0,
                0,
                0,
                $size,
                $size,
                imagesx($src),
                imagesy($src)
            );

            $icons[$size] = $img;
        }

        self::writeIco($icons, self::ICON_PATH);
        self::updateVersionHash();

        imagedestroy($src);
        foreach ($icons as $img) {
            imagedestroy($img);
        }
    }

    /**
     * Alias für Controller-Kompatibilität
     * (Cache-Busting ohne Upload)
     */
    public static function bumpVersion(): void
    {
        self::updateVersionHash();
    }

    /**
     * Erstellt ein valides Multi-Resolution ICO
     */
    private static function writeIco(array $images, string $path): void
    {
        $data   = '';
        $dir    = '';
        $offset = 6 + (count($images) * 16);

        foreach ($images as $size => $img) {
            ob_start();
            imagepng($img);
            $png = ob_get_clean();

            $dir .= pack(
                'CCCCvvVV',
                $size === 256 ? 0 : $size,
                $size === 256 ? 0 : $size,
                0,
                0,
                1,
                32,
                strlen($png),
                $offset
            );

            $data   .= $png;
            $offset += strlen($png);
        }

        $header = pack('vvv', 0, 1, count($images));
        file_put_contents($path, $header . $dir . $data);
    }

    /**
     * Aktualisiert den Versions-Hash für Cache-Busting
     */
    private static function updateVersionHash(): void
    {
        if (!file_exists(self::ICON_PATH)) {
            return;
        }

        $hash = substr(sha1_file(self::ICON_PATH), 0, 12);
        $dir  = dirname(self::VERSION_FILE);

        if (!is_dir($dir)) {
            mkdir($dir, 0775, true);
        }

        file_put_contents(self::VERSION_FILE, $hash);
    }

    /**
     * Liefert die aktuelle Favicon-Version
     */
    public static function getVersion(): string
    {
        if (!file_exists(self::VERSION_FILE)) {
            return '1';
        }

        return trim(file_get_contents(self::VERSION_FILE));
    }
}
