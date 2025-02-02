<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/reset-password/{token}', function ($taken){
    return view('reset-password', ['token' => $taken]);
});

Route::post('reset-password/{token}', [AuthController::class ,'resetPassword'])->name('password.reset');