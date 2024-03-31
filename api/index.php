<?php
require 'vendor/autoload.php'; // Include Composer's autoloader

ini_set('display_errors', 1); ini_set('display_startup_errors', 1); error_reporting(E_ALL);


$mongoDbUri = $_ENV['MONGODB_URI'];
// Use the URI to connect to MongoDB
$client = new MongoDB\Client($mongoDbUri);
$db = $client->metangajiiaoi7;
$users = $db->users;

// Assuming you're receiving these from a form submission
$userEmail = "user@example.com";
$userPassword = password_hash("userPassword", PASSWORD_DEFAULT);

// Insert a new user into the 'users' collection
$insertResult = $users->insertOne([
    'email' => $userEmail,
    'password' => $userPassword,
    'coins' => 100, // Starting coins
    'places' => []
]);

echo "Inserted with Object ID '{$insertResult->getInsertedId()}'";
?>
