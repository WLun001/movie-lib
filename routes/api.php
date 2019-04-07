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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('api')->namespace('Auth')->prefix('auth')->group(function() {
    Route::post('login', 'AuthController@login');
    Route::post('logout', 'AuthController@logout');
    Route::post('refresh', 'AuthController@refresh');
    Route::post('me', 'AuthController@me');
});

Route::get('/studios/search', 'StudioController@search');
Route::apiResource('studios', 'StudioController');
Route::get('/movies/search', 'MovieController@search');
Route::apiResource('movies', 'MovieController');
Route::get('/actors/search', 'ActorController@search');
Route::apiResource('actors', 'ActorController');
