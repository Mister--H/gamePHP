<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Models\User;
use App\Models\Database;

class AuthController
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

    public function showLoginForm()
    {
        if ($this->auth->isLoggedIn()) {
            header('Location: /start');
            exit;
        }
        renderView('auth/login');
    }

    public function processLogin()
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
    public function showRegistrationForm()
    {
        if ($this->auth->isLoggedIn()) {
            header('Location: /start');
            exit;
        }
        renderView('auth/register');
    }

    public function processRegistration()
    {
        $name = $_POST['name'];
        $email = $_POST['email'];
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

        // Ensure the email doesn't already exist
        $existingUser = $this->user->findUserByEmail($email);
        if ($existingUser) {
            // Handle the case where the user already exists
            $_SESSION['error'] = "This Email is already registered. Please login.";
            header('Location: /register');
            exit;
        }
        // Insert the new user
        $this->user->register($name, $email, $password);
        // Redirect or send a success message
        header('Location: /start');
    }

    public function logout()
    {
        // Unset user_id from session
        if (isset($_SESSION['user_id'])) {
            unset($_SESSION['user_id']);
        }
        if (isset($_SESSION['account_id'])) {
            unset($_SESSION['account_id']);
        }
        // Clear the remember cookie
        if (isset($_COOKIE['remember'])) {
            unset($_COOKIE['remember']);
            setcookie('remember', '', time() - 3600, '/'); // set the expiration date to one hour ago
        }

        // Clear the JWT token cookie
        if (isset($_COOKIE['token'])) {
            unset($_COOKIE['token']);
            setcookie('token', '', time() - 3600, '/'); // set the expiration date to one hour ago
        }

        // Redirect to login page or home page after logout
        header('Location: /login');
        exit;
    }
}