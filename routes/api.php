<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MovieController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\FavoriteController;

Route::get('/movies/search', [MovieController::class, 'search']);
Route::get('/movies/{id}', [MovieController::class, 'show']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:api')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    Route::get('/favorites', [FavoriteController::class, 'index']);
    Route::post('/favorites', [FavoriteController::class, 'store']);
    Route::get('/favorites/genres', [FavoriteController::class, 'genres']);
    Route::delete('/favorites/{movie_id}', [FavoriteController::class, 'delete']);
});