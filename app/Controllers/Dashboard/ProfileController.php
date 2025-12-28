<?php
declare(strict_types=1);

class ProfileController
{
    /* =========================================================
       AVATAR UPLOAD (IMMER RUND)
    ========================================================= */
    public function uploadAvatar(): void
    {
        Security::requireLogin();

        /* =========================
           CSRF
        ========================= */
        if (!Csrf::check($_POST['_csrf'] ?? '')) {
            Response::error(403);
            return;
        }

        if (
            empty($_FILES['avatar']) ||
            $_FILES['avatar']['error'] !== UPLOAD_ERR_OK
        ) {
            Response::redirect('/dashboard/profile');
            return;
        }

        $file = $_FILES['avatar'];

        /* =========================
           SIZE LIMIT (2 MB)
        ========================= */
        if ($file['size'] > 2 * 1024 * 1024) {
            $_SESSION['flash_error'] = 'Avatar ist zu groß (max. 2 MB).';
            Response::redirect('/dashboard/profile');
            return;
        }

        /* =========================
           MIME WHITELIST
        ========================= */
        $allowed = [
            'image/jpeg',
            'image/png',
            'image/webp',
        ];

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime  = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mime, $allowed, true)) {
            $_SESSION['flash_error'] = 'Ungültiges Bildformat.';
            Response::redirect('/dashboard/profile');
            return;
        }

        /* =========================
           LOAD IMAGE
        ========================= */
        switch ($mime) {
            case 'image/jpeg':
                $src = imagecreatefromjpeg($file['tmp_name']);
                break;
            case 'image/png':
                $src = imagecreatefrompng($file['tmp_name']);
                break;
            case 'image/webp':
                $src = imagecreatefromwebp($file['tmp_name']);
                break;
            default:
                $_SESSION['flash_error'] = 'Bild konnte nicht verarbeitet werden.';
                Response::redirect('/dashboard/profile');
                return;
        }

        if (!$src) {
            $_SESSION['flash_error'] = 'Bild konnte nicht geladen werden.';
            Response::redirect('/dashboard/profile');
            return;
        }

        /* =========================
           CROP ZENTRIERT (QUADRAT)
        ========================= */
        $size = min(imagesx($src), imagesy($src));
        $x = (imagesx($src) - $size) / 2;
        $y = (imagesy($src) - $size) / 2;

        $avatarSize = 256;

        $dst = imagecreatetruecolor($avatarSize, $avatarSize);

        imagealphablending($dst, false);
        imagesavealpha($dst, true);
        $transparent = imagecolorallocatealpha($dst, 0, 0, 0, 127);
        imagefill($dst, 0, 0, $transparent);

        imagecopyresampled(
            $dst,
            $src,
            0,
            0,
            (int)$x,
            (int)$y,
            $avatarSize,
            $avatarSize,
            $size,
            $size
        );

        /* =========================
           RUND-MASKE
        ========================= */
        $mask = imagecreatetruecolor($avatarSize, $avatarSize);
        imagealphablending($mask, false);
        imagesavealpha($mask, true);

        $maskBg = imagecolorallocatealpha($mask, 0, 0, 0, 127);
        imagefill($mask, 0, 0, $maskBg);

        $circle = imagecolorallocatealpha($mask, 0, 0, 0, 0);
        imagefilledellipse(
            $mask,
            $avatarSize / 2,
            $avatarSize / 2,
            $avatarSize,
            $avatarSize,
            $circle
        );

        imagecopymerge($dst, $mask, 0, 0, 0, 0, $avatarSize, $avatarSize, 100);

        imagedestroy($mask);
        imagedestroy($src);

        /* =========================
           SAVE FILE (PNG)
        ========================= */
        $userId = (int) $_SESSION['user']['id'];
        $filename = 'avatar_' . $userId . '.png';
        $path = BASE_PATH . '/public/uploads/avatars/' . $filename;

        if (!is_dir(dirname($path))) {
            mkdir(dirname($path), 0755, true);
        }

        imagepng($dst, $path);
        imagedestroy($dst);

        /* =========================
           DB UPDATE
        ========================= */
        Database::execute(
            'UPDATE users SET avatar = ? WHERE id = ?',
            [$filename, $userId]
        );

        $_SESSION['flash_success'] = 'Avatar erfolgreich aktualisiert.';
        Response::redirect('/dashboard/profile');
    }
}
