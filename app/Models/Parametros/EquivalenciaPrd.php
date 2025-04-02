<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;

class EquivalenciaPrd extends Model
{
    use HasFactory;
    protected $table    ='prd_equivalencia';
    protected $fillable = [
        'idEqui',
        'empId',
        'idPrd',
        'equiPrdBulto',
        'equiBultPallet',
        'equiPrdBins'        
    ];

    public function getCreatedAtAttribute($value){
        return Carbon::createFromTimestamp(strtotime($value))
        ->timezone(Config::get('app.timezone'))
        ->toDateTimeString();
    }
        
    public function getUpdatedAtAttribute($value){
        return Carbon::createFromTimestamp(strtotime($value))
        ->timezone(Config::get('app.timezone'))
        ->toDateTimeString();
    }
}
