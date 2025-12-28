<?php

class GalleryModel
{
    public function getAll(): array
    {
        return Database::fetchAll(
            "SELECT * FROM gallery_media ORDER BY created_at DESC"
        );
    }

    public function getPublic(): array
    {
        return Database::fetchAll(
            "SELECT * FROM gallery_media ORDER BY created_at DESC"
        );
    }
}
