<?php

namespace App\Models\Produccion;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;

class OrdenProduccion extends Model
{
    use HasFactory;

    protected $table    ='prod_orden';
    protected $fillable = [
        'orpId',
        'empId',
        'prvId',    
        'orpNumOc',
        'orpNumRea',
        'orpFech',
        'orpUsrG',
        'orpObs',
        'orpTurns',
        'orpEst', // 1: Pendiente, 2: Procesando, 3: Aprobada, 4: Rechazada
        'orpEstPrc', // 1: Pendiente, 2: Procesando, 3: Aprobada, 4: Rechazada
        'orpHdrCustShortText1', // 255, //
        'orpHdrCustShortText2',// 100, // Clase documento descripcion
        'orpHdrCustShortText3',// 100, // 
        'orpHdrCustShortText4', //100, // 
        'orpHdrCustShortText5', // 100, // 
        'orpHdrCustShortText6', // 100, // 
        'orpHdrCustShortText7', //100, // 
        'orpHdrCustShortText8', //100, // 
        'orpHdrCustShortText9', //100, // 
        'orpHdrCustShortText10', //20, // Clase documento
        'orpHdrCustShortText11', //20, // 
        'orpHdrCustShortText12', //20, // 
        'orpHdrCustShortText13', //20, // 
        'orpHdrCustLongText1' // 
     
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
}
