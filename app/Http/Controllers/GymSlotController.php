<?php

namespace App\Http\Controllers;

use App\Models\GymSlot;
use Illuminate\Http\Request;
use App\Models\ViewCalendarStatus;

class GymSlotController extends Controller
{
    public function index(Request $request)
    {
        $query = \App\Models\ViewCalendarStatus::with(['dailyCalendar.branch'])
          ->withCount(['reservations' => function ($query) {
              $query->where('status', 'confirmed');
          }]);

        if ($request->has('branch_id')) {
            $branchId = $request->input('branch_id');
            $query->whereHas('dailyCalendar', function($q) use ($branchId) {
                $q->where('gym_branch_id', $branchId);
            });
        }

        $slots = $query->get();

        // Mapear para agregar atributo de cupos disponibles fácilmente
        // Y aplanar la fecha para el frontend si la necesita directa
        $slots->map(function ($slot) {
            $slot->available_quota = max(0, $slot->max_quota - $slot->reservations_count);
            // Formatear a Y-m-d para que FullCalendar no falle en el parseo
            $slot->date = \Carbon\Carbon::parse($slot->dailyCalendar->date)->format('Y-m-d');
            return $slot;
        });

        return response()->json($slots);
    }

    public function store(Request $request)
    {
        // Verificar si es admin (rolId 1 es admin según sysAdmin.php)
        if ($request->input('rolId') != 1) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        $validated = $request->validate([
            'gym_daily_calendar_id' => 'required|exists:gym_daily_calendars,id',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'max_quota' => 'integer|min:1',
            'status' => 'boolean'
        ]);

        $slot = GymSlot::create($validated);
        return response()->json($slot, 201);
    }

    public function update(Request $request, $id)
    {
        if ($request->input('rolId') != 1) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        $slot = GymSlot::findOrFail($id);

        $validated = $request->validate([
            'start_time' => 'date_format:H:i',
            'end_time' => 'date_format:H:i|after:start_time',
            'max_quota' => 'integer|min:1',
            'status' => 'boolean'
        ]);

        $slot->update($validated);
        return response()->json($slot);
    }

    public function destroy(Request $request, $id)
    {
        if ($request->input('rolId') != 1) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        $slot = GymSlot::findOrFail($id);
        $slot->delete();

        return response()->json(['message' => 'Bloque eliminado correctamente']);
    }

    public function attendees(Request $request, $id)
    {
        if ($request->input('rolId') != 1) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        $slot = GymSlot::with(['reservations.user'])->findOrFail($id);
        return response()->json($slot);
    }
}
