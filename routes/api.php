<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::namespace('App\Http\Controllers')->group(function () {
    // login
    Route::post('/login', 'LoginController@login');
    // logout
    Route::post('/logout', 'LoginController@logout');
});
