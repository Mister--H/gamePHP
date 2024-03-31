<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Models\User;
use App\Models\Database;

class UserController
{
    protected $auth;
    private $user;
    public function __construct()
    {
        // Assuming Database class takes care of its own configuration
        $database = new Database();
        $this->user = new User($database);
        $this->auth = new Auth($this->user);
    }

    public function settings()
    {
        $data = [
            'user' => $_SESSION['user'] ?? '',
        ];
        renderView('game/settings', $data);
    }

    public function processSettings()
    {
        $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
        $password = $_POST['password'];
        $rememberMe = isset($_POST['remember_me']);

        $result = $this->auth->login($email, $password, $rememberMe);

        if (isset($result['error'])) {
            $_SESSION['error'] = $result['error'];
            header('Location: /login');
            exit;
        }

        setcookie('token', $result['token'], ['httponly' => true, 'samesite' => 'Strict']);
        header('Location: /start');
        exit;
    }
}