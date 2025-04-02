<?php

namespace App\Models\Parametros;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;

class Comuna extends Model
{
    use HasFactory;
    protected $table    ='parm_comuna';
    protected $fillable = [
        'paiId',
        'empId',
        'regId',
        'ciuId',
        'comId',
        'comDes',
        'comCod'
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
