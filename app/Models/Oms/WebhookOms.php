<?php

namespace App\Models\Oms;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;

class WebhookOms extends Model
{
    use HasFactory;
    
    protected $table    ='webhook_oms';
    protected $fillable = [
        'omshId',
        'json',
        'x_wc_webhook_topic',
        'x_wc_webhook_resource',
        'x_wc_webhook_event',
        'session',
        'header',
        'web_estado'
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
