<?php

namespace App\Models\Sd;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;

class SdOrden extends Model
{
    use HasFactory;
    
    protected $table = 'sd_orden';
    
    protected $fillable = [
        'ordId',
        'empId',
        'centroId',
        'almId',
        'ordNumber',// Número de onda
        'ordQty',// Cantidad de orden
        'ordestatus', // Estado del pedido P:Pendiente L:Liberado V:Verificado A:Almacenado
        'ordTip', // Tipo Salida / Entrada
        'ordTipDes',//Tipo Salida / Entrada
        'ordClase',//CODIGO Clase 
        'ordClaseDes',//Descripcion Clase 
        'ordHdrCustShortText1',//Documento relacionado
        'ordHdrCustShortText2',//Proveedor  / Cliente
        'ordHdrCustShortText3',//Nombre Proveedor / Cliente
        'ordHdrCustShortText4',//Fecha de la orden
        'ordHdrCustShortText5',//Fecha promesa entrega
        'ordHdrCustShortText6',//Cantidad de lineas       
        'ordHdrCustShortText7',//
        'ordHdrCustShortText8',//
        'ordHdrCustShortText9',//
        'ordHdrCustShortText10',//
        'ordHdrCustShortText11',//
        'ordHdrCustShortText12',//
        'ordHdrCustShortText13',//
        'ordHdrCustLongText1'//
    ];

  /*  public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }
    
    public function lineas()
    {
        return $this->hasMany(LineaOrden::class);
    }
*/

            
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

