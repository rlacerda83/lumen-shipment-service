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

    $api->get('shipment/carriers', 'CarriersController@index');

    $api->get('shipment/carriers/{id}', 'CarriersController@get');

    $api->post('shipment/carriers','CarriersController@create');

    $api->put('shipment/carriers/{id}','CarriersController@update');

    $api->delete('shipment/carriers/{id}', 'CarriersController@delete');
});


