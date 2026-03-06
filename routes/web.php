<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LogoutController;

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('auth')->group(function () {
    Route::post('/login', LoginController::class)->name('login');
    Route::post('/register', RegisterController::class)->name('register');
    Route::post('/logout', LogoutController::class)->middleware('auth')->name('logout');
});
