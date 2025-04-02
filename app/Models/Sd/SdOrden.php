<?php

namespace App\Models\Sd;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;

class SdOrden extends Model
{
    use HasFactory;
    
    protected $table = 'sd_orden';
    
    protected $fillable = [
        'ordId',
        'empId',
        'centroId',
        'almId',
        'ordNumber',// Número de onda
        'ordQty',// Cantidad de orden
        'ordestatus', // Estado del pedido P:Pendiente L:Liberado V:Verificado A:Almacenado
        'ordTip', // Tipo Salida / Entrada
        'ordTipDes',//Tipo Salida / Entrada
        'ordClase',//Clase 
        'ordClaseDes',//Clase 
        'ordHdrCustShortText1',//Direccion
        'ordHdrCustShortText2',//Ciudad
        'ordHdrCustShortText3',//Región
        'ordHdrCustShortText4',//Identificación de orden migrado
        'ordHdrCustShortText5',//Estado de la orden
        'ordHdrCustShortText6',//Teléfono
        'ordHdrCustShortText7',//Nombre
        'ordHdrCustShortText8',//Email
        'ordHdrCustShortText9',//Courier
        'ordHdrCustShortText10',//Latitud de la orden
        'ordHdrCustShortText11',// Lomgitud de la orden
        'ordHdrCustShortText12',//Clase de documento
        'ordHdrCustShortText13',//Ruta
        'ordHdrCustLongText1'//Comentarios
    ];

  /*  public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }
    
    public function lineas()
    {
        return $this->hasMany(LineaOrden::class);
    }
*/
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
