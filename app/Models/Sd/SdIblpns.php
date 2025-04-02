<?php

namespace App\Models\Sd;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;

class SdIblpns extends Model
{
    use HasFactory;
    protected $table    ='sd_iblpns';
    protected $fillable = [
        'iblpnId',
        'empId',
        'prdId',
        'iblpnQty',
        'iblpnOriginalBarcode',
        'iblpnStatus', //P: Pendiente, A: Almacenado, R: Reservado, T: En tránsito
        'iblpnType', //I: Ingreso, E: Egreso
        'iblpnHdrCustShortText1', //Orden de SD
        'iblpnHdrCustShortText2', //Id de la Orden de SD
        'iblpnHdrCustShortText3', //Sector destino 
        'iblpnHdrCustShortText4', //Sector Código
        'iblpnHdrCustShortText5', // Cantidad Orignal
        'iblpnHdrCustShortText6',
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

    public static function generateUniqueBarcode()
    {
        // Obtén el último código generado
        $lastBarcode = self::query()
            ->where('iblpnOriginalBarcode', 'like', 'IBLPN%')
            ->orderBy('iblpnOriginalBarcode', 'desc')
            ->value('iblpnOriginalBarcode');

        // Extrae el número del último código, si existe
        $lastNumber = $lastBarcode ? intval(substr($lastBarcode, 5)) : 0;

        // Incrementa el número y genera el nuevo código
        $newNumber = $lastNumber + 1;

        return 'IBLPN' . str_pad($newNumber, 7, '0', STR_PAD_LEFT);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (!$model->iblpnOriginalBarcode) {
                $model->iblpnOriginalBarcode = self::generateUniqueBarcode();
            }
        });
    }


}
