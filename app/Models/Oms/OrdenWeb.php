<?php

namespace App\Models\Oms;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;


class OrdenWeb extends Model
{
    use HasFactory;

    protected $table = 'vent_ordenes';
    
    protected $fillable = [
        'opedId',
        'empId',
        'cliId',// Agregado cliId
        'opedparentid',
        'opedstatus' ,// pending, processing, on-hold, completed, cancelled, refunded, failed
        'opedmoneda',
        'opedversion',
        'opedfechaCreacion', // Cambio de date a timestamp
        'opedpreciosIncluyenImpuestos',
        'opeddescuentoTotal',
        'opeddescuentoImpuesto',
        'opedenvioTotal',
        'opedenvioImpuesto',
        'opedimpuestoCarrito',
        'opedtotal',
        'opedtotalImpuesto',
        'opedclaveOrden',
        'opedMetodoPago',
        'opedtituloMetodoPago',
        'opeddireccionIpCliente',
        'opedEntrega',
        'opePlace',
        'opeComCod',
        'userAgentCliente',
        'opedcarritoHash',
        'opedidExt',
        'clasTipId'
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

    
 public function scopeFilter($query, $filter) { 
    foreach($filter as $item){ 
                        
        $column = $item->column; 
        if( count( $item->values ) > 0 && $column != ""){  
            foreach($item->values as $values){
                $query->orWhere($column, 'like', '%' . $values. '%');
            }                  
        }else{
            
            if($column != "" && count( $item->values ) > 0 ){
                $query->where($item->column, 'LIKE', '%' . $item->values[0]. '%');
            }
           
        }      
     
    }
 }

}
