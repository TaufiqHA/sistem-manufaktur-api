<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\MaterialController;

// Authentication routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected routes (require authentication)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [AuthController::class, 'profile']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);

    // Existing protected routes
    Route::get('/user-profile', function (Request $request) {
        return $request->user();
    });

    // User resource routes with sanctum middleware
    Route::apiResource('users', UserController::class);

    // Project resource routes with sanctum middleware
    Route::apiResource('projects', ProjectController::class);

    // Material resource routes with sanctum middleware
    Route::apiResource('materials', MaterialController::class);

    // Additional material routes
    Route::patch('/materials/{material}/stock', [MaterialController::class, 'updateStock']);
    Route::get('/materials-low-stock', [MaterialController::class, 'lowStock']);
});
