<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;

class OrdenVentaDet extends Model
{
    use HasFactory;   

    protected $table    ='ord_venta_det';
    protected $fillable = [
       'idOrvd',
       'idOrv',
       'empId',
       'idMon',
       'orpvPrdCod',
       'orpvPrdDes',
       'orpvPrdCost',
       'orpvPrdNet',       
       'orpvCant',
       'orpvPrecio',
       'orpvDesc',
       'orpvObs'
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
