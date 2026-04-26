<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Support\Facades\Config;

class ViewCalendarStatus extends Model
{
    protected $table = 'calendar_status';
    public $timestamps = false;

    protected $fillable = [
        'id',
        'gym_id',
        'gym_branch_id',
        'date',
        'start_time',
        'end_time',
        'max_quota',
        'status',
        'created_at',
        'updated_at',

    ];

    public function getCreatedAtAttribute($value)
    {
        return Carbon::createFromTimestamp(strtotime($value))
            ->timezone(Config::get('app.timezone'))
            ->toDateTimeString();
    }

    public function getUpdatedAtAttribute($value)
    {
        return Carbon::createFromTimestamp(strtotime($value))
            ->timezone(Config::get('app.timezone'))
            ->toDateTimeString();
    }

    public function dailyCalendar()
    {
        return $this->belongsTo(GymDailyCalendar::class, 'gym_daily_calendar_id');
    }

    public function reservations()
    {
        return $this->hasMany(GymReservation::class, 'gym_slot_id', 'id');
    }
}
