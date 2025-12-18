<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\MachineController;
use App\Http\Controllers\TaskController;

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

    // Machine resource routes with sanctum middleware
    Route::apiResource('machines', MachineController::class);

    // Additional machine routes
    Route::patch('/machines/{machine}/toggle-maintenance', [MachineController::class, 'toggleMaintenance']);
    Route::get('/machines/type/{type}', [MachineController::class, 'getByType']);
    Route::get('/machines/status/{status}', [MachineController::class, 'getByStatus']);

    // ProjectItem resource routes with sanctum middleware
    Route::apiResource('project-items', \App\Http\Controllers\ProjectItemController::class);

    // BomItem resource routes with sanctum middleware
    Route::apiResource('bom-items', \App\Http\Controllers\BomItemController::class);

    // Additional BomItem route for getting items by project item ID
    Route::get('/bom-items-by-project-item/{projectItemId}', [\App\Http\Controllers\BomItemController::class, 'getByProjectItem']);

    // Task resource routes with sanctum middleware
    Route::apiResource('tasks', TaskController::class);

    // Additional task routes
    Route::patch('/tasks/{task}/status', [TaskController::class, 'updateStatus']);
    Route::patch('/tasks/{task}/quantities', [TaskController::class, 'updateQuantities']);
    Route::post('/tasks/{task}/start-downtime', [TaskController::class, 'startDowntime']);
    Route::post('/tasks/{task}/end-downtime', [TaskController::class, 'endDowntime']);
    Route::get('/tasks-statistics', [TaskController::class, 'statistics']);

    // Production Log resource routes with sanctum middleware
    Route::apiResource('production-logs', \App\Http\Controllers\ProductionLogController::class);

    // Additional production log routes
    Route::get('/production-logs/project/{projectId}', [\App\Http\Controllers\ProductionLogController::class, 'getByProject']);
    Route::get('/production-logs/machine/{machineId}', [\App\Http\Controllers\ProductionLogController::class, 'getByMachine']);
    Route::get('/production-summary', [\App\Http\Controllers\ProductionLogController::class, 'getProductionSummary']);
});
