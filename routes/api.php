<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PostController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::middleware(['auth:api', 'role:admin'])->group(function () {
    Route::get('/posts', [PostController::class, 'adminPosts']);
    });

Route::middleware(['auth:api', 'role:reporter'])->group(function () {
        Route::post('/post', [PostController::class, 'store']);
      Route::get('/posts', [PostController::class, 'reporterPosts']);
    });
