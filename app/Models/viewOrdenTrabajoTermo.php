<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;

class viewOrdenTrabajoTermo extends Model
{
    use HasFactory;

    protected $table    ='ordenes_de_termoformado';

    protected $fillable = [
    'id',
    'id_det',
    'id_termo',
    'usuario_genera ',
    'oc',
    'op',
    'orden_prod_fec ',
    'estado_orden',
    'estado_termo_op',
    'estado_termo_ctl',
    'prioridad',
    'producto',
    'cantidad_sol',
    'lote_salida',
    'tipo',
    'maquina',
    'turno',
    'tipo_caja',
    'lote_caja',
    'tipo_bolsa',
    'lote_bolsa',
    'created_at',
    'updated_at',
   


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
