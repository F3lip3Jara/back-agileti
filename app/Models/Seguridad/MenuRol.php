<?php

namespace App\Models\Seguridad;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;

class MenuRol extends Model
{
    use HasFactory;
    protected $table    ='menu_roles';
    protected $fillable = [
        'molId',
        'rolId',
        'molDes',
        'molIcon',
        'optId',
        'optDes',
        'optLink',      
        'empId'
    ];

   
}
