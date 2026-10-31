<?php

// Archivo usado para modificar las rutas http
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::view('/dashboard', 'dashboard')->name('dashboard');
Route::view('/login', 'auth')->name('login');