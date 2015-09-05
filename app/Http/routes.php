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

    // Carriers
    $api->get('carriers', 'CarriersController@index');

    $api->post('carriers/rates', 'CarriersController@getAllRates');

    $api->get('carriers/{id}', 'CarriersController@get');

    $api->post('carriers','CarriersController@create');

    $api->put('carriers/{id}','CarriersController@update');

    $api->delete('carriers/{id}', 'CarriersController@delete');



    //$api->get('carriers/{idCarrier}/rates', 'CarriersController@getAllRates');


    //Services
    $api->get('carriers/{idCarrier}/services', 'CarriersServicesController@index');

    $api->get('carriers/{idCarrier}/services/{idService}', 'CarriersServicesController@get');

    $api->post('carriers/{idCarrier}/services','CarriersServicesController@create');

    $api->put('carriers/{idCarrier}/services/{idService}','CarriersServicesController@update');

    $api->delete('carriers/{idCarrier}/services/{idService}', 'CarriersServicesController@delete');
});


