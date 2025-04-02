<?php

namespace App\Models\Sd;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;

class Sector extends Model
{
    use HasFactory;
    protected $table = 'sd_cent_alm_sector';
     
    protected $fillable = [
        'sectorId',
        'empId',
        'centroId',
        'almId',
        'secDes',
        'secCod',
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
