<?php
// login.php
require 'vendor/autoload.php';
session_start();

ini_set('display_errors', 1); ini_set('display_startup_errors', 1); error_reporting(E_ALL);

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Now, you can access the MongoDB URI like this:
$mongoDbUri = $_ENV['MONGODB_URI'];
// Use the URI to connect to MongoDB
$client = new MongoDB\Client($mongoDbUri);

$collection = $client->metangajiiaoi7->users;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $user = $collection->findOne(['email' => $email]);
    if ($user && password_verify($password, $user['password'])) {
        // Password is correct, start the session
        $_SESSION['user_id'] = (string) $user['_id']; // Store user's ID or another unique identifier in session
        // Redirect to game page or send a success message
        echo "Login successful.";
    } else {
        // Handle incorrect credentials
        echo "Incorrect email or password.";
    }
}
?>