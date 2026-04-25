<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\GymBranch;
use App\Models\GymCalendarConfig;
use App\Models\GymDailyCalendar;
use App\Models\GymSlot;
use Carbon\Carbon;

class GenerateGymCalendar extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gym:generate-calendar {days=30 : Number of days to generate}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generates daily calendars and slots based on weekly configuration for all active branches';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $daysToGenerate = (int) $this->argument('days');
        $branches = GymBranch::where('status', true)->get();

        if ($branches->isEmpty()) {
            $this->warn('No active branches found.');
            return 0;
        }

        $this->info("Generating calendar for the next {$daysToGenerate} days...");
        $bar = $this->output->createProgressBar($branches->count() * $daysToGenerate);
        $bar->start();

        foreach ($branches as $branch) {
            for ($i = 0; $i < $daysToGenerate; $i++) {
                $date = Carbon::today()->addDays($i);
                $dayOfWeek = $date->dayOfWeekIso; // 1 (Mon) - 7 (Sun)

                // Skip if DailyCalendar already exists for this branch and date
                $exists = GymDailyCalendar::where('gym_branch_id', $branch->id)
                    ->where('date', $date->toDateString())
                    ->exists();

                if ($exists) {
                    $bar->advance();
                    continue;
                }

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
                
                $bar->advance();
            }
        }

        $bar->finish();
        $this->newLine();
        $this->info('Gym calendar generated successfully!');
        
        return 0;
    }
}
