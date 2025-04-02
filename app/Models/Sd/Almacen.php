<?php

namespace App\Models\Sd;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Support\Facades\Config;

class Almacen extends Model
{
    use HasFactory;


    protected $table    ='sd_centro_alm';
    protected $fillable = [
        'empId',
        'centroId',
        'almId',
        'almDes',
        'almTip',
        'almCap',
      

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

    public function centro()
    {
        return $this->belongsTo(Centro::class, 'centroId');
    }

}
