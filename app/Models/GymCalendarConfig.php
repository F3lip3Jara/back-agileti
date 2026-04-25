<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GymCalendarConfig extends Model
{
    protected $fillable = [
        'gym_branch_id', 'day_of_week', 'open_time', 'close_time',
        'slot_duration_minutes', 'default_max_quota', 'is_open'
    ];

    protected $casts = [
        'is_open' => 'boolean'
    ];

    public function branch()
    {
        return $this->belongsTo(GymBranch::class, 'gym_branch_id');
    }
}
