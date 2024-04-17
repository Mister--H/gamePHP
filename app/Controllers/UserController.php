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
            'user' => $_SESSION['user_id'] ?? '',
        ];
        renderView('game/settings', $data);
    }

    public function processSettings()
    {
        $firstName = $_POST['firstName'] ?? '';
        $lastName = $_POST['lastName'] ?? '';
        $nickname = $_POST['nickname'] ?? '';
        $sex = $_POST['sex'] ?? '';
        $age = $_POST['age'] ?? '';
        $phoneNumber = $_POST['phoneNumber'] ?? '';
        $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
        $password = $_POST['password'] ?? '';
        $instagram = $_POST['instagram'] ?? '';
        $facebook = $_POST['facebook'] ?? '';
        $twitter = $_POST['twitter'] ?? '';
        $telegram = $_POST['telegram'] ?? '';
        $bio = $_POST['bio'] ?? '';

        // Handle uploaded file
        $avatarPath = $_SESSION['user']['avatar'];
        if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
            $avatarTmpPath = $_FILES['avatar']['tmp_name'];
            $avatarFileName = $_FILES['avatar']['name'];
            $avatarExtension = pathinfo($avatarFileName, PATHINFO_EXTENSION); // Get the file extension
            $avatarNewFileName = uniqid() . '.' . $avatarExtension; // Generate a unique filename
            $avatarPath = 'assets/img/users/avatars/' . $avatarNewFileName;

            // Move the uploaded file to the destination folder with the new filename
            move_uploaded_file($avatarTmpPath, $avatarPath);
        }



        $result = $this->user->saveProfile($email, $password, $firstName, $lastName, $nickname, $sex, $age, $phoneNumber, $instagram, $facebook, $twitter, $telegram, $bio, $avatarPath);

        if (isset($result['error'])) {
            $_SESSION['error'] = $result['error'];
            header('Location: /start/settings');
            exit;
        }

        $_SESSION['user'] = $result;
        header('Location: /start/settings');
        exit;
    }

    public function getUserInfo(){
        $input = file_get_contents('php://input');
        $data = json_decode($input, true);

        $userId = $data['userId'];
        $user = $this->user->getUserById($userId);
        header('Content-Type: application/json');
        echo json_encode($user);
    }
    

}