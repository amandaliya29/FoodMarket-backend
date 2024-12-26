<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::controller(AuthController::class)->group(function () {
    Route::post('register', 'register');
    Route::post('login', 'login');

    Route::middleware(['auth:sanctum'])->group(function () {
        Route::get('logout', 'logout');

        Route::controller(ProductController::class)->prefix('product')->group(function () {
            Route::get('list', 'list');
            Route::get('get/{id}', 'get');
            Route::post('save', 'save');
            Route::post('upload', 'media');
            Route::delete('delete/{id}', 'delete');
        });
    });
});

Route::get('check', function () {
    return \Illuminate\Support\Facades\Request::root();
});