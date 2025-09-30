<?php

namespace App\Models\Sd;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;

class SdPalletDet extends Model
{
    protected $table = 'sd_pallets_det';
    protected $primaryKey = 'palletDetId';
    public $timestamps = true;
    protected $fillable = ['palletId', 'iblpnId'];


   
    public function pallet()
    {
        return $this->belongsTo(SdPallet::class, 'palletId');
    }

    public function iblpn()
    {
        return $this->belongsTo(SdIblpns::class, 'iblpnId');
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
