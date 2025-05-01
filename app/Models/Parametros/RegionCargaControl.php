<?php

namespace App\Models\Parametros;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;

class RegionCargaControl extends Model
{
    use HasFactory;

    protected $table = 'parm_region_carga_control';
    protected $primaryKey = 'regCargaId';


    protected $fillable = [
        'empId',
        'paiId',
        'regCargaEst',
        'regCargaTotal',
        'regCargaProgreso',
        'regCargaError'
    ];

    public function pais()
    {
        return $this->belongsTo(Pais::class, 'paiId', 'paiId');
    }

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