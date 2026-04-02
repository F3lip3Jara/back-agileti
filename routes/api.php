<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Seguridad\TotpController;

// Rutas TOTP
Route::prefix('totp')->group(function () {
    Route::post('/setup', [TotpController::class, 'setup']);
    Route::post('/verify', [TotpController::class, 'verify']);
    Route::post('/status', [TotpController::class, 'status']);
    Route::post('/disable', [TotpController::class, 'disable']);
});
