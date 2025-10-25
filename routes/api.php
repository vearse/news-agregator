<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ArticleController;
use App\Http\Controllers\Api\UserPreferenceController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/



Route::post('/register', [AuthController::class, 'register'])->name('auth.register');
Route::post('/login', [AuthController::class, 'login'])->name('auth.login');

// Protected authentication routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('auth.logout');
    Route::get('/account', [AuthController::class, 'account'])->name('auth.account');
});


Route::prefix('articles')->middleware('throttle:api-authenticated')->group(function () {
    Route::get('/', [ArticleController::class, 'index'])
        ->middleware('throttle:search')
        ->name('articles.index');
    Route::get('/{id}', [ArticleController::class, 'show'])->name('articles.show');
    Route::get('/meta/sources', [ArticleController::class, 'sources'])->name('articles.sources');
    Route::get('/meta/categories', [ArticleController::class, 'categories'])->name('articles.categories');
    Route::get('/meta/authors', [ArticleController::class, 'authors'])->name('articles.authors');
});

Route::middleware(['auth:sanctum', 'throttle:preferences'])->prefix('preferences')->group(function () {
    Route::get('/', [UserPreferenceController::class, 'show'])->name('preferences.show');
    Route::put('/', [UserPreferenceController::class, 'update'])->name('preferences.update');
});