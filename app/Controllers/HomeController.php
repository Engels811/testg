<?php

class HomeController
{
    public function index(): void
    {
        View::render('home', [
            'title'       => 'Home – Engels811 Network',
            'description' => 'Engels811 Network - Gaming, Streaming & Community',
            'currentPage' => 'home',
            'bodyClass'   => 'engels-bg'
        ]);
    }
}
