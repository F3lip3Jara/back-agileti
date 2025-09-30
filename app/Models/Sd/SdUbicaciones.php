<?php
namespace App\Models\Sd;

use App\Models\Sd\Centro;
use App\Models\Sd\Almacen;
use App\Models\Sd\Sector;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Support\Facades\Config;
class SdUbicaciones extends Model
{
    protected $table = 'sd_centro_alm_sec_ubi';
    protected $primaryKey = 'ubicacionId';
    public $timestamps = true;
    protected $fillable = ['empId', 'centroId', 'almId', 'sectorId', 'ubiDes', 'ubiCod', 'ubiAlto', 'ubiAncho', 'ubiLargo', 'ubiVol', 'ubiAct'];  
      
    public function centro()
    {
        return $this->belongsTo(Centro::class, 'centroId');
    }
    public function almacen()
    {
        return $this->belongsTo(Almacen::class, 'almId');
    }
    public function sector()
    {
        return $this->belongsTo(Sector::class, 'sectorId');
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
