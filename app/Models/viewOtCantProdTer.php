<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;

class viewOtCantProdTer extends Model
{
     use HasFactory;
    protected $table    ='ter_tot_bul_prd';

    protected $fillable = [
        'idTer',
        'idOrdt',
    	'idOrdtd',	
    	'ordtdPrdCod',
        'terdCaja',
    	'equiPrdBulto',
        'tot_prd_bul'

    ];
}
