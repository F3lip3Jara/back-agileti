<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;

class Termoformado extends Model
{

    use HasFactory;

    protected $table    ='termoformado';
    protected $fillable = [
       'idTer',
       'empId',
       'idOrdt',
       'idOrdtd',
       'terUso',
       'terEst',
       'terEstCtl',
       'terMaq',  
       'terTip', 
       'terPrdCaja',
       'terPrdBolsa', 
       'terLotCaja',
       'terLotBolsa', 
       'terObs',
       'terLotSal',
       'terTurn',   
       'terCavTot', 
       'terCavAct', 
       'terRepro', 
       'terMerma',
      
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
