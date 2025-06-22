<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\API\TypeOfWorkController;
use App\Http\Controllers\API\ContractorController;
use App\Http\Controllers\API\ConductorController;
use App\Http\Controllers\API\JobOrderController;
use App\Http\Controllers\API\JobOrderStatementController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth:sanctum', 'verified'])->name('dashboard');

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    //Type of work
    Route::get('type-of-works/view', [TypeOfWorkController::class, 'create'])->middleware(['role:admin']);
    Route::get('contractors/view', [ContractorController::class, 'create'])->middleware(['role:admin']);
    Route::get('conductors/view', [ConductorController::class, 'create'])->middleware(['role:admin']);
    Route::get('job-orders/view', [JobOrderController::class, 'create'])->middleware(['role:admin']);
    Route::get('jos/view', [JobOrderStatementController::class, 'create'])->middleware(['role:admin']);
});

require __DIR__.'/auth.php';
