<?php

namespace App\Models\Parametros;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;

class Producto extends Model
{
    use HasFactory;

    protected $table    ='parm_producto';
    protected $fillable = [
        'prdId',
        'prdCod',
        'prdDes',
        'prdObs',
        'prdRap',
        'prdEan',
        'prdTip',
        'prdCost',
        'prdNet',
        'prdBrut',
        'prdInv',
        'prdPes',
        'prdMin',
        'monId',
        'grpId',
        'grpsId',
        'unId',
        'colId',
        'empId',
        'prdIdExt',
        'prdUrl',
        'prdMig',
        'tallaId',
        'prdAncho',
        'prdLargo',
        'prdAlto',
        'prdPeso',
        'prdVolumen'
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

}
