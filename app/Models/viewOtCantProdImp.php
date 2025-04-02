<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class viewOtCantProdImp extends Model
{
    use HasFactory;
    protected $table    ='imp_tot_bul_prd';

    protected $fillable = [
        'idImp',
        'idOrdt',
    	'idOrdtd',	
    	'ordtdPrdCod',
        'impdCajaAcu',
    	'equiPrdBulto',
        'tot_prd_bul'
        ];
}
