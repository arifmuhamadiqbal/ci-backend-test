<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->get('/', 'Home::index');

// AUTH
$routes->group('api', function($routes) {
    $routes->post('register', 'AuthController::register');
    $routes->post('login',    'AuthController::login');
});

// Protected (butuh JWT)
$routes->group('api', ['filter' => 'jwt'], function($routes) {
    // CRUD user login
    $routes->resource('users', ['controller' => 'Users']); // GET/POST/PUT/PATCH/DELETE

    // Realtime search
    $routes->get('search/name/(:any)', 'Realtime::byName/$1');
    $routes->get('search/nim/(:segment)', 'Realtime::byNim/$1');
    $routes->get('search/ymd/(:segment)', 'Realtime::byYmd/$1');
});

