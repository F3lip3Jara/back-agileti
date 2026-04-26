<?php

use App\Models\ViewCalendarStatus;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $slots = ViewCalendarStatus::with(['dailyCalendar.branch'])
          ->withCount(['reservations' => function ($q) {
              $q->where('status', 'confirmed');
          }])
          ->limit(1)
          ->get();
          
    $slots->map(function ($slot) {
        $slot->available_quota = max(0, $slot->max_quota - $slot->reservations_count);
        $slot->date = \Carbon\Carbon::parse($slot->dailyCalendar->date)->format('Y-m-d');
        return $slot;
    });
    
    echo json_encode($slots);
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage();
}
