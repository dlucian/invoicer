<?php

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

$app->get('/', function () use ($app) {
    return $app->welcome();
});

$app->group(['prefix' => 'v1','namespace' => 'App\Http\Controllers'], function($app)
{
    $app->get('invoice','InvoiceController@index');
    $app->get('invoice/{id}','InvoiceController@get');
    $app->post('invoice','InvoiceController@create');
    $app->put('invoice/{id}','InvoiceController@update');
    $app->delete('invoice/{id}','InvoiceController@delete');
});