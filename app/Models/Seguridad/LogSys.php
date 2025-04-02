<?php

namespace App\Models\Seguridad;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;

class LogSys extends Model
{
    use HasFactory;
    protected $table    ='segu_log_sys';
    protected $fillable = [
        'idLog',
        'empId',
        'etaId',
        'etaDesId',
        'lgId',
        'lgName',
        'lgDes',
        'lgTip',
        'lgDes1',
        'lgDes2',
        'lgDes3',
        'lgDes4',
      
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
