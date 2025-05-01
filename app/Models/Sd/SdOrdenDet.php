<?php

namespace App\Models\Sd;

use App\Models\Parametros\Producto;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;


class SdOrdenDet extends Model
{
    use HasFactory;

    protected $table = 'sd_orden_det';
    
    protected $fillable = [
        'empId',
        'orddId',
        'centroId',
        'almId',// Agregado cliId
        'ordId',
        'orddNumber',
        'orddQtySol',
        'orddQtyAsig',
        'orddCantRecep',
        'ordDtlCustShortText1', 
        'ordDtlCustShortText2',
        'ordDtlCustShortText3',
        'ordDtlCustShortText4',
        'ordDtlrCustShortText5',
        'ordDtlCustShortText6',
        'ordDtlCustShortText7',
        'ordDtlCustShortText8',
        'ordDtlCustShortText9',
        'ordDtlCustShortText10'
    ];

    public function unidadMedida(){
        $producto = Producto::select('unId' , 'prdId')->where('prdCod', $this->ordDtlCustShortText1)->first();


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
