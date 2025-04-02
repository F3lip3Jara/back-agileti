<?php

namespace App\Models\Sd;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Support\Facades\Config;

class Centro extends Model
{
   
    use HasFactory;


    protected $table    ='sd_centro';
    protected $fillable = [
        'centroId',
        'empId',
        'cenDes',
        'cenDir',
        'cenCap',
        'cenPlace',
        'cenContacto',// Persona o número de contacto
        'centEmail',// Correo de contacto
        'cenHoraApertura',// Horario de apertura
        'cenHoraCierre',// Horario de cierre
        'cenStockLimitWeb',// Stock máximo disponible para ventas web
        'cenStockLimiteRepo',// Stock para reabastecimiento interno
       'cenEstado', 
        'cenTelefono',// Extensión telefónica si aplica
        'cenLat',
        'cenLong'

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
