<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController; // Asegúrese de que esta línea esté presente

Route::get('/', function () {
    return view('welcome');
});

// Ruta para visualizar el catálogo SPA
Route::get('/app', function () {
    return view('spa');
});

// Esta es la única línea que debe haber en relación al LoginController
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');