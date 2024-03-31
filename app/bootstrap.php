<?php

require_once __DIR__ . '/../vendor/autoload.php'; 
$router = require __DIR__ . '/../routes/routes.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();
 // Start the session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/helpers.php'; // Include the helpers file