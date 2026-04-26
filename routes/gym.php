<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GymSlotController;
use App\Http\Controllers\GymReservationController;
use App\Http\Controllers\GymController;
use App\Http\Controllers\GymBranchController;
use App\Http\Controllers\GymProfileController;

// Rutas para Gym (dentro de postMiddleware por lo que el usuario está autenticado)
Route::prefix('gym')->group(function () {

    // Perfiles
    Route::get('/profile/me', [GymProfileController::class, 'myProfile']);
    Route::put('/profile/me', [GymProfileController::class, 'updateMyProfile']);

    // Profesor
    Route::get('/teacher/students', [GymProfileController::class, 'searchStudents']);
    Route::put('/teacher/students/{id}/routine', [GymProfileController::class, 'updateStudentRoutine']);

    // Admin (Restricciones)
    Route::get('/admin/students/{id}/restrictions', [GymProfileController::class, 'getStudentRestrictions']);
    Route::post('/admin/students/{id}/restrictions/toggle', [GymProfileController::class, 'toggleBranchRestriction']);

    // Admin (Configuracion Horarios)
    Route::get('/branches/{id}/config', [GymBranchController::class, 'getConfig']);
    Route::post('/branches/confing', [GymBranchController::class, 'updateConfig']);

    // Gyms y Sedes (Admin)
    Route::get('/gyms', [GymController::class, 'index']);
    Route::post('/gyms', [GymController::class, 'store']);
    Route::put('/gyms/{id}', [GymController::class, 'update']);
    Route::delete('/gyms/{id}', [GymController::class, 'destroy']);

    Route::get('/branches', [GymBranchController::class, 'index']);
    Route::post('/branches', [GymBranchController::class, 'store']);
    Route::put('/branches/{id}', [GymBranchController::class, 'update']);
    Route::delete('/branches/{id}', [GymBranchController::class, 'destroy']);

    // Slots (Bloques de horario)
    Route::get('/slots', [GymSlotController::class, 'index']); // Ver todos los bloques futuros
    Route::post('/slots', [GymSlotController::class, 'store']); // Crear bloque (Admin)
    Route::put('/slots/{id}', [GymSlotController::class, 'update']); // Editar bloque
    Route::delete('/slots/{id}', [GymSlotController::class, 'destroy']); // Eliminar bloque

    // Reservas
    Route::get('/reservations', [GymReservationController::class, 'index']); // Ver mis reservas (Alumno) o todas (Admin)
    Route::post('/reservations', [GymReservationController::class, 'store']); // Reservar un bloque
    Route::delete('/reservations/{id}', [GymReservationController::class, 'destroy']); // Cancelar reserva

    // Obtener inscritos en un bloque específico (para admin/profesor)
    Route::get('/slots/{id}/attendees', [GymSlotController::class, 'attendees']);

    // Marcar asistencia (para admin/profesor)
    Route::put('/reservations/{id}/attendance', [GymReservationController::class, 'markAttendance']);
});
