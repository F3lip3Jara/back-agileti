<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;

class Inyeccion extends Model
{
    use HasFactory;
    protected $table    ='inyeccion';
    protected $fillable = [
        'idIny',
        'empId',            
        'idOrdt',  
        'idOrdtd',          
        'inyUso',          
        'inyEst',
        'inyEstCtl',
        'inyMaq',  
        'inyTip', 
        'inyPrdCaja',
        'inyPrdBolsa',  
        'inyLotCaja',
        'inyLotBolsa',                  
        'inyObs',
        'inyLotSal',   
        'inyTurn',
        'inyidMez'

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
