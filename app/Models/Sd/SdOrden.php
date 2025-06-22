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
        'ordClase',//CODIGO Clase 
        'ordClaseDes',//Descripcion Clase 
        'ordHdrCustShortText1',//Documento relacionado
        'ordHdrCustShortText2',//Proveedor  / Cliente
        'ordHdrCustShortText3',//Nombre Proveedor / Cliente
        'ordHdrCustShortText4',//Fecha de la orden
        'ordHdrCustShortText5',//Fecha promesa entrega
        'ordHdrCustShortText6',//Cantidad de lineas       
        'ordHdrCustShortText7',//
        'ordHdrCustShortText8',//
        'ordHdrCustShortText9',//
        'ordHdrCustShortText10',//
        'ordHdrCustShortText11',//
        'ordHdrCustShortText12',//
        'ordHdrCustShortText13',//
        'ordHdrCustLongText1'//
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


public function scopeFilter($query, $filter) { 

   
    foreach($filter as $item){ 
                        
        $column = $item->column; 
        $count  = 0;
        $fecha_inicio = Carbon::create(2025, 1, 1);
        $fecha_fin = Carbon::create(2025, 1, 31);
        
        if($column == "created_at"){
            foreach($item->values as $value){
                if($count == 0){
                    $fecha_inicio = Carbon::create($value);
                }else{
                    $fecha_fin = Carbon::create($value);
                }
                $count++;
            }    
            $query->whereBetween('created_at', [$fecha_inicio, $fecha_fin]);
        }else{
            if( count( $item->values ) > 0 && $column != ""){  
                $count = 0;
                foreach($item->values as $values){
                    if($count == 0){
                        $query->where($column, 'like', '%' . $values. '%');
                    }else{
                        $query->orWhere($column, 'like', '%' . $values. '%');
                    }
                    $count++;
                }             
            }else{                
                if($column != "" && count( $item->values ) > 0 ){
                    $query->where($item->column, 'LIKE', '%' . $item->values[0]. '%');
                }
               
            } 
        }
     
    }
 }



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
