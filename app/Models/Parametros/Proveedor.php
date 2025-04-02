<?php

namespace App\Models\Parametros;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;

class Proveedor extends Model
{
    use HasFactory;

    protected $table    ='parm_proveedor';
    protected $fillable = [
        'idPrv',
        'prvRut',
        'prvNom',
        'prvNom2',
        'prvGiro',
        'prvNum',
        'prvDir',
        'prvTel',
        'prvCli',
        'prvPrv',
        'prvMail',
        'paiId',
        'regId',
        'comId',
        'ciuId',
        'empId',
        'prvAct',
        'prvLat',
        'prvLong',
        'prvPlace'

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
