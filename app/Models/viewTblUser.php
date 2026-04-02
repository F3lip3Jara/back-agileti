<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;

class viewTblUser extends Model
{
    use HasFactory;
    protected $table    ='tblusuarios';

    protected $fillable = [
        'id',
        'empId',
        'empresa',
        'rolId',
        'name',
        'email',
        'rolDes',
        'emploNom',
        'emploApe',
        'emploFecNac',
        'gerDes',
        'activado',
        'reinicio',
        'created_at',
        'gerId'
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

    public function scopeFilter($query, $filter, $sorting) 
    {
        foreach ($filter as $item) {
            $column = $item->column;
            $operator = $item->operator;
    
            if ($column == "created_at" && count($item->values) == 2) {
                $fecha_inicio = Carbon::parse($item->values[0]);
                $fecha_fin = Carbon::parse($item->values[1]);
                $query->whereBetween('created_at', [$fecha_inicio, $fecha_fin]);
            } else if (!empty($item->values) && $column != "") {
                $query->where(function ($q) use ($item, $operator, $column) {
                    foreach ($item->values as $i => $value) {
                        if ($operator === 'like') {
                            $i === 0
                                ? $q->where($column, 'like', '%' . trim($value) . '%')
                                : $q->orWhere($column, 'like', '%' . trim($value) . '%');
                        } else {
                            $i === 0
                                ? $q->where($column, $operator, $value)
                                : $q->orWhere($column, $operator, $value);
                        }
                    }
                });
            }
        }
    
        // Sorting
        if (count($sorting) > 0) {
            foreach ($sorting as $item) {
                $query->orderBy($item->column, $item->direction);
            }
        }
    }

}
