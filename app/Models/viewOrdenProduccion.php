<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;

class viewOrdenProduccion extends Model
{
    use HasFactory;
    protected $table    ='orden_produccion';

    protected $fillable = [
            'empId',
            'id',
            'usuario',
            'orden_compra',
            'orden_produccion',
            'proveedor',
            'prv_telefono',
            'proveedor_id',
            'rut',
            'fecha',
            'estado_ord',
            'estado_pro',
            'observaciones',
            'prd_total',
            'tipo',
            'tipo_des',
            'tipo_id',
            'almacen_id',
            'almacen_destino',
            'centro_id',
            'centro_destino',
            'latitud',
            'longitud',
            'fech_promesa',
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
