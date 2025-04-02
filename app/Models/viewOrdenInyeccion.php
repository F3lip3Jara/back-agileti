<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;

class viewOrdenInyeccion extends Model
{
    use HasFactory;
    protected $table    ='ordenes_de_inyeccion';

    protected $fillable = [
        'id',
        'id_det',
        'id_inye',
        'usuario_genera',
        'oc',
        'op',
        'orden_prod_fec',
        'estado_orden',
        'estado_inye_op',
        'estado_inye_ctl',
        'prioridad',
        'producto',
        'cantidad_sol',
        'lote_salida',
        'tipo',
        'maquina',
        'turno',
        'lote_mezcla',
        'tipo_caja',
        'lote_caja',
        'tipo_bolsa',
        'lote_bolsa'
        
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
