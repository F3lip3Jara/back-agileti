<?php

namespace App\Http\Controllers;

use App\Models\GymReservation;
use App\Models\GymSlot;
use Illuminate\Http\Request;

class GymReservationController extends Controller
{
    public function index(Request $request)
    {
        $userId = $request->input('idUser');
        $rolId = $request->input('rolId');

        // Si es admin, puede ver todas las reservas, si no, solo las suyas
        if ($rolId == 1 && $request->has('all')) {
            $reservations = GymReservation::with(['user', 'gymSlot.dailyCalendar.branch'])->get();
        } else {
            $reservations = GymReservation::with('gymSlot.dailyCalendar.branch')
                ->where('user_id', $userId)
                ->orderBy('created_at', 'desc')
                ->get();
        }

        return response()->json($reservations);
    }

    public function store(Request $request)
    {
        $userId = $request->input('idUser');
        
        $validated = $request->validate([
            'gym_slot_id' => 'required|exists:gym_slots,id',
        ]);

        $slot = GymSlot::with('dailyCalendar.branch')->findOrFail($validated['gym_slot_id']);

        // Verificar si el bloque está activo
        if (!$slot->status) {
            return response()->json(['error' => 'El bloque no está activo para reservas'], 400);
        }

        $dailyCalendar = $slot->dailyCalendar;

        // Verificar si la fecha ya pasó
        if (now()->toDateString() > $dailyCalendar->date || (now()->toDateString() == $dailyCalendar->date && now()->toTimeString() > $slot->start_time)) {
            return response()->json(['error' => 'No puedes reservar un bloque que ya pasó'], 400);
        }

        // REGLA: No puede reservar en dos sedes el mismo día. 
        $reservationsThatDay = GymReservation::where('user_id', $userId)
            ->where('status', 'confirmed')
            ->whereHas('gymSlot.dailyCalendar', function ($query) use ($dailyCalendar) {
                $query->where('date', $dailyCalendar->date);
            })
            ->with('gymSlot.dailyCalendar')
            ->get();

        foreach ($reservationsThatDay as $res) {
            if ($res->gymSlot->dailyCalendar->gym_branch_id != $dailyCalendar->gym_branch_id) {
                return response()->json(['error' => 'Ya tienes una reserva en otra sede para este día. No puedes reservar en múltiples sedes el mismo día.'], 400);
            }
            if ($res->gym_slot_id == $slot->id) {
                return response()->json(['error' => 'Ya tienes una reserva confirmada para este bloque exacto.'], 400);
            }
        }

        // Validación Concurrente de Cupo
        $confirmedCount = GymReservation::where('gym_slot_id', $slot->id)
            ->where('status', 'confirmed')
            ->count();

        if ($confirmedCount >= $slot->max_quota) {
            return response()->json(['error' => 'No hay cupos disponibles para este bloque'], 400);
        }

        // Guardar reserva
        $existing = GymReservation::where('user_id', $userId)->where('gym_slot_id', $slot->id)->first();
        
        if ($existing) {
            $existing->status = 'confirmed';
            $existing->save();
        } else {
            $existing = GymReservation::create([
                'user_id' => $userId,
                'gym_slot_id' => $slot->id,
            ]);
        }

        return response()->json($existing, 201);
    }

    public function destroy(Request $request, $id)
    {
        $userId = $request->input('idUser');
        $rolId = $request->input('rolId');

        $reservation = GymReservation::findOrFail($id);

        if ($reservation->user_id != $userId && $rolId != 1) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        $reservation->status = 'cancelled';
        $reservation->save();

        return response()->json(['message' => 'Reserva cancelada correctamente']);
    }

    public function markAttendance(Request $request, $id)
    {
        if ($request->input('rolId') != 1) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        $reservation = GymReservation::findOrFail($id);
        
        $validated = $request->validate([
            'attended' => 'required|boolean'
        ]);

        $reservation->attended = $validated['attended'];
        $reservation->save();

        return response()->json($reservation);
    }
}
