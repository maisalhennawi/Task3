<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Route;

Route::controller(AuthController::class)
    ->prefix('auth')
    ->group(function () {
        Route::middleware('guest:sanctum')
            ->group(function () {
                Route::post('register', 'signup')->name('auth.register');
                Route::post('login', 'login')->name('auth.login');
                
            });

        Route::middleware('auth:sanctum')
            ->group(function () {
                Route::get('refresh', 'refresh')->name('auth.refresh');
                Route::post('logout', 'logout')->name('auth.logout');
            });
    });
