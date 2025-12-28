<?php

class LiveController
{
    public function index(): void
    {
        View::render('live', [
            'title' => 'Live – Engels811 Network',
            'currentPage' => 'live'
        ]);
    }
}
