<?php

namespace App\Models\Parametros;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;

class MovRechazo extends Model
{
    use HasFactory;

    protected $table    ='parm_mot_rechazo';
    protected $fillable = [
        'motId',
        'empId',
        'motDes',
        'etaId'
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

    public function scopeBuscarpor($query, $tipo, $buscar) {     	
        return  $query->whereIn($tipo,$buscar);
    }

}
