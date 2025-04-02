<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;

class Impresion extends Model
{
    use HasFactory;
    protected $table    ='impresion';
    protected $fillable = [
        'idImp',
        'empId',
        'idOrdt',
        'idOrdtd',
        'impUso',
        'impEst',
        'impEstCtl',
        'impMaq',
        'impTip',
        'impPrdCaja',
        'impPrdBolsa',
        'impLotCaja',
        'impLotBolsa',
        'impObs',
        'impLotSal',
        'impTurn',
        'impReproceso',
        'impBasura',
        'impMerma',
        'impidTer'
        
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
