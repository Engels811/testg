<?php
declare(strict_types=1);

final class HomeController
{
    public function index(): void
    {
        View::render('home', [
            'title'           => 'Home – Engels811 Network',
            'pageDescription' => 'Engels811 Network – Gaming, Streaming & Community',
            'currentPage'     => 'home',
            'bodyClass'       => 'engels-bg'
        ]);
    }
}
