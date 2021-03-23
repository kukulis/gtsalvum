<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group([

    'middleware' => 'api',
    'prefix' => 'auth'

], function ($router) {
    get_class($router);
    Route::post('login', 'AuthController@login');
    Route::post('logout', 'AuthController@logout');
    Route::post('refresh', 'AuthController@refresh');
    Route::post('me', 'AuthController@me');
});


Route::group(['middleware' => 'auth:api'], function() {

    // tasks
    Route::get('tasks', 'TasksController@index');
    Route::put('task/create', 'TasksController@create');
    Route::post('task/update/{id}', 'TasksController@update');
    Route::get('task/show/{id}', 'TasksController@show');
    Route::delete('task/delete/{id}', 'TasksController@delete');

    // messages
    Route::get('messages', 'MessagesController@index' );
    Route::get('message/view/{id}', 'MessagesController@view' );
    Route::put('message/create', 'MessagesController@create' );
    Route::post('message/update/{id}', 'MessagesController@update' );
    Route::delete('message/delete/{id}', 'MessagesController@delete' );
});



