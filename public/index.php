<?php 
// Improved error reporting setup
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../app/bootstrap.php';

$uri = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
$method = $_SERVER['REQUEST_METHOD'];

try {
    $router->direct($uri, $method);
} catch (Exception $e) {
    // Handle the exception, possibly show a 404 page
    echo $e->getMessage();
}