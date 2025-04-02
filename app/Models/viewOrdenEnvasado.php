<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;

class viewOrdenEnvasado extends Model
{
    use HasFactory;
    protected $table    ='ordenes_de_envasado';

    protected $fillable = [
    'id',
    'id_envasado',
    'usuario',
    'orden_compra',
    'orden_produccion',
    'rut_cliente',
    'fecha',
    'estado_ord',
    'estado_pro',
    'observaciones',    
    'created_at',
    'updated_at'
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
