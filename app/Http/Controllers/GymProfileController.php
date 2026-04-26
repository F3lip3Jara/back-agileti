<?php

namespace App\Http\Controllers;

use App\Models\GymProfile;
use App\Models\GymBranchRestriction;
use App\Models\Parametros\Empleado;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GymProfileController extends Controller
{
    // Obtiene el perfil del usuario autenticado (Alumno o Profesor)
    public function myProfile(Request $request)
    {
        $userId = $request['idUser'];

        $profile = GymProfile::firstOrCreate(
            ['user_id' => $userId],
            ['type' => 'student'] // Por defecto es estudiante si no existe
        );

        // Agregamos la info básica de usuario
        $user = Empleado::where('id', $userId)->first();

        return response()->json([
            'profile' => $profile,
            'user' => [
                'name' => $user->emploNom,
                'email' => $user->email,
                'empleado' => $user->empleado
            ]
        ]);
    }

    // Permite al alumno actualizar sus datos físicos
    public function updateMyProfile(Request $request)
    {
        $userId = $request['idUser'];

        $validated = $request->validate([
            'gender' => 'nullable|in:M,F,O',
            'activity_level' => 'nullable|in:baja,media,alta',
            'weight' => 'nullable|numeric|min:20|max:300',
            'height' => 'nullable|numeric|min:50|max:250',
            'medical_conditions' => 'nullable|string'
        ]);

        $profile = GymProfile::where('user_id', $userId)->firstOrFail();

        // Un estudiante no puede editar su rutina ni su tipo de perfil aquí
        $profile->update($validated);

        return response()->json($profile);
    }

    // --- MÉTODOS PARA EL PROFESOR ---

    // Busca alumnos
    public function searchStudents(Request $request)
    {
        // Verificar si el usuario actual es profesor
        $userId = $request['idUser'];
        $myProfile = GymProfile::where('user_id', $userId)->first();

        // TODO: En producción validar también roles de sysAdmin
        if (!$myProfile || $myProfile->type !== 'teacher') {
            // Permitir a admins (rol 1) también
            if ($request->input('rolId') != 1) {
                return response()->json(['error' => 'Acceso denegado. Solo profesores.'], 403);
            }
        }

        $query = $request->input('q');

        // Buscar en usuarios/empleados
        $students = User::whereHas('gymProfile', function ($q) {
            $q->where('type', 'student');
        })
            ->with(['gymProfile', 'empleado'])
            ->where(function ($q) use ($query) {
                if ($query) {
                    $q->where('name', 'like', "%$query%")
                        ->orWhere('email', 'like', "%$query%")
                        ->orWhereHas('empleado', function ($q2) use ($query) {
                            $q2->where('emploNom', 'like', "%$query%")
                                ->orWhere('emploApe', 'like', "%$query%");
                        });
                }
            })
            ->take(50)
            ->get();

        return response()->json($students);
    }

    // El profesor actualiza la rutina de un alumno
    public function updateStudentRoutine(Request $request, $studentUserId)
    {
        $userId = $request->input('idUser');
        $myProfile = GymProfile::where('user_id', $userId)->first();

        if (!$myProfile || $myProfile->type !== 'teacher') {
            if ($request->input('rolId') != 1) {
                return response()->json(['error' => 'Acceso denegado. Solo profesores.'], 403);
            }
        }

        $validated = $request->validate([
            'routine' => 'required|string'
        ]);

        $studentProfile = GymProfile::where('user_id', $studentUserId)->firstOrFail();
        $studentProfile->update(['routine' => $validated['routine']]);

        return response()->json($studentProfile);
    }

    // --- MÉTODOS PARA ADMIN (Restricciones) ---
    public function getStudentRestrictions($studentUserId)
    {
        $restrictions = GymBranchRestriction::where('user_id', $studentUserId)
            ->with('branch')
            ->get();
        return response()->json($restrictions);
    }

    public function toggleBranchRestriction(Request $request, $studentUserId)
    {
        if ($request->input('rolId') != 1) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        $validated = $request->validate([
            'gym_branch_id' => 'required|exists:gym_branches,id'
        ]);

        $existing = GymBranchRestriction::where('user_id', $studentUserId)
            ->where('gym_branch_id', $validated['gym_branch_id'])
            ->first();

        if ($existing) {
            $existing->delete();
            return response()->json(['message' => 'Restricción eliminada, ahora tiene acceso']);
        } else {
            GymBranchRestriction::create([
                'user_id' => $studentUserId,
                'gym_branch_id' => $validated['gym_branch_id']
            ]);
            return response()->json(['message' => 'Restricción agregada, acceso denegado a esta sede']);
        }
    }
}
