<?php

namespace App\Http\Controllers\Sd;
use App\Http\Controllers\Controller;
use App\Jobs\LogSistema;
use App\Jobs\SdCierreTraslado;
use App\Jobs\SdOrdenJobTemp;
use App\Jobs\StockMov;
use App\Models\Sd\SdOrden;
use App\Models\Sd\SdOrdenDet;
use App\Models\Sd\SdOrdeTemp;
use App\Models\Sd\SdTIblns;
use App\Models\Sd\SdTraslado;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class SdOrdController extends Controller
{
    public function index(Request $request){

        $query = SdOrden::select('*')
        ->join('sd_centro', 'sd_centro.centroId', '=', 'sd_orden.centroId')   
        ->join('sd_centro_alm', 'sd_centro_alm.almId', '=', 'sd_orden.almId')   
        ->where('sd_orden.empId', $request->empId)    
        ->orderBy('sd_orden.created_at', 'desc')
        ->first();

        $columns = $query ? array_keys($query->toArray()) : [];
        $columns = array_filter($columns, function ($column) {
            return $column !== 'empId'; // Columna a excluir
        });

        $columns = array_values($columns); // Reindexar el array si es necesa
        $filtros = $request['filter'];
        $filtros = json_decode(base64_decode($filtros));
        
       if(isset($filtros)){       
            $data     = SdOrden::query()       
                    ->filter($filtros)
                    ->join('sd_centro', 'sd_centro.centroId', '=', 'sd_orden.centroId')   
                    ->join('sd_centro_alm', 'sd_centro_alm.almId', '=', 'sd_orden.almId')   
                    ->where('sd_orden.empId', $request->empId)    
                    ->orderBy('sd_orden.created_at', 'desc')
                    ->get();
       }else{
            $data    = SdOrden::select('*')
                    ->join('sd_centro', 'sd_centro.centroId', '=', 'sd_orden.centroId')   
                    ->join('sd_centro_alm', 'sd_centro_alm.almId', '=', 'sd_orden.almId')   
                    ->where('sd_orden.empId', $request->empId)    
                    ->orderBy('sd_orden.created_at', 'desc')
                    ->take(1500)->get();
       }
       
        $resources = array(
                "data"   => $data,
                "colums" => $columns
        );
	  return response()->json($resources, 200); 	
	
    }

    public function ins(Request $request){
        return $request->all();
        $name     = $request['name'];
        $empId    = $request['empId'];
        $id       = $request['id'];
        $centroId = $request['pedido']['centro_id'];
        $almId    = $request['pedido']['almacen_id'];
        $tipo     = $request['pedido']['tipo'];
        
        $data     = json_encode($request->all());
        
        $valida = SdOrdeTemp::all()
                        ->where('empId', $empId)
                        ->where('centroId', $centroId)
                        ->where('almId', $almId)
                        ->where('ordtTip', $tipo)
                        ->where('ordtCustShortText2', $id)
                        ->take(1);

        //si la variable es null o vacia elimino el rol
        if (sizeof($valida) > 0) {
            //en el caso que no se ecuentra vacia no puedo eliminar
            $resources = array(
                array(
                    "error" => "1", 'mensaje' => "La orden ya se encuentra en proceso",
                    'type' => 'info'
                )
            );
            return response()->json($resources, 200);
        } else {
             $affected = SdOrdeTemp::create([
                 'empId'    => $empId,
                 'centroId' => $centroId,
                 'almId'    => $almId,
                 'ordtCustShortText1'=>$data ,
                 'ordtCustShortText2' => $id,
                 'ordtTip' => $tipo,
                 'ordtest'  => 'N', 
             ]);

             if (isset($affected)) {
                    $job = new LogSistema( $request->log['0']['optId'] , $request->log['0']['accId'] , $name , $empId , $request->log['0']['accDes'], $request->log['0']['accTip']);
                     dispatch($job); 
                     $job = new SdOrdenJobTemp($empId);         
                     dispatch($job);             
                     $resources = array(
                     array("error" => '0', 'mensaje' => $request->log['0']['accMessage'], 'type' => $request->log['0']['accType'])
                     );
                     return response()->json($resources, 200);
             } else {
                 return response()->json('error', 204);
             }
        }
    }

    function ver(Request $request){
        $ordId = $request['ordId'];
        $data = SdOrdenDet::where('ordId', $ordId)->get();
        return response()->json($data, 200);
    }

    public function insOrdTrasInt(Request $request){
     
        $data     = $request->all();
        $empId    = $data['empId'];
        $name     = $data['name'];
        $idUser   = $data['idUser'];
        $prd      = json_encode($data['0']['prd']);
        $centroId = $data['0']['centroId'];
        $almId    = $data['0']['almId'];        
      
        $affected = SdTIblns::all()
                            ->where('empId', $empId )
                            ->where('centroId', $centroId)
                            ->where('almId' , $almId)
                            ->where('stockTblpnJson' , $prd);
        if(sizeof($affected) > 0){
            $resources = array(
                array("error" => '0', 'mensaje' => 'Error la orden ya tiene traslado iniciado' , 'type' => 'info')
            );
            return response()->json($resources, 200);
        }else{
                $affected=SdTIblns::create([
                    'empId'         => $empId,
                    'centroId'      => $centroId,
                    'almId'         => $almId,   
                    'stockTblpnJson'=> $prd
                ]);

        
            
                if (isset($affected)) {
                    $job = new LogSistema( $request->log['0']['optId'] , $request->log['0']['accId'] , $name , $empId , $request->log['0']['accDes'], $request->log['0']['accTip']);
                    dispatch($job);
                    $job = new StockMov($empId, $idUser, $name, $centroId, $almId);
                    dispatch($job);         
                    $resources = array(
                        array("error" => '0', 'mensaje' => $request->log['0']['accMessage'], 'type' => $request->log['0']['accType'])
                    );
                    return response()->json($resources, 200);
                } else {
                    return response()->json('error', 204);
                }
        }
    }

    public function pdfOrden(Request $request){
        $ordId = $request['ordId'];
        $data = SdTraslado::select('prdCod', 'iblpnOriginalBarcode' , 'iblpnQty' , 'trasSecDesDes' , 'iblpnHdrCustShortText6')
                    ->join('sd_iblpns', 'sd_iblpns.iblpnId', '=', 'sd_traslado.iblpnId')   
                    ->join('parm_producto', 'sd_iblpns.prdId', '=', 'parm_producto.prdId')   
                    -> where('trasHdrCustShortText1', $ordId)
                    ->get();
        return response()->json($data, 200);
    }

    public function ordenPda(Request $request){
        $ordNumber = $request['ordNumber'];
        $data = SdOrden::select('ordestatus')->where('ordNumber', $ordNumber)->get();  
        
        if(sizeof($data) > 0){
            if ($data['0']['ordestatus'] == 'V') {
                $data = SdTraslado::select('prdCod', 'iblpnOriginalBarcode' , 'iblpnQty' , 'trasSecDesDes' , 'iblpnHdrCustShortText6')
                            ->join('sd_iblpns', 'sd_iblpns.iblpnId', '=', 'sd_traslado.iblpnId')   
                            ->join('parm_producto', 'sd_iblpns.prdId', '=', 'parm_producto.prdId')   
                            ->where('trassecCod', $ordNumber)
                            ->get();
                return response()->json($data, 200);
            }else{
                $resources = array(
                    array("error" => '0', 'mensaje' => 'Error la orden no está en estado verificado' , 'type' => 'info')
                );
                return response()->json($resources, 200);
            }
        }else{
            $resources = array(
                array("error" => '0', 'mensaje' => 'Error la orden no existe' , 'type' => 'info')
            );
            return response()->json($resources, 200);
        }
       
    }

    public function ordenChEstA(Request $request){
       $data = $request->all();
       $empId    = $data['empId'];
       $name     = $data['name'];
       $idUser   = $data['idUser'];
       
       
       foreach($data as $item){
         $ordNumber =  $item['ordenNumero'];
         $iblpns    =  $item['iblpns'];
        
            $affected = SdOrden::where('ordNumber', $ordNumber)->update(
                [
                    'ordestatus' => 'A'
                ]
            );

            if ($affected > 0) {
                $job = new LogSistema( $request->log['0']['optId'] , $request->log['0']['accId'] , $name , $empId , $request->log['0']['accDes'], $request->log['0']['accTip']);
                dispatch($job);  
                $job = new SdCierreTraslado($empId, $iblpns);
                dispatch($job);      
                $resources = array(
                    array("error" => '0', 'mensaje' => $request->log['0']['accMessage'], 'type' => $request->log['0']['accType'])
                );
                return response()->json($resources, 200);
            } else {
                return response()->json('error', 204);
            }    
       }

     //   $data = SdOrden::select('ordestatus')->where('ordNumber', $ordNumber)->get();   

    }
}
