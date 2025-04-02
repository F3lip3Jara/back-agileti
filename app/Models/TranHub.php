<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;

class TranHub extends Model
{
    use HasFactory;
    protected $table    ='tranbank';
    protected $fillable = [
        'idTran',
        'idOrv',
        'json',
        'token_ws',
        'empId',
        'transtip',
        'trancentra',
        'transtatus'
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
