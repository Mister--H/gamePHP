<?php
require 'vendor/autoload.php';
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
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Ensure the email doesn't already exist
    $existingUser = $collection->findOne(['email' => $email]);
    if ($existingUser) {
        // Handle the case where the user already exists
        echo "Email already registered.";
        exit;
    }

    // Insert the new user
    $collection->insertOne([
        'email' => $email,
        'password' => $password,
        'coins' => 100, // Starting coins
        'ownedPlaces' => []
    ]);

    // Redirect or send a success message
    echo "Registration successful.";
}
