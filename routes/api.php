<?php

use Illuminate\Support\Facades\Route;

Route::namespace('App\Http\Controllers')->group(function () {
    // login
    Route::post('/login', 'LoginController@login');
    // logout
    Route::post('/logout', 'LoginController@logout');

    Route::group(['middleware' => ['auth.jwt']], function () {
        // users
        Route::get('/users', 'UserController@index');
        Route::get('/users/{user_id}', 'UserController@show');
        Route::post('/users/{user_id}', 'UserController@create');
        Route::put('/users/{user_id}', 'UserController@update');
        Route::delete('/users/{user_id}', 'UserController@delete');
    });
});
