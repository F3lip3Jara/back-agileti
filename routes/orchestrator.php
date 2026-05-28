<?php

use App\Http\Controllers\Orchestrator\LinearWebhookController;
use App\Http\Controllers\Orchestrator\TaskController;
use Illuminate\Support\Facades\Route;


// Rutas del Orquestador de Tareas IA
Route::prefix('orchestrator')->group(function () {
    Route::get('/tasks', [TaskController::class, 'index']);
    Route::get('/tasks/{id}', [TaskController::class, 'show']);
    Route::post('/tasks/{id}/retry', [TaskController::class, 'retry']);
    Route::post('/tasks/{id}/cancel', [TaskController::class, 'cancel']);
});

// Receptor del Webhook de Linear
Route::post('/webhook/linear', [LinearWebhookController::class, 'handle']);


?>