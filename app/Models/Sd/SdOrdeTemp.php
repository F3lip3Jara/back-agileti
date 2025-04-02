<?php

namespace App\Models\Sd;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;

class SdOrdeTemp extends Model
{
    use HasFactory;
    
    protected $table = 'sd_ordentemp';
    
    protected $fillable = [
         'ordtId',
         'empId',
         'centroId',
         'almId',
         'ordtCustShortText1', //orden
         'ordtCustShortText2',
         'ordtTip',
         'ordtest',
         'updated_at',
         'created_at'
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