<?php

namespace App\Http\Controllers\Sd;

use App\Http\Controllers\Controller;
use App\Jobs\LogSistema;
use App\Models\Parametros\Producto;
use App\Models\Sd\SdIblpns;
use App\Models\Sd\SdStocks;
use App\Models\Sd\SdTIblns;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class StockController extends Controller
{
    public function index(Request $request)
    {
        $table   = 'sd_stocks';
        $columns = Schema::getColumnListing($table);
        $columns = array_filter($columns, function ($column) {
            return $column !== 'empId'; // Columna a excluir
        });

        $columns = array_values($columns); // Reindexar el array si es necesa
        $filtros = $request['filter'];
        $filtros = json_decode(base64_decode($filtros));
        
       if(isset($filtros)){       
        $data     =SdStocks::query()       
                    ->filter($filtros)
                    ->join('sd_centro', 'sd_centro.centroId', '=', 'sd_stocks.centroId')   
                    ->join('sd_centro_alm', 'sd_centro_alm.almId', '=', 'sd_stocks.almId')
                    ->join('parm_producto', 'parm_producto.prdId', '=', 'sd_stocks.prdId')   
                    ->where('sd_stocks.empId', $request->empId)    
                    ->orderBy('sd_stocks.created_at', 'desc')
                    ->get();
       }else{
         $data    = SdStocks::select('*')
                    ->join('sd_centro', 'sd_centro.centroId', '=', 'sd_stocks.centroId')   
                    ->join('sd_centro_alm', 'sd_centro_alm.almId', '=', 'sd_stocks.almId')   
                    ->join('parm_producto', 'parm_producto.prdId', '=', 'sd_stocks.prdId')   
                    ->where('sd_stocks.empId', $request->empId)    
                    ->orderBy('sd_stocks.created_at', 'desc')
                    ->take(1500)->get();
       }
       
        $resources = array(
                "data"   => $data,
                "colums" => $columns
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
