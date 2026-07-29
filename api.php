<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\LeadController;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\ActivityController;
use App\Http\Controllers\Api\AuthController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Read-only operations — accessible with a 'read' or 'write' ability token
Route::middleware(['auth:sanctum', 'abilities:read'])->group(function () {
    Route::apiResource('customers', CustomerController::class)->only(['index', 'show']);
    Route::apiResource('leads', LeadController::class)->only(['index', 'show']);
    Route::apiResource('contacts', ContactController::class)->only(['index', 'show']);
    Route::apiResource('activities', ActivityController::class)->only(['index', 'show']);
});

// Write operations — require a 'write' ability token
Route::middleware(['auth:sanctum', 'abilities:write'])->group(function () {
    Route::apiResource('customers', CustomerController::class)->only(['store', 'update', 'destroy']);
    Route::apiResource('leads', LeadController::class)->only(['store', 'update', 'destroy']);
    Route::apiResource('contacts', ContactController::class)->only(['store', 'update', 'destroy']);
    Route::apiResource('activities', ActivityController::class)->only(['store', 'update', 'destroy']);
});