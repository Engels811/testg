<?php
declare(strict_types=1);

class AdminGalleryController
{
    private GalleryModel $model;

    public function __construct()
    {
        $this->model = new GalleryModel();
    }

    public function index(): void
    {
        Security::requireAdmin();

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
