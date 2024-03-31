<?php

namespace App\Core;

use App\Models\User;
use \Firebase\JWT\JWT;

class Auth {
    private $userModel;
    private $jwtSecretKey;

    public function __construct(User $userModel) {
        $this->userModel = $userModel;
        // Assuming your secret key for JWT is defined in a config file or environment variable
        $this->jwtSecretKey = $_ENV['JWT_SECRET_KEY']; 
    }

    public function login($email, $password, $rememberMe = false) {
        $user = $this->userModel->verifyUserCredentials($email, $password);

        if (!$user) {
            return ['error' => 'Invalid credentials.'];
        }

        $payload = [
            "iss" => $_ENV['BASE_URL'],
            "aud" => $_ENV['BASE_URL'],
            "iat" => time(),
            "exp" => time() + (24 * 60 * 60), // Token expires in 1 day
            "sub" => $user['id'],
        ];

        $jwt = JWT::encode($payload, $this->jwtSecretKey, 'HS256');
        $_SESSION['user_id'] = $user;
        if ($rememberMe) {
            $rememberToken = bin2hex(random_bytes(16)); // Generate a secure token
            setcookie('remember', $rememberToken, time() + (86400 * 30), "/", "", false, true); // 30 days expiry, HttpOnly
            $this->userModel->storeRememberToken($user['id'], $rememberToken);
        }

        return ['token' => $jwt];
    }

    public function isLoggedIn() {
        if (isset($_SESSION['user_id'])) {
            // User is logged in through the session
            return true;
        } elseif (isset($_COOKIE['remember'])) {
            // Remember me cookie exists, validate it
            $userId = $this->userModel->validateRememberToken($_COOKIE['remember']);
            
            if ($userId) {
                // Valid token, log the user in by setting the session
                $_SESSION['user_id'] = $this->userModel->getUserById($userId);
                return true;
            }
        }

        return false;
    }

}