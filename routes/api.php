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

Route::middleware('api')->namespace('Auth')->prefix('auth')->group(function () {
    Route::post('login', 'AuthController@login');
    Route::post('logout', 'AuthController@logout');
    Route::post('refresh', 'AuthController@refresh');
    Route::post('me', 'AuthController@me');
});

Route::middleware(['jwt.auth', 'can:manage-movies'])->group(function () {
    Route::get('/movies/search', 'MovieController@search');
    Route::apiResource('movies', 'MovieController')->only([
        'store',
        'update',
        'delete',
    ]);

    Route::get('/studios/search', 'StudioController@search');
    Route::apiResource('studios', 'StudioController')->only([
        'store',
        'update',
        'delete',
    ]);

    Route::get('/actors/search', 'ActorController@search');
    Route::apiResource('actors', 'ActorController')->only([
        'store',
        'update',
        'delete'
    ]);
});

Route::middleware(['jwt.auth', 'can:view-movies'])->group(function () {
    Route::apiResource('movies', 'MovieController')->only([
        'index',
        'show',
        'search'
    ]);
    Route::apiResource('studios', 'StudioController')->only([
        'index',
        'show',
        'search'
    ]);
    Route::apiResource('actors', 'ActorController')->only([
        'index',
        'show',
        'search'
    ]);
});

