<?php

namespace App\Controllers;

class PageController
{
    private $userId;

    public function __construct()
    {
        $this->userId = $_SESSION['user_id']['id'] ?? null;
    }

    public function home()
    {
        renderView('home');
    }

    public function dashboard()
    {
        renderView('game/index');
    }

}