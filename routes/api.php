<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\TypeOfWorkController as APITypeOfWorkController;
use App\Http\Controllers\API\ContractorController as APIContractorController;
use App\Http\Controllers\API\ConductorController as APIConductorController;
use App\Http\Controllers\API\JobOrderController as APIJobOrderController;
use App\Http\Controllers\API\JobOrderStatementController as APIJobOrderStatementController;
use App\Http\Controllers\API\ContractorDashboardController;
use App\Http\Controllers\API\ConductorDashboardController;

    Route::get('/user', function (Request $request) {
        return $request->user();
    })->middleware('auth:sanctum');



    // Admin routes
    Route::middleware(['auth:sanctum', 'role:admin'])->prefix('admin')->group(function() {
        Route::apiResource('type-of-works', APITypeOfWorkController::class);
        Route::put('type-of-work/{hashedId}', [APITypeOfWorkController::class, 'update']);
        Route::delete('type-of-work/{hashedId}', [APITypeOfWorkController::class, 'destroy']);

        //Contractor CRUD Routes - (For Admin )
        Route::apiResource('contractors', APIContractorController::class);
        Route::get('contractors/{hashedId}', [APIContractorController::class, 'show']);
        Route::put('contractor/{hashedId}', [APIContractorController::class, 'update']);
        Route::delete('contractors/{hashedId}', [APIContractorController::class, 'destroy']);

        //Conductor CRUD Routes - (For Admin )
        Route::apiResource('conductors', APIConductorController::class);
        Route::get('conductors/{hashedId}', [APIConductorController::class, 'show']);
        Route::put('conductor/{hashedId}', [APIConductorController::class, 'update']);
        Route::delete('conductors/{hashedId}', [APIConductorController::class, 'destroy']);

        Route::apiResource('job-orders', APIJobOrderController::class);
        Route::put('job-order/{hashedId}', [APIJobOrderController::class, 'update']);
        Route::delete('job-orders/{hashedId}', [APIJobOrderController::class, 'destroy']);
        Route::get('jos', [APIJobOrderStatementController::class,'index']);
        Route::post('jos', [APIJobOrderStatementController::class,'store']);
        Route::post('jos/grouped-job-orders', [APIJobOrderStatementController::class, 'groupedJobOrders']);

        Route::apiResource('job-order-statements', APIJobOrderStatementController::class);
        Route::get('job-order-statements/grouped-job-orders', [APIJobOrderStatementController::class, 'groupedJobOrders']);
        Route::get('job-order-statements/{id}/job-orders', [APIJobOrderStatementController::class, 'jobOrders']);

    });

    // Contractor routes
    Route::middleware(['auth:sanctum', 'role:contractor'])->prefix('contractor')->group(function() {
        Route::get('job-orders', [ContractorDashboardController::class,'index']);
        Route::get('jos', [ContractorDashboardController::class,'jos']);
    });

    // Conductor routes
    Route::middleware(['auth:sanctum', 'role:conductor'])->prefix('conductor')->group(function() {
        Route::get('assigned-job-orders', [ConductorDashboardController::class,'index']);
    });

