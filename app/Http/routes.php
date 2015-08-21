<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/
$api = app('Dingo\Api\Routing\Router');

$api->version('v1', ['middleware' => 'api.auth', 'namespace' => 'App\Http\Controllers\V1'], function ($api) {

    $api->get('carriers', 'CarriersController@index');

    $api->get('carriers/{id}', 'CarriersController@get');

    $api->post('carriers','CarriersController@create');

    $api->put('carriers/{id}','CarriersController@update');

    $api->delete('carriers/{id}', 'CarriersController@delete');
});


