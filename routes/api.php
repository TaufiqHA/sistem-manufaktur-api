<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MachineController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\RfqItemController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\ReceivingGoodController;

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

    // Additional ProjectItem routes
    Route::get('/project-items/project/{projectId}', [\App\Http\Controllers\ProjectItemController::class, 'getByProjectId']);

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

    // RFQ resource routes with sanctum middleware
    Route::apiResource('rfqs', \App\Http\Controllers\RfqController::class);

    // RFQ Item resource routes with sanctum middleware
    Route::apiResource('rfq-items', RfqItemController::class);
    Route::get('/rfq-items-by-rfq/{rfqId}', [RfqItemController::class, 'getByRfq']);

    // Supplier resource routes with sanctum middleware
    Route::apiResource('suppliers', \App\Http\Controllers\SupplierController::class);

    // Purchase Order resource routes with sanctum middleware
    Route::apiResource('purchase-orders', \App\Http\Controllers\PurchaseOrderController::class);

    // PO Item resource routes with sanctum middleware
    Route::apiResource('po-items', \App\Http\Controllers\PoItemController::class);

    // Receiving Good resource routes with sanctum middleware
    Route::apiResource('receiving-goods', \App\Http\Controllers\ReceivingGoodController::class);

    // Receiving Item resource routes with sanctum middleware
    Route::apiResource('receiving-items', \App\Http\Controllers\ReceivingItemController::class);
    Route::get('/receiving-goods/{receiving}/items', [\App\Http\Controllers\ReceivingItemController::class, 'getByReceiving']);

    // Finished Goods Warehouse resource routes with sanctum middleware
    Route::apiResource('finished-goods-warehouses', \App\Http\Controllers\FinishedGoodsWarehouseController::class);

    // Production Log resource routes with sanctum middleware
    Route::apiResource('production-logs', \App\Http\Controllers\ProductionLogController::class);

    // Additional production log routes
    Route::get('/production-logs/project/{projectId}', [\App\Http\Controllers\ProductionLogController::class, 'getByProject']);
    Route::get('/production-logs/machine/{machineId}', [\App\Http\Controllers\ProductionLogController::class, 'getByMachine']);
    Route::get('/production-summary', [\App\Http\Controllers\ProductionLogController::class, 'getProductionSummary']);

    // Backup routes
    Route::prefix('backups')->group(function () {
        Route::get('/', [\App\Http\Controllers\BackupController::class, 'index']);
        Route::post('/', [\App\Http\Controllers\BackupController::class, 'store']);
        Route::get('/stats', [\App\Http\Controllers\BackupController::class, 'stats']);
        Route::get('/{id}', [\App\Http\Controllers\BackupController::class, 'show']);
        Route::put('/{id}', [\App\Http\Controllers\BackupController::class, 'update']);
        Route::patch('/{id}', [\App\Http\Controllers\BackupController::class, 'update']);
        Route::delete('/{id}', [\App\Http\Controllers\BackupController::class, 'destroy']);
        Route::get('/{id}/download', [\App\Http\Controllers\BackupController::class, 'download']);
    });
});
