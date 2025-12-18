<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\MaterialController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// User resource routes with sanctum middleware
Route::apiResource('users', UserController::class)->middleware('auth:sanctum');

// Project resource routes with sanctum middleware
Route::apiResource('projects', ProjectController::class)->middleware('auth:sanctum');

// Material resource routes with sanctum middleware
Route::apiResource('materials', MaterialController::class)->middleware('auth:sanctum');

// Additional material routes
Route::patch('/materials/{material}/stock', [MaterialController::class, 'updateStock'])->middleware('auth:sanctum');
Route::get('/materials-low-stock', [MaterialController::class, 'lowStock'])->middleware('auth:sanctum');
