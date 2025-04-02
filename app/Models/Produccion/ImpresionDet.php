<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;

class ImpresionDet extends Model
{
    use HasFactory;
    protected $table    ='impresion_det';
    protected $fillable = [
        'idImpd',
        'empId',
        'idImp',
        'impdHorIni',
        'impdHorFin',
        'impdPesoCaja',
        'impdCajaAcu',
        'impdidMot',
        'impdDefecto', 
        'impdUso',
        'impdRol',          
        'impdTip',
        'impdEst'

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
