<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Gym;
use App\Models\GymBranch;
use App\Models\GymCalendarConfig;
use App\Models\GymDailyCalendar;
use App\Models\GymSlot;
use Carbon\Carbon;

class GymSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Crear el Gimnasio (Asumimos empId 1 para la prueba)
        $gym = Gym::create([
            'empId' => 1,
            'name' => 'AgileTI Fitness Club',
            'status' => true,
        ]);

        // 2. Crear un par de Sedes
        $branchNorte = GymBranch::create([
            'gym_id' => $gym->id,
            'name' => 'Sede Norte',
            'address' => 'Av. Siempre Viva 123',
            'phone' => '+56912345678',
            'status' => true,
        ]);

        $branchSur = GymBranch::create([
            'gym_id' => $gym->id,
            'name' => 'Sede Sur',
            'address' => 'Calle Falsa 456',
            'phone' => '+56987654321',
            'status' => true,
        ]);

        // 3. Crear Configuración de Calendario (Lunes a Viernes para ambas sedes)
        $branches = [$branchNorte, $branchSur];
        
        foreach ($branches as $branch) {
            // Configurar Lunes a Viernes (1 al 5)
            for ($day = 1; $day <= 5; $day++) {
                GymCalendarConfig::create([
                    'gym_branch_id' => $branch->id,
                    'day_of_week' => $day,
                    'open_time' => '08:00:00',
                    'close_time' => '22:00:00',
                    'slot_duration_minutes' => 60,
                    'default_max_quota' => 20,
                    'is_open' => true,
                ]);
            }
            
            // Sábados medio día
            GymCalendarConfig::create([
                'gym_branch_id' => $branch->id,
                'day_of_week' => 6,
                'open_time' => '09:00:00',
                'close_time' => '14:00:00',
                'slot_duration_minutes' => 60,
                'default_max_quota' => 15, // Menos cupo los sábados
                'is_open' => true,
            ]);

            // Domingos cerrado
            GymCalendarConfig::create([
                'gym_branch_id' => $branch->id,
                'day_of_week' => 7,
                'open_time' => '00:00:00',
                'close_time' => '00:00:00',
                'slot_duration_minutes' => 60,
                'default_max_quota' => 0,
                'is_open' => false,
            ]);
        }

        // 4. Generar Calendario Diario y Bloques (Slots) para la semana actual
        $today = Carbon::today();
        
        foreach ($branches as $branch) {
            // Generar para hoy y los próximos 6 días (1 semana de muestra)
            for ($i = 0; $i < 7; $i++) {
                $date = $today->copy()->addDays($i);
                $dayOfWeek = $date->dayOfWeekIso; // 1 (Lunes) a 7 (Domingo)

                $config = GymCalendarConfig::where('gym_branch_id', $branch->id)
                    ->where('day_of_week', $dayOfWeek)
                    ->first();

                if ($config && $config->is_open) {
                    $dailyCalendar = GymDailyCalendar::create([
                        'gym_branch_id' => $branch->id,
                        'date' => $date->toDateString(),
                        'is_holiday' => false,
                        'open_time' => $config->open_time,
                        'close_time' => $config->close_time,
                        'slot_duration_minutes' => $config->slot_duration_minutes,
                    ]);

                    // Generar Bloques de Tiempo (Slots)
                    $startTime = Carbon::parse($config->open_time);
                    $endTime = Carbon::parse($config->close_time);

                    while ($startTime->copy()->addMinutes($config->slot_duration_minutes) <= $endTime) {
                        $slotEnd = $startTime->copy()->addMinutes($config->slot_duration_minutes);
                        
                        GymSlot::create([
                            'gym_daily_calendar_id' => $dailyCalendar->id,
                            'start_time' => $startTime->toTimeString(),
                            'end_time' => $slotEnd->toTimeString(),
                            'max_quota' => $config->default_max_quota,
                            'status' => true,
                        ]);

                        $startTime->addMinutes($config->slot_duration_minutes);
                    }
                }
            }
        }
    }
}
