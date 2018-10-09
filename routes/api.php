<?php

use Illuminate\Http\Request;

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

$api = app('Dingo\Api\Routing\Router');

$api->version('v1', ['middleware' => 'auth:api'], function ($api) {
    $api->get('/me', ['uses' => 'App\Http\Controllers\Api\ProfileController@me']);

    $api->group(['prefix' => 'books'], function ($api) {
        $api->get('/', ['uses' => 'App\Http\Controllers\Api\BooksController@index']);
        $api->post('/', ['uses' => 'App\Http\Controllers\Api\BooksController@store']);
        $api->get('{id}', ['uses' => 'App\Http\Controllers\Api\BooksController@show']);
        $api->put('{id}', ['uses' => 'App\Http\Controllers\Api\BooksController@update']);
        $api->delete('{id}', ['uses' => 'App\Http\Controllers\Api\BooksController@delete']);
    });
});
