<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\LeadController;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\ActivityController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\EmailController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\UserManagementController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);
Route::get('/test', function () {
    return response()->json([
        'message' => 'API is working'
    ]);
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Profile (any authenticated user) + user administration (Admin-only,
// enforced inside UserManagementController). Used by the mobile app's
// Profile screen.
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/profile', [ProfileController::class, 'show']);
    Route::put('/profile', [ProfileController::class, 'update']);
    Route::put('/profile/password', [ProfileController::class, 'updatePassword']);

    Route::apiResource('users', UserManagementController::class)
        ->only(['index', 'store', 'update', 'destroy']);
});

Route::name('api.')->group(function () {

    // Read-only operations — accessible with a 'read' or 'write' ability token
    Route::middleware(['auth:sanctum', 'abilities:read'])->group(function () {
        Route::apiResource('customers', CustomerController::class)->only(['index', 'show']);
        Route::apiResource('leads', LeadController::class)->only(['index', 'show']);
        Route::apiResource('contacts', ContactController::class)->only(['index', 'show']);
        Route::apiResource('activities', ActivityController::class)->only(['index', 'show']);
        Route::post('/emails/send', [EmailController::class, 'send']);
    });

    // Write operations — require a 'write' ability token
    Route::middleware(['auth:sanctum', 'abilities:write'])->group(function () {
        Route::apiResource('customers', CustomerController::class)->only(['store', 'update', 'destroy']);
        Route::apiResource('leads', LeadController::class)->only(['store', 'update', 'destroy']);
        Route::apiResource('contacts', ContactController::class)->only(['store', 'update', 'destroy']);
        Route::apiResource('activities', ActivityController::class)->only(['store', 'update', 'destroy']);
        Route::post('/emails/send', [EmailController::class, 'send']);
    });

});