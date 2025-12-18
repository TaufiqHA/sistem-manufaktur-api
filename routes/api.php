<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProjectController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// User resource routes with sanctum middleware
Route::apiResource('users', UserController::class)->middleware('auth:sanctum');

// Project resource routes with sanctum middleware
Route::apiResource('projects', ProjectController::class)->middleware('auth:sanctum');
