<?php

namespace App\Models\Produccion;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;

class OrdProDet extends Model
{
    use HasFactory;

    protected $table    ='prod_orden_det';
    protected $fillable = [
            'orpdId',
            'orpId',
            'empId',
            'orpdPrdCod',
            'orpdPrdDes',
            'orpdCant',
            'orpdDtlCustShortText1',
            'orpdDtlCustShortText2',  // 
            'orpdDtlCustShortText3',  // 
            'orpdDtlCustShortText4',  // 
            'orpdDtlrCustShortText5', // 
            'orpdDtlCustShortText6',  // 
            'orpdDtlCustShortText7',  // 
            'orpdDtlCustShortText8',  //  
            'orpdDtlCustShortText9',  // 
            'orpdDtlCustShortText10' // 
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
