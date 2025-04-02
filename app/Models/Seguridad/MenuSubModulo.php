<?php

namespace App\Models\Seguridad;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MenuSubModulo extends Model
{
    use HasFactory;

    protected $table    ='menu_roles_sub';
    protected $fillable = [
        'molId',
        'rolId',
        'molsDes',
        'molIcon',
        'optId',
        'optDes',
        'optLink',
        'empId',
        'molsId'
    ];

    
}
