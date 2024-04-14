<?php

use App\Core\Router;
use App\Core\Auth;
use App\Models\User;
use App\Models\Database;
// Assume DashboardController and WooController are created for dashboard and WooCommerce routes

$router = new Router;
// Assuming Database class is correctly set up to return a PDO connection
$database = new Database();
$userModel = new User($database);
$auth = new Auth($userModel);

// Public routes
$router->get('', 'PageController@home');
$router->get('login', 'AuthController@showLoginForm');
$router->post('login', 'AuthController@processLogin');
$router->get('register', 'AuthController@showRegistrationForm');
$router->post('register', 'AuthController@processRegistration');
$router->get('logout', 'AuthController@logout');

$router->group('start', function($router) {
    $router->get('start', 'PageController@start');
    $router->get('start/settings', 'UserController@settings');
    $router->post('start/settings', 'UserController@processSettings');
    $router->post('api/setPosition', 'GameController@setPosition');
    $router->get('api/getPosition', 'GameController@getPosition');
    $router->post('api/getNearbyPlayersPosition', 'GameController@getNearbyPlayersPosition');
    
    
    // $router->get('dashboard/credentials/delete/{id}', 'CredentialController@deleteCredentials');
  

    
}, ['middleware' => function() use ($auth) {
    return $auth->isLoggedIn();
    }]);

// Save $router for use in the front controller
return $router;