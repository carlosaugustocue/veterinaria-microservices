<?php

/** @var \Laravel\Lumen\Routing\Router $router */

// Health check del gateway
$router->get('/', 'GatewayController@health');
$router->get('/health', 'GatewayController@health');

// Rutas públicas de autenticación (sin middleware)
$router->group(['prefix' => 'api/auth'], function () use ($router) {
    $router->post('register', 'GatewayController@authProxy');
    $router->post('login', 'GatewayController@authProxy');
    $router->get('roles', 'GatewayController@authProxy');
});

// Rutas protegidas de autenticación
$router->group(['prefix' => 'api/auth', 'middleware' => 'gateway.auth'], function () use ($router) {
    $router->get('me[/{path:.*}]', 'GatewayController@authProxy');
    $router->post('logout[/{path:.*}]', 'GatewayController@authProxy');
    $router->post('refresh[/{path:.*}]', 'GatewayController@authProxy');
});

// Rutas protegidas para otros microservicios
$router->group(['middleware' => 'gateway.auth'], function () use ($router) {
    
    // Microservicio de mascotas
    $router->group(['prefix' => 'api/pets'], function () use ($router) {
        $router->get('[/{path:.*}]', ['uses' => 'GatewayController@proxy', 'service' => 'pets']);
        $router->post('[/{path:.*}]', ['uses' => 'GatewayController@proxy', 'service' => 'pets']);
        $router->put('[/{path:.*}]', ['uses' => 'GatewayController@proxy', 'service' => 'pets']);
        $router->delete('[/{path:.*}]', ['uses' => 'GatewayController@proxy', 'service' => 'pets']);
    });

    // Microservicio de citas
    $router->group(['prefix' => 'api/appointments'], function () use ($router) {
        $router->get('[/{path:.*}]', ['uses' => 'GatewayController@proxy', 'service' => 'appointments']);
        $router->post('[/{path:.*}]', ['uses' => 'GatewayController@proxy', 'service' => 'appointments']);
        $router->put('[/{path:.*}]', ['uses' => 'GatewayController@proxy', 'service' => 'appointments']);
        $router->delete('[/{path:.*}]', ['uses' => 'GatewayController@proxy', 'service' => 'appointments']);
    });

    // Microservicio médico - Solo veterinarios y administradores
    $router->group(['prefix' => 'api/medical', 'middleware' => 'gateway.role:veterinario,administrador'], function () use ($router) {
        $router->get('[/{path:.*}]', ['uses' => 'GatewayController@proxy', 'service' => 'medical']);
        $router->post('[/{path:.*}]', ['uses' => 'GatewayController@proxy', 'service' => 'medical']);
        $router->put('[/{path:.*}]', ['uses' => 'GatewayController@proxy', 'service' => 'medical']);
        $router->delete('[/{path:.*}]', ['uses' => 'GatewayController@proxy', 'service' => 'medical']);
    });

    // Microservicio de inventario - Solo administradores
    $router->group(['prefix' => 'api/inventory', 'middleware' => 'gateway.role:administrador'], function () use ($router) {
        $router->get('[/{path:.*}]', ['uses' => 'GatewayController@proxy', 'service' => 'inventory']);
        $router->post('[/{path:.*}]', ['uses' => 'GatewayController@proxy', 'service' => 'inventory']);
        $router->put('[/{path:.*}]', ['uses' => 'GatewayController@proxy', 'service' => 'inventory']);
        $router->delete('[/{path:.*}]', ['uses' => 'GatewayController@proxy', 'service' => 'inventory']);
    });

    // Microservicio de facturación
    $router->group(['prefix' => 'api/billing'], function () use ($router) {
        $router->get('[/{path:.*}]', ['uses' => 'GatewayController@proxy', 'service' => 'billing']);
        $router->post('[/{path:.*}]', ['uses' => 'GatewayController@proxy', 'service' => 'billing']);
        $router->put('[/{path:.*}]', ['uses' => 'GatewayController@proxy', 'service' => 'billing']);
        $router->delete('[/{path:.*}]', ['uses' => 'GatewayController@proxy', 'service' => 'billing']);
    });
});