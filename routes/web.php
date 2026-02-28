<?php

/** @var \Laravel\Lumen\Routing\Router $router */

$router->get('/', function () use ($router) {
    return response()->json([
        'app' => 'Flight Search API',
        'version' => '1.0.0',
        'endpoints' => [
            'POST /api/search' => 'Search for flights',
            'GET /api/countries' => 'Get visa-free countries',
        ]
    ]);
});

$router->group(['prefix' => 'api'], function () use ($router) {
    $router->post('search', 'FlightSearchController@search');
    $router->get('countries', 'FlightSearchController@countries');
});
