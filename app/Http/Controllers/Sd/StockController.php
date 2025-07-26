<?php

namespace App\Http\Controllers\Sd;

use App\Http\Controllers\Controller;
use App\Jobs\LogSistema;
use App\Models\FieldDefinition;
use App\Models\Parametros\Producto;
use App\Models\Sd\SdIblpns;
use App\Models\Sd\SdStockIblpn;
use App\Models\Sd\SdStocks;
use App\Models\Sd\SdTIblns;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class StockController extends Controller
{
    public function index(Request $request)
    {
        
       $query = SdStocks::select(
        'stockId',
        'sd_stocks.centroId',
        'sd_stocks.almId',
        'sd_stocks.prdId',
        'sd_stocks.stockQty',
        'sd_stocks.stockEst',
        'sd_stocks.created_at',
        'sd_stocks.updated_at',
        'cenDes',
        'almDes',
        'cod_pareo',
        'descripcion',
        'grupo',
        'sub_grupo',
        'color',
        'moneda',
        'costo',
        'neto',
        'bruto',
        'medida',
        'minimo',
        'url',
        'talla',

       )
       ->join('sd_centro', 'sd_centro.centroId', '=', 'sd_stocks.centroId')   
       ->join('sd_centro_alm', 'sd_centro_alm.almId', '=', 'sd_stocks.almId')   
       ->join('productos', 'productos.id', '=', 'sd_stocks.prdId')   
       ->where('sd_stocks.empId', $request->empId)    
       ->orderBy('sd_stocks.created_at', 'desc')
        ->first();

        if ($query) {
            $columns = array_keys($query->toArray());            
            // Obtener definiciones de campos filtrables
            $fieldDefinitions = FieldDefinition::whereIn('field_name', $columns)
                ->where('is_filterable', 1)
                ->get();
            // Crear array de definiciones formateado
            $columnDefinitions = [];
            foreach ($fieldDefinitions as $definition) {
                $columnDefinitions[] = [
                    'campo' => $definition->field_name,
                    'label' => $definition->label,
                    'data_type' => $definition->data_type
                ];
            }
        } else {
            $columnDefinitions = [];
        }

        $filtros = $request['filter'];
        $filtros = json_decode(base64_decode($filtros));
       // return $filtros;
        if(isset($filtros)){       
            $data     =SdStocks::query()       
            ->filter($filtros)
            ->join('sd_centro', 'sd_centro.centroId', '=', 'sd_stocks.centroId')   
            ->join('sd_centro_alm', 'sd_centro_alm.almId', '=', 'sd_stocks.almId')
            ->join('productos', 'productos.id', '=', 'sd_stocks.prdId')   
            ->where('sd_stocks.empId', $request->empId)    
            ->orderBy('sd_stocks.created_at', 'desc')
            ->get();
        } else {
            $data    = SdStocks::select(
                    'stockId',
                    'sd_stocks.centroId',
                    'sd_stocks.almId',
                    'sd_stocks.prdId',
                    'sd_stocks.stockQty',
                    'sd_stocks.stockEst',
                    'sd_stocks.created_at',
                    'sd_stocks.updated_at',
                    'cenDes',
                    'almDes',
                    'cod_pareo',
                    'descripcion',
                    'grupo',
                    'sub_grupo',
                    'color',
                    'moneda',
                    'costo',
                    'neto',
                    'bruto',
                    'medida',
                    'minimo',
                    'url',
                    'talla',
                 
            )
            ->join('sd_centro', 'sd_centro.centroId', '=', 'sd_stocks.centroId')   
            ->join('sd_centro_alm', 'sd_centro_alm.almId', '=', 'sd_stocks.almId')   
            ->join('productos', 'productos.id', '=', 'sd_stocks.prdId')   
            ->where('sd_stocks.empId', $request->empId)    
            ->orderBy('sd_stocks.created_at', 'desc')
            ->take(1500)->get();
        }
       
        $resources = array(
            "data" => $data,
            "columns" => $columnDefinitions
        );
        return response()->json($resources, 200); 		
    }


    public function indexIblpn(Request $request)
    {
        $query = SdStockIblpn::select(
            'sd_stocks_iblpns.stockIblpnId',            
            'sd_stocks_iblpns.empId',
            'sd_stocks_iblpns.centroId',
            'sd_stocks_iblpns.almId',
            'sd_stocks_iblpns.iblpnId',
            'sd_stocks_iblpns.prdId',
            'sd_stocks_iblpns.stockIblpnQty',
            'sd_iblpns.iblpnStatus',
            'sd_iblpns.iblpnOriginalBarcode',
            'sd_iblpns.iblpnHdrCustShortText3',
            'sd_iblpns.iblpnHdrCustShortText4',            
            'sd_iblpns.iblpnQty',
            'cenDes',
            'almDes',
            'cod_pareo',
            'descripcion',
            'talla',
            'color',
            'grupo',
            'sub_grupo',
            'url',
            'sd_stocks_iblpns.created_at',
            'sd_stocks_iblpns.updated_at'
    
           )
           ->join('sd_centro', 'sd_centro.centroId', '=', 'sd_stocks_iblpns.centroId')   
           ->join('sd_centro_alm', 'sd_centro_alm.almId', '=', 'sd_stocks_iblpns.almId')   
           ->join('productos', 'productos.id', '=', 'sd_stocks_iblpns.prdId')   
           ->join('sd_iblpns', 'sd_iblpns.iblpnId', '=', 'sd_stocks_iblpns.iblpnId')   
           ->where('sd_stocks_iblpns.empId', $request->empId)    
           ->orderBy('sd_stocks_iblpns.created_at', 'desc')
            ->first();
    
            if ($query) {
                $columns = array_keys($query->toArray());            
                // Obtener definiciones de campos filtrables
                $fieldDefinitions = FieldDefinition::whereIn('field_name', $columns)
                    ->where('is_filterable', 1)
                    ->get();
                // Crear array de definiciones formateado
                $columnDefinitions = [];
                foreach ($fieldDefinitions as $definition) {
                    $columnDefinitions[] = [
                        'campo' => $definition->field_name,
                        'label' => $definition->label,
                        'data_type' => $definition->data_type
                    ];
                }
            } else {
                $columnDefinitions = [];
            }
    
            $filtros = $request['filter'];
            $filtros = json_decode(base64_decode($filtros));
           // return $filtros;
            if(isset($filtros)){       
                $data     =SdStockIblpn::query()       
                ->filter($filtros)
                ->join('sd_centro', 'sd_centro.centroId', '=', 'sd_stocks_iblpns.centroId')   
                ->join('sd_centro_alm', 'sd_centro_alm.almId', '=', 'sd_stocks_iblpns.almId')   
                ->join('productos', 'productos.id', '=', 'sd_stocks_iblpns.prdId')   
                ->join('sd_iblpns', 'sd_iblpns.iblpnId', '=', 'sd_stocks_iblpns.iblpnId')   
                ->where('sd_stocks_iblpns.empId', $request->empId)    
                ->orderBy('sd_stocks_iblpns.created_at', 'desc')
                ->get();
            } else {
                $data    =  SdStockIblpn::select(
                    'sd_stocks_iblpns.stockIblpnId',            
                    'sd_stocks_iblpns.empId',
                    'sd_stocks_iblpns.centroId',
                    'sd_stocks_iblpns.almId',
                    'sd_stocks_iblpns.iblpnId',
                    'sd_stocks_iblpns.prdId',
                    'sd_stocks_iblpns.stockIblpnQty',
                    'sd_iblpns.iblpnStatus',
                    'sd_iblpns.iblpnOriginalBarcode',
                    'sd_iblpns.iblpnHdrCustShortText3',
                    'sd_iblpns.iblpnHdrCustShortText4',            
                    'sd_iblpns.iblpnQty',
                    'cenDes',
                    'almDes',
                    'cod_pareo',
                    'descripcion',
                    'talla',
                    'color',
                    'grupo',
                    'sub_grupo',
                    'url',
                    'sd_stocks_iblpns.created_at',
                    'sd_stocks_iblpns.updated_at'
            
                   )
                   ->join('sd_centro', 'sd_centro.centroId', '=', 'sd_stocks_iblpns.centroId')   
                   ->join('sd_centro_alm', 'sd_centro_alm.almId', '=', 'sd_stocks_iblpns.almId')   
                   ->join('productos', 'productos.id', '=', 'sd_stocks_iblpns.prdId')   
                   ->join('sd_iblpns', 'sd_iblpns.iblpnId', '=', 'sd_stocks_iblpns.iblpnId')   
                   ->where('sd_stocks_iblpns.empId', $request->empId)    
                   ->orderBy('sd_stocks_iblpns.created_at', 'desc')
                     ->take(1500)->get();
            }
           
            $resources = array(
                "data" => $data,
                "columns" => $columnDefinitions
            );
            return response()->json($resources, 200); 
    }

    
   
    public function ins(Request $request)
    {      
        
    }

    public function del(Request $request)
    {
        /* $id     = 0;
        $header = $request->header('access-token');
        $val    = User::select('token' , 'id', 'activado')->where('token' , $header)->get();
        $xid    = $request->idPai;



        foreach($val as $item){
            if($item->activado = 'A'){
                $id = $item->id;
            }
        }
        if($id >0){
            $valida = Region::all()->where('idPai' , $xid)->take(1);
            //si la variable es null o vacia elimino el rol
            if(sizeof($valida) > 0 ){
                  //en el caso que no se ecuentra vacia no puedo eliminar
                  $resources = array(
                    array("error" => "1", 'mensaje' => "El País  no se puede eliminar , asociado a Región",
                    'type'=> 'danger')
                    );
                   return response()->json($resources, 200);
            }else{

               $valida = Proveedor::all()->where('idPai', $xid)->take(1);

               if(sizeof($valida) > 0 ){

                $resources = array(
                    array("error" => "1", 'mensaje' => "El País  no se puede eliminar , asociado a Proveedor",
                    'type'=> 'danger')
                    );
                    return response()->json($resources, 200);
               }else{

                $valida = PrvDirDes::all()->where('idPai', $xid)->take(1);

                if(sizeof($valida) > 0 ){
                    //en el caso que no se ecuentra vacia no puedo eliminar
                   $resources = array(
                      array("error" => "1", 'mensaje' => "La Comuna no se puede eliminar , asociado a Dirección",
                      'type'=> 'danger')
                      );
                     return response()->json($resources, 200);
                }else{
                    $affected = Pais:: where('idPai', $xid)->delete();

                    if($affected > 0){
                        $resources = array(
                            array("error" => '0', 'mensaje' => "País Eliminado Correctamente" ,'type'=> 'warning')
                            );
                        return response()->json($resources, 200);
                    }else{
                        $resources = array(
                        array("error" => "2", 'mensaje' => "No se encuentra registro" ,'type'=> 'warning')
                        );
                        return response()->json($resources, 200);
                    }
                }

               }



            }

        }else{
                return response()->json('error' , 203);
        }*/
    }
   
}
