<?php
declare(strict_types=1);

class AdminGalleryController
{
    private GalleryModel $model;

    public function __construct()
    {
        $this->model = new GalleryModel();
    }

    /* =========================================================
       ACCESS GUARD (PERMISSION-BASIERT)
    ========================================================= */

    private function guard(string $permission): void
    {
        if (
            empty($_SESSION['user']) ||
            !Permission::has($permission)
        ) {
            http_response_code(403);
            View::render('errors/403', [
                'title' => 'Zugriff verweigert'
            ]);
            exit;
        }
    }

    /* =========================================================
       INDEX – GALERIE
    ========================================================= */

    public function index(): void
    {
        $this->guard('admin.gallery.manage');

        $images = $this->model->getAll() ?? [];

        View::render(
            'admin/gallery/index',
            [
                'title'  => 'Admin – Galerie',
                'images' => $images
            ],
            'admin'
        );
    }
}
