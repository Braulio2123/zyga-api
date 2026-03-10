<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\VehicleController;
use App\Http\Controllers\Api\V1\ServiceController;
use App\Http\Controllers\Api\V1\ProviderController;
use App\Http\Controllers\Api\V1\AssistanceRequestController;

Route::prefix('v1')->group(function () {
    Route::prefix('auth')->group(function () {
        Route::post('register', [AuthController::class, 'register']);
        Route::post('login', [AuthController::class, 'login']);
    });

    Route::middleware('auth:sanctum')->group(function () {
        Route::prefix('auth')->group(function () {
            Route::post('logout', [AuthController::class, 'logout']);
        });

        Route::get('me', function (Request $request) {
            return response()->json([
                'data' => $request->user(),
            ]);
        });

        Route::apiResource('vehicles', VehicleController::class);

        Route::get('services', [ServiceController::class, 'index']);
        Route::get('services/{id}', [ServiceController::class, 'show']);

        Route::apiResource('providers', ProviderController::class);
        Route::put('providers/{id}/services', [ProviderController::class, 'updateServices']);
        Route::put('providers/{id}/schedule', [ProviderController::class, 'updateSchedule']);

        Route::apiResource('assistance-requests', AssistanceRequestController::class);
        Route::patch('assistance-requests/{id}/cancel', [AssistanceRequestController::class, 'cancel']);
        Route::patch('assistance-requests/{id}/status', [AssistanceRequestController::class, 'changeStatus']);
    });
});