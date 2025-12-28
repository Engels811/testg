<?php
declare(strict_types=1);

class AdminFaviconController
{
    /* =========================================================
       ACCESS GUARD (OWNER / SYSTEM-PERMISSION)
    ========================================================= */

    private function guard(): void
    {
        if (
            empty($_SESSION['user']) ||
            !Permission::has('admin.system.favicon')
        ) {
            http_response_code(403);
            View::render('errors/403', [
                'title' => 'Zugriff verweigert'
            ]);
            exit;
        }
    }

    /* =========================================================
       GET /admin/settings/favicon
    ========================================================= */

    public function index(): void
    {
        $this->guard();

        View::render('admin/settings/favicon', [
            'title' => 'Favicon verwalten'
        ]);
    }

    /* =========================================================
       POST /admin/settings/favicon/upload
    ========================================================= */

    public function upload(): void
    {
        $this->guard();
        Security::checkCsrf();

        if (empty($_FILES['favicon']['tmp_name'])) {
            $this->redirectBack();
        }

        $tmpFile = $_FILES['favicon']['tmp_name'];

        if (!is_uploaded_file($tmpFile)) {
            $this->redirectBack();
        }

        $img = @imagecreatefromstring(
            file_get_contents($tmpFile)
        );

        if (!$img) {
            $this->redirectBack();
        }

        // Zielpfad
        $target = BASE_PATH . '/public/favicon.png';

        // 64×64 erzeugen
        $size = 64;
        $canvas = imagecreatetruecolor($size, $size);

        imagealphablending($canvas, false);
        imagesavealpha($canvas, true);

        imagecopyresampled(
            $canvas,
            $img,
            0, 0, 0, 0,
            $size, $size,
            imagesx($img),
            imagesy($img)
        );

        imagepng($canvas, $target);

        imagedestroy($img);
        imagedestroy($canvas);

        // Cache-Bust / Versionierung
        if (class_exists('FaviconService')) {
            FaviconService::bumpVersion();
        }

        $_SESSION['flash_success'] = 'Favicon wurde aktualisiert.';
        $this->redirectBack();
    }

    /* =========================================================
       REDIRECT
    ========================================================= */

    private function redirectBack(): void
    {
        header('Location: /admin/settings/favicon');
        exit;
    }
}
