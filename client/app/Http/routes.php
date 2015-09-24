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

Route::get('/', function () {
    return view('welcome');
});

Route::controllers([
    'auth' => 'Auth\AuthController',
    'password' => 'Auth\PasswordController',
]);

Route::group(['middleware' => 'auth'], function () {
    Route::get('/home', ['as' => 'home', 'uses' => 'HomeController@index']);

    Route::get('/invoices', ['as' => 'invoices-list', 'uses' => 'InvoicesController@index']);
    Route::get('/invoice/create', ['as' => 'invoice-create', 'uses' => 'InvoicesController@create']);

    Route::get('/invoice/{id}', ['as' => 'invoice-view', 'uses' => 'InvoicesController@view']);
    Route::get('/invoice/{id}/edit', ['as' => 'invoice-update', 'uses' => 'InvoicesController@update']);
    Route::post('/invoice/{id}/edit', ['as' => 'invoice-update', 'uses' => 'InvoicesController@store']);
    Route::get('/invoice/{id}/duplicate', ['as' => 'invoice-duplicate', 'uses' => 'InvoicesController@duplicate']);

    Route::get('/invoice/{id}/delete', ['as' => 'invoice-delete', 'uses' => 'InvoicesController@delete']);

    Route::get('/invoice/{id}/domestic', ['as' => 'invoice-domestic', 'uses' => 'InvoicesController@domestic']);
    Route::get('/invoice/{id}/foreign', ['as' => 'invoice-foreign', 'uses' => 'InvoicesController@foreign']);

    Route::get('/settings', ['as' => 'settings-list', 'uses' => 'SettingsController@index']);
    Route::get('/setting/{id}', ['as' => 'setting-update', 'uses' => 'SettingsController@update']);
    Route::post('/setting/{id}', ['as' => 'setting-update', 'uses' => 'SettingsController@store']);
});
