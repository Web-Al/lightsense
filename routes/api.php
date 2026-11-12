<?php

use App\Http\Controllers\Api\ReadingController;
use App\Http\Middleware\AuthenticateDevice;
use Illuminate\Support\Facades\Route;

// These addresses start with /api automatically.
// Every request must include the device key. Max 120 requests per minute.
Route::middleware(['throttle:120,1', AuthenticateDevice::class])->group(function () {
    Route::get('/ping', [ReadingController::class, 'ping']);       // GET  /api/ping
    Route::post('/readings', [ReadingController::class, 'store']); // POST /api/readings
});
