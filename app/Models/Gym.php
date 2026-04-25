<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Gym extends Model
{
    protected $fillable = ['empId', 'name', 'status'];

    protected $casts = [
        'status' => 'boolean'
    ];

    public function branches()
    {
        return $this->hasMany(GymBranch::class);
    }
}
