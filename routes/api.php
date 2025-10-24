<?php

use App\Http\Controllers\Api\ArticleController;
use App\Http\Controllers\Api\UserPreferenceController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Public article endpoints with rate limiting
Route::prefix('articles')->middleware('throttle:api-authenticated')->group(function () {
    Route::get('/', [ArticleController::class, 'index'])
        ->middleware('throttle:search')
        ->name('articles.index');
    Route::get('/{id}', [ArticleController::class, 'show'])->name('articles.show');
    Route::get('/meta/sources', [ArticleController::class, 'sources'])->name('articles.sources');
    Route::get('/meta/categories', [ArticleController::class, 'categories'])->name('articles.categories');
    Route::get('/meta/authors', [ArticleController::class, 'authors'])->name('articles.authors');
});

// Protected user preference endpoints with rate limiting
Route::middleware(['auth:sanctum', 'throttle:preferences'])->prefix('preferences')->group(function () {
    Route::get('/', [UserPreferenceController::class, 'show'])->name('preferences.show');
    Route::put('/', [UserPreferenceController::class, 'update'])->name('preferences.update');
});