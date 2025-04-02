<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;

class Envasado extends Model
{
    use HasFactory;
    protected $table    ='envasado';
    protected $fillable = [
        'idEnv',
        'empId',
        'idTer',
        'envTurn',
        'envLotSal',
        'envPrdCaja',
        'envPrdBolsa',
        'envLotCaja',
        'envLotBolsa',
        'envMaq',
        'envEst',
        'envEstCtl',
        'envObs'
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
