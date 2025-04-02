<?php

namespace App\Models\Parametros;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;

class Clientes extends Model
{
    use HasFactory;

    protected $table    ='parm_clientes';
    protected $fillable = [
        'cliId',
        'empId',
        'cliemail',
        'clinombre',
        'cliapellido',
        'cliempresa',
        'clidireccion_1',
        'clidireccion_2',
        'cliciudad',
        'clicomuna',
        'clipais',
        'clitelefono',
        'cliidExt'
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
