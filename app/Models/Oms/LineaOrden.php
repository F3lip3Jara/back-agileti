<?php

namespace App\Models\Oms;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;

class LineaOrden extends Model
{
    use HasFactory;

    protected $table = 'vent_lineas_ordenes';
    
    protected $fillable = [
        'opedId',
        'empId',
        'opeddId',// Agregado cliId
        'opeddproductoId',
        'opeddnombreProducto',
        'opeddsubtotal',
        'opeddtotal',
        'opeddcantidad',
        'centroId',        
        'almId'
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
