<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;

class viewProveedores extends Model
{
    use HasFactory;
    protected $table    ='proveedores';

    protected $fillable = [
        'id',
        'rut',
        'nombre',
        'nombre_fantasia',
        'giro',
        'pais',
        'region',
        'comuna',
        'ciudad',
        'direccion',
        'numero',
        'telefono',
        'es_cliente',
        'es_proveedor',
        'mail',
        'activo',
        'created_at',
        'updated_at',
        'empId'
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

}
