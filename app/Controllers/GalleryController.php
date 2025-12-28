<?php

class GalleryController
{
    /* =========================================================
       GALERIE – ÜBERSICHT
    ========================================================= */

    public function index(): void
    {
        View::render('gallery/index', [
            'title' => 'Galerie',
            'currentPage' => 'galerie'
        ]);
    }

    /* =========================================================
       GALERIE – SECTION (COMMUNITY / ARTWORK / BTS)
    ========================================================= */

    public function section(string $section): void
    {
        $map = [
            'community' => [
                'title' => 'Community',
                'subtitle' => 'Screenshots & Momente aus der Community'
            ],
            'artwork' => [
                'title' => 'Artwork',
                'subtitle' => 'Designs, Logos & Fanart'
            ],
            'bts' => [
                'title' => 'Behind the Scenes',
                'subtitle' => 'Technik, Setup & Workflows'
            ]
        ];

        if (!isset($map[$section])) {
            http_response_code(404);
            View::render('errors/404', ['title' => 'Galerie nicht gefunden']);
            return;
        }

        $images = Database::fetchAll(
            "SELECT gm.*, u.username
             FROM gallery_media gm
             JOIN users u ON u.id = gm.user_id
             WHERE gm.section = ?
             ORDER BY gm.created_at DESC",
            [$section]
        );

        View::render('gallery/section', [
            'title'           => 'Galerie – ' . $map[$section]['title'],
            'section'         => $section,
            'sectionTitle'    => $map[$section]['title'],
            'sectionSubtitle' => $map[$section]['subtitle'],
            'images'          => $images
        ]);
    }

    /* =========================================================
       UPLOAD – ALLE EINGELOGGTEN USER
    ========================================================= */

    public function upload(): void
    {
        if (empty($_SESSION['user'])) {
            http_response_code(403);
            View::render('errors/403', ['title' => 'Login erforderlich']);
            return;
        }

        $section = $_POST['section'] ?? '';
        $allowedSections = ['community', 'artwork', 'bts'];

        if (!in_array($section, $allowedSections, true)) {
            http_response_code(400);
            exit('Ungültiger Galerie-Bereich');
        }

        if (
            empty($_FILES['image']) ||
            $_FILES['image']['error'] !== UPLOAD_ERR_OK
        ) {
            header('Location: /galerie/' . $section);
            exit;
        }

        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $allowedExt = ['jpg', 'jpeg', 'png', 'webp'];

        if (!in_array($ext, $allowedExt, true)) {
            exit('Ungültiger Dateityp');
        }

        $filename   = uniqid('img_', true) . '.' . $ext;
        $targetDir  = BASE_PATH . '/public/uploads/gallery/' . $section . '/';
        $targetFile = $targetDir . $filename;

        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        if (!move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
            exit('Upload fehlgeschlagen');
        }

        Database::execute(
            "INSERT INTO gallery_media (user_id, section, file)
             VALUES (?, ?, ?)",
            [
                $_SESSION['user']['id'],
                $section,
                $filename
            ]
        );

        header('Location: /galerie/' . $section);
        exit;
    }

    /* =========================================================
       BILD LÖSCHEN – NUR EIGENE
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

        $image = Database::fetch(
            "SELECT * FROM gallery_media WHERE id = ?",
            [$id]
        );

        if (!$image) {
            http_response_code(404);
            exit('Bild nicht gefunden');
        }

        if ((int)$image['user_id'] !== (int)$_SESSION['user']['id']) {
            http_response_code(403);
            exit('Keine Berechtigung');
        }

        $filePath = BASE_PATH . '/public/uploads/gallery/' .
                    $image['section'] . '/' . $image['file'];

        if (is_file($filePath)) {
            unlink($filePath);
        }

        Database::execute(
            "DELETE FROM gallery_media WHERE id = ?",
            [$id]
        );

        header('Location: /galerie/' . $image['section']);
        exit;
    }
}
