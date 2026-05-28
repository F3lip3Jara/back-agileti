<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Seguridad\TotpController;
use App\Http\Controllers\Orchestrator\TaskController;
use App\Http\Controllers\Orchestrator\LinearWebhookController;

// Rutas TOTP
Route::prefix('totp')->group(function () {
    Route::post('/setup', [TotpController::class, 'setup']);
    Route::post('/verify', [TotpController::class, 'verify']);
    Route::post('/status', [TotpController::class, 'status']);
    Route::post('/disable', [TotpController::class, 'disable']);
});
