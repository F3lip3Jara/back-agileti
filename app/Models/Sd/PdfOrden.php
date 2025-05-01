<?php

namespace App\Models\Sd;

use Illuminate\Database\Eloquent\Model;

class PdfOrden extends Model
{
    protected $table = 'sd_pdf_generar_orden';
    protected $fillable = ['orden_ids', 'status', 'file_path']; 

   
    
}
