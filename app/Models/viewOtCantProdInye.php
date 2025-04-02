<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class viewOtCantProdInye extends Model
{
    use HasFactory;
    protected $table    ='iny_tot_bul_prd';

    protected $fillable = [
        'idIny',
        'idOrdt',
    	'idOrdtd',	
    	'ordtdPrdCod',
        'inydCaja',
    	'equiPrdBulto',
        'tot_prd_bul'

    ];
}
