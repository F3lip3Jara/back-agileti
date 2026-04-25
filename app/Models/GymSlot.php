<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GymSlot extends Model
{
    protected $fillable = [
        'gym_daily_calendar_id', 'start_time', 'end_time', 
        'max_quota', 'status'
    ];

    protected $casts = [
        'status' => 'boolean'
    ];

    public function dailyCalendar()
    {
        return $this->belongsTo(GymDailyCalendar::class, 'gym_daily_calendar_id');
    }

    public function reservations()
    {
        return $this->hasMany(GymReservation::class);
    }

    public function getAvailableQuotaAttribute()
    {
        $confirmed = $this->reservations()->where('status', 'confirmed')->count();
        return max(0, $this->max_quota - $confirmed);
    }
}
