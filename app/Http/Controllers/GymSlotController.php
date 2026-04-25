<?php

namespace App\Http\Controllers;

use App\Models\GymSlot;
use Illuminate\Http\Request;

class GymSlotController extends Controller
{
    public function index(Request $request)
    {
        // Obtener bloques de fecha actual en adelante, unidos a su calendario diario
        // Para esto usamos whereHas
        $slots = GymSlot::with(['dailyCalendar.branch'])
          ->withCount(['reservations' => function ($query) {
              $query->where('status', 'confirmed');
          }])
          ->whereHas('dailyCalendar', function($query) {
              $query->where('date', '>=', now()->toDateString());
          })
          // Ordenamos a través de un join para poder ordenar por fecha del daily_calendar
          ->join('gym_daily_calendars', 'gym_slots.gym_daily_calendar_id', '=', 'gym_daily_calendars.id')
          ->orderBy('gym_daily_calendars.date')
          ->orderBy('gym_slots.start_time')
          ->select('gym_slots.*') // Importante seleccionar solo slots para no sobreescribir ids
          ->get();

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
