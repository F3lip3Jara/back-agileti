<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;

class InyeccionDet extends Model
{
    use HasFactory;

    use HasFactory;
    protected $table    ='inyeccion_det';
    protected $fillable = [
        'idInyd',
         'empId',      
         'idIny',                
        'inydEst',
        'inydHorIni',
        'inydHorFin',
        'inydUso',
        'inydRol',          
        'inydRechazo',
        'inydLimp',
        'inydConmutacion', 
        'inydCaja', 
        'inydTipo', 
        'inydidMot',
        'inydDefecto',
         'inydSani', 
        'inydPesoCaja', 
        'inydUnAlm',
        'inydFechVen',
        'inydTip',
        'inydObs'
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
