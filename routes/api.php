<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Route::post('/posts', [PostController::class, 'store']);
// Route::get('/posts', [PostController::class, 'index']);
// Route::put('/posts/{id}', [PostController::class, 'update']);
// Route::delete('/posts/{id}', [PostController::class, 'destroy']);

Route::apiResource('/posts',PostController::class);

Route::post('/posts/search', [PostController::class, 'search']);

// Route::post('/posts/details', PostController::class, 'postDetails');
// api.php
Route::get('posts/{id}', [PostController::class, 'show']);



