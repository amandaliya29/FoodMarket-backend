<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\OfferController;
use App\Http\Controllers\OrderContoller;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::controller(AuthController::class)->group(function () {
    Route::post('register', 'register');
    Route::post('login', 'login');
    Route::post('forgot-password', 'forgotPassword');
    // Route::post('reset-password/{token}', 'resetPassword')->name('password.reset');
});

Route::middleware(['auth:sanctum'])->group(function () {
    Route::controller(AuthController::class)->group(function () {
        Route::get('logout', 'logout');
        Route::post('change-password', 'changePassword');
    });

    Route::controller(ProductController::class)->prefix('product')->group(function () {
        Route::get('list', 'list');
        Route::get('get/{id}', 'get');
        Route::post('save', 'save');
        Route::post('upload', 'media');
        Route::delete('delete/{id}', 'delete');
    });

    Route::controller(CategoryController::class)->prefix('category')->group(function () {
        Route::get('list', 'list');
        Route::get('get/{id}', 'get');
        Route::post('save', 'save');
        Route::post('upload', 'media');
        Route::delete('delete/{id}', 'delete');
    });

    Route::controller(OfferController::class)->prefix('offer')->group(function () {
        Route::get('list', 'list');
        Route::get('get/{id?}', 'get');
        Route::post('save', 'save');
        Route::post('upload', 'media');
        Route::delete('delete/{id}', 'delete');
    });

    Route::controller(OrderContoller::class)->prefix('order')->group(function () {
        Route::get('list', 'list');
        Route::get('get/{id?}', 'get');
        Route::post('create', 'create');
        Route::post('cash-order', 'cash');
        Route::post('cancel', 'cancel');
        Route::post('status', 'status');
        Route::get('past-order', 'pastOrder');
        Route::get('inprogress-order', 'inprogressOrder');
    });
});

Route::post('/order/webhooks', [OrderContoller::class, 'webhooks']);