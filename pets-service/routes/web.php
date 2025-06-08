<?php

/** @var \Laravel\Lumen\Routing\Router $router */

// Health check
$router->get('/', function () use ($router) {
    return response()->json([
        'service' => 'Pets Service',
        'version' => '1.0.0',
        'status' => 'running'
    ]);
});

// Rutas públicas (no requieren autenticación)
$router->group(['prefix' => 'api/pets'], function () use ($router) {
    
    // Especies - pueden ser públicas para formularios
    $router->get('species', 'SpeciesController@index');
    $router->get('species/list', 'SpeciesController@list');
    $router->get('species/{id}', 'SpeciesController@show');
    
    // Razas - pueden ser públicas para formularios
    $router->get('breeds', 'BreedController@index');
    $router->get('breeds/species/{speciesId}', 'BreedController@bySpecies');
    $router->get('breeds/{id}', 'BreedController@show');
});

// Rutas protegidas (requieren autenticación JWT)
$router->group(['prefix' => 'api/pets', 'middleware' => 'jwt.auth'], function () use ($router) {
    
    // CRUD de mascotas
    $router->get('', 'PetController@index');           // Listar mascotas
    $router->post('', 'PetController@store');          // Registrar mascota (RF-03)
    $router->get('{id}', 'PetController@show');        // Ver mascota específica
    $router->put('{id}', 'PetController@update');      // Actualizar mascota
    $router->delete('{id}', 'PetController@destroy');  // Eliminar mascota (solo admin)
    
    // Rutas adicionales útiles
    $router->get('{id}/summary', 'PetController@summary');  // Resumen de mascota para otros servicios
});