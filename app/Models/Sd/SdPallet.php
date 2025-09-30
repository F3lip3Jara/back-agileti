<?php

namespace App\Models\Sd;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;

class SdPallet extends Model
{
    protected $table = 'sd_pallets';
    protected $primaryKey = 'palletId';
    public $timestamps = true;
    protected $fillable = ['pall_codigo', 'empId', 'centroId', 'almId', 'sectorId', 'ubicacionId', 'pall_estado'];

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

    public function centro()
    {
        return $this->belongsTo(Centro::class, 'centroId');
    }
    public function almacen()
    {
        return $this->belongsTo(Almacen::class, 'almId');
    }
    public function sector()
    {
        return $this->belongsTo(Sector::class, 'sectorId');
    }
    public function ubicacion()
    {
        return $this->belongsTo(SdUbicaciones::class, 'ubicacionId');
    }

    public function palletDet()
    {
        return $this->hasMany(SdPalletDet::class, 'palletId');
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
