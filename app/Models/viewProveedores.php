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
        'activo'
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
