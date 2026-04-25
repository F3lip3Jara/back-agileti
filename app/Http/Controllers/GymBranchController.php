<?php

namespace App\Http\Controllers;

use App\Models\GymBranch;
use Illuminate\Http\Request;

class GymBranchController extends Controller
{
    public function index(Request $request)
    {
        if ($request->input('rolId') != 1) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        $branches = GymBranch::with('gym')->get();
        return response()->json($branches);
    }

    public function store(Request $request)
    {
        if ($request->input('rolId') != 1) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        $validated = $request->validate([
            'gym_id' => 'required|exists:gyms,id',
            'name' => 'required|string|max:150',
            'address' => 'nullable|string',
            'phone' => 'nullable|string',
            'status' => 'boolean'
        ]);

        $branch = GymBranch::create($validated);
        return response()->json($branch, 201);
    }

    public function update(Request $request, $id)
    {
        if ($request->input('rolId') != 1) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        $branch = GymBranch::findOrFail($id);

        $validated = $request->validate([
            'name' => 'string|max:150',
            'address' => 'nullable|string',
            'phone' => 'nullable|string',
            'status' => 'boolean'
        ]);

        $branch->update($validated);
        return response()->json($branch);
    }

    public function destroy(Request $request, $id)
    {
        if ($request->input('rolId') != 1) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        $branch = GymBranch::findOrFail($id);
        $branch->delete();

        return response()->json(['message' => 'Sede eliminada']);
    }
}
