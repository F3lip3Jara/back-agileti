<?php

namespace App\Models\Seguridad;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;

class Roles extends Model
{
    use HasFactory;

    protected $table    ='segu_roles';
    protected $fillable = [
        'empId',
        'rolId',
        'rolDes'
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

    public function scopeFiltrarOtroValor($query, $otroValor)
    {
        if ($otroValor !== null) {
            return $query->where('otro_campo', '=', $otroValor);
        }

        return $query;
    }
}
