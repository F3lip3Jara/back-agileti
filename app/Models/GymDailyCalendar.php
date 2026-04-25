<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GymDailyCalendar extends Model
{
    protected $fillable = [
        'gym_branch_id', 'date', 'is_holiday', 
        'open_time', 'close_time', 'slot_duration_minutes'
    ];

    protected $casts = [
        'date' => 'date',
        'is_holiday' => 'boolean'
    ];

    public function branch()
    {
        return $this->belongsTo(GymBranch::class, 'gym_branch_id');
    }

    public function slots()
    {
        return $this->hasMany(GymSlot::class);
    }
}
