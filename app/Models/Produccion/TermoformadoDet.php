<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;

class TermoformadoDet extends Model
{
    use HasFactory;
    protected $table    ='termoformado_det';
    protected $fillable = [
       'idTerd',
       'idTer',
       'empId',
       'terdEst',
       'terdHorIni',
       'terdHorFin',  
       'terdUso', 
       'terdRol',   
       'terdLotExt',
       'terdRechazo',
       'terdLimp',
       'terdTem',
       'terdCaja',
       'terdTipo',
       'terdDefecto',
       'terdRechazo',
       'terdSani',
       'terdPesoUn',
       'terUnAlm',
       'terdidMot'
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
