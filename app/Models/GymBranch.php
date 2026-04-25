<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GymBranch extends Model
{
    protected $fillable = ['gym_id', 'name', 'address', 'phone', 'status'];

    protected $casts = [
        'status' => 'boolean'
    ];

    public function gym()
    {
        return $this->belongsTo(Gym::class);
    }

    public function calendarConfigs()
    {
        return $this->hasMany(GymCalendarConfig::class);
    }

    public function dailyCalendars()
    {
        return $this->hasMany(GymDailyCalendar::class);
    }
}
