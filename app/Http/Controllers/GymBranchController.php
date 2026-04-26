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

    public function getConfig(Request $request, $branchId)
    {
        if ($request->input('rolId') != 1) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        $configs = \App\Models\GymCalendarConfig::where('gym_branch_id', $branchId)
            ->orderBy('day_of_week')
            ->get();

        // Si no existen configuraciones para esta sede, inicializamos las de lunes a domingo vacías
        if ($configs->count() === 0) {
            for ($day = 1; $day <= 7; $day++) {
                \App\Models\GymCalendarConfig::create([
                    'gym_branch_id' => $branchId,
                    'day_of_week' => $day,
                    'open_time' => '08:00:00',
                    'close_time' => '22:00:00',
                    'slot_duration_minutes' => 60,
                    'default_max_quota' => 20,
                    'is_open' => ($day === 7) ? false : true // Domingo cerrado por defecto
                ]);
            }
            $configs = \App\Models\GymCalendarConfig::where('gym_branch_id', $branchId)
                ->orderBy('day_of_week')
                ->get();
        }

        return response()->json($configs);
    }

    public function updateConfig(Request $request)
    {
        if ($request->input('rolId') != 1) {
            return response()->json(['error' => 'No autorizado'], 403);
        }



        $configsData = $request->all();

        foreach ($configsData as $data) {
            if (!is_array($data)) continue;

            $branchId = $data['gym_branch_id'];


            $affected = \App\Models\GymCalendarConfig::where('gym_branch_id', $branchId)
                ->where('day_of_week', $data['day_of_week'])
                ->update(
                    [
                        'open_time' => $data['open_time'],
                        'close_time' => $data['close_time'],
                        'slot_duration_minutes' => $data['slot_duration_minutes'],
                        'default_max_quota' => $data['default_max_quota'],
                        'is_open' => $data['is_open']
                    ]
                );

            // Actualizar los slots futuros que ya fueron generados para este día de la semana
            $today = \Carbon\Carbon::today()->toDateString();
            $dailyCalendars = \App\Models\GymDailyCalendar::where('gym_branch_id', $branchId)
                ->where('date', '>=', $today)
                // MySQL WEEKDAY() devuelve 0 (Lunes) a 6 (Domingo). Nuestra config day_of_week es 1 (Lunes) a 7 (Domingo).
                ->whereRaw('WEEKDAY(date) + 1 = ?', [$data['day_of_week']])
                ->pluck('id');

            if ($dailyCalendars->isNotEmpty()) {
                \App\Models\GymSlot::whereIn('gym_daily_calendar_id', $dailyCalendars)
                    ->update([
                        'max_quota' => $data['default_max_quota'],
                        'status' => $data['is_open']
                    ]);
            }
        }

        return response()->json(['message' => 'Configuración actualizada']);
    }
}
