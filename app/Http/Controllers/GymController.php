<?php

namespace App\Http\Controllers;

use App\Models\Gym;
use Illuminate\Http\Request;

class GymController extends Controller
{
    public function index(Request $request)
    {
        // Admin
        if ($request->input('rolId') != 1) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        $gyms = Gym::with('branches')->get();
        return response()->json($gyms);
    }

    public function store(Request $request)
    {
        if ($request->input('rolId') != 1) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'status' => 'boolean'
        ]);

        // Asignamos a la empresa empId=1 por defecto para la arquitectura actual
        $validated['empId'] = 1;

        $gym = Gym::create($validated);
        return response()->json($gym, 201);
    }

    public function update(Request $request, $id)
    {
        if ($request->input('rolId') != 1) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        $gym = Gym::findOrFail($id);

        $validated = $request->validate([
            'name' => 'string|max:150',
            'status' => 'boolean'
        ]);

        $gym->update($validated);
        return response()->json($gym);
    }

    public function destroy(Request $request, $id)
    {
        if ($request->input('rolId') != 1) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        $gym = Gym::findOrFail($id);
        $gym->delete();

        return response()->json(['message' => 'Gimnasio eliminado']);
    }
}
