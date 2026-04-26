<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GymBranchRestriction extends Model
{
    protected $fillable = [
        'user_id',
        'gym_branch_id'
    ];
    
    public function branch()
    {
        return $this->belongsTo(GymBranch::class, 'gym_branch_id');
    }
}
