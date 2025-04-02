<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;

class OrdenVenta extends Model
{
    use HasFactory;   

    protected $table    ='ord_venta';
    protected $fillable = [
       'idOrv',
       'empId',
       'idPrv',
       'orvNumRea',
       'orvFech',
       'orvUsrG',
       'orvObs',
       'orvEst',
       'orvEstTrans',
       'orvNumTrj',
       'orvPrecioTot',
       'orvPrecioIva',
       'orvPrecioPag',       
       'idTipPag',
       

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
