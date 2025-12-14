<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PlataformaController;
use App\Http\Controllers\Api\GeneroController;
use App\Http\Controllers\Api\JuegoController;

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\FavoriteController;
use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\ExternalController;

//rutas api

// Auth endpoints (register/login/logout)
Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

// Legacy / direct login fallback (kept for compatibility)
Route::post('/login', [AuthController::class, 'login']);

// Optional GET fallback to provide friendly message
Route::get('/login', function(){
    return response()->json([
        'success' => false,
        'message' => 'GET no soportado. Use POST a /api/auth/login para autenticar.'
    ], 405);
});

// Weather endpoint (public)
Route::get('/weather', [ExternalController::class, 'weather']);
 
// Catálogo visible para todos (Solo Index y Show)
// Productos - exponer también con nombres en inglés para el enunciado
Route::get('/juegos', [JuegoController::class, 'index']);
Route::get('/juegos/{id}', [JuegoController::class, 'show']);
Route::get('/products', [JuegoController::class, 'index']);
Route::get('/products/{id}', [JuegoController::class, 'show']);
Route::get('/plataformas', [PlataformaController::class, 'index']);

// Comentarios - GET público para que todos puedan verlos
Route::get('/comments/{juegoId}', [CommentController::class, 'index']);
 
// Rutas Protegidas (Requieren Token - cualquier usuario autenticado)
Route::middleware('auth:sanctum')->group(function () {
    // Comentarios - POST y DELETE requieren auth
    Route::post('/comments', [CommentController::class, 'store']);
    Route::delete('/comments/{id}', [CommentController::class, 'destroy']);

    // Favoritos
    Route::get('/favorites', [FavoriteController::class, 'index']);
    Route::post('/favorites', [FavoriteController::class, 'store']);
    Route::delete('/favorites/{juegoId}', [FavoriteController::class, 'destroy']);
});
 
// Rutas Protegidas (Requieren Token Y ser Admin)
Route::middleware(['auth:sanctum', 'is_admin'])->group(function () {
// Logout
    // admin product management (also accept english paths)
    Route::post('/logout', [AuthController::class, 'logout']);
 
    // Gestión completa de Juegos (excepto index/show que ya definimos arriba)
    Route::post('/juegos', [JuegoController::class, 'store']);
    Route::put('/juegos/{id}', [JuegoController::class, 'update']);
    Route::delete('/juegos/{id}', [JuegoController::class, 'destroy']);

    // english aliases
    Route::post('/products', [JuegoController::class, 'store']);
    Route::put('/products/{id}', [JuegoController::class, 'update']);
    Route::delete('/products/{id}', [JuegoController::class, 'destroy']);
 
    // Gestión de Plataformas y Géneros (Todo el CRUD)
    Route::apiResource('plataformas', PlataformaController::class)->except(['index']); // Index es público
    Route::apiResource('generos', GeneroController::class);
});

