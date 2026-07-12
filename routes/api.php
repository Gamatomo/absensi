<?php

use App\Http\Controllers\Api\V1\DeviceAttendanceController;
use App\Http\Controllers\Api\V1\DeviceAuthController;
use App\Http\Controllers\Api\V1\DeviceConfigController;
use App\Http\Controllers\Api\V1\DeviceHeartbeatController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function (): void {
    Route::post('/device/login', [DeviceAuthController::class, 'login']);
    
    // RFID Scan endpoints (Cloud architecture)
    Route::post('/device/scan', [DeviceAttendanceController::class, 'storeScan']);
    Route::get('/device/last-scan', [DeviceAttendanceController::class, 'getLastScan']);

    Route::middleware('auth:sanctum')->group(function (): void {
        Route::post('/device/attendance-events', [DeviceAttendanceController::class, 'store']);
        Route::post('/device/heartbeat', [DeviceHeartbeatController::class, 'store']);
        Route::get('/device/config', [DeviceConfigController::class, 'show']);
    });
});
