<?php

class PartnerController
{
    public function index(): void
    {
        $partners = Database::fetchAll(
            "SELECT *
             FROM partners
             WHERE is_active = 1
             ORDER BY name ASC"
        );

        View::render('partner/index', [
            'partners' => $partners
        ]);
    }
}
