<?php

/** @var \Laravel\Lumen\Routing\Router $router */

$router->get('/', function () use ($router) {
    return response()->json([
        'service' => 'Auth Service',
        'version' => '1.0.0',
        'status' => 'running'
    ]);
});

// Rutas públicas
$router->group(['prefix' => 'api/auth'], function () use ($router) {
    $router->post('register', 'AuthController@register');
    $router->post('login', 'AuthController@login');
    $router->get('roles', 'AuthController@roles');
});

// Rutas protegidas
$router->group(['prefix' => 'api/auth', 'middleware' => 'auth'], function () use ($router) {
    $router->get('me', 'AuthController@me');
    $router->post('logout', 'AuthController@logout');
    $router->post('refresh', 'AuthController@refresh');
});