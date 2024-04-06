<?php

namespace App\Controllers;

class PageController
{
    private $userId;

    public function __construct()
    {
        $this->userId = $_SESSION['user_id'] ?? null;
    }

    public function home()
    {

        renderView('home');
    }

    public function start()
    {
        $data = [
            'api' => $_ENV['GOOGLE_MAPS_API_KEY'] ?? null,
        ];
        renderView('game/index', $data);
    }
    public function settings()
    {
        echo "Hello";
    }

}