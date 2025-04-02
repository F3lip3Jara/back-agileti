<?php

namespace App\Models\Parametros;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;

class PrvDirDes extends Model
{
    use HasFactory;
    protected $table    ='parm_prv_suc';
    protected $fillable = [
        'empId',
        'prvId',
        'prvdId',
        'prvdDir',
        'prvdNum',
        'paiId',
        'regId',
        'comId',
        'ciuId'

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
