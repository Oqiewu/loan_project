<?php

/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->post('/loans', 'LoanController@store');
$router->get('/loans/{id}', 'LoanController@show');
$router->put('/loans/{id}', 'LoanController@update');
$router->delete('/loans/{id}', 'LoanController@destroy');
$router->get('/loans', 'LoanController@index');
$router->post('loans/{id}/repay', 'LoanController@repay');
