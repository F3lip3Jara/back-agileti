<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GymReservation extends Model
{
    protected $fillable = [
        'user_id', 'gym_slot_id', 'status', 'attended'
    ];

    protected $casts = [
        'attended' => 'boolean'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function gymSlot()
    {
        return $this->belongsTo(GymSlot::class);
    }
}
