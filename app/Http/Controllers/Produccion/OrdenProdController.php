<?php

namespace App\Http\Controllers\Produccion;

use App\Http\Controllers\Controller;
use App\Jobs\LogSistema;
use App\Models\Produccion\OrdenProduccion;
use App\Models\Produccion\OrdProDet;
use App\Models\Parametros\Producto;
use App\Models\Seguridad\Empresa;
use App\Models\User;
use App\Models\viewOrdenProduccion;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class OrdenProdController extends Controller
{
    public function index(Request $request)
    {
        $table   = 'orden_produccion';
        $columns = Schema::getColumnListing($table);
        $columns = array_filter($columns, function ($column) {
            return $column !== 'empId'; // Columna a excluir
        });

        $columns = array_values($columns); // Reindexar el array si es necesario
        $filtros = $request['filter'];
        $filtros = json_decode(base64_decode($filtros));
        
       if(isset($filtros)){       
        $data     = viewOrdenProduccion::query()->filter($filtros)
                    ->where('empId', $request->empId)
                    ->get();
       }else{
         $data    = viewOrdenProduccion::select('*')->where('empId', $request->empId)->take(1500)->get();
       }
       
        $resources = array(
                "data"   => $data,
                "colums" => $columns
        );
 	
	  return response()->json($resources, 200); 
    }

    public function update(Request $request)
    {
        $data   = $request->all();       
        $empId  = $data['empId'];
        $name   = $data['name'];
        $idUser = $data['idUser'];  
        $fecha          = Carbon::parse($data['fechaCreacion'])->format('Y-m-d');       
        $ordenes        = $data['productos'];     
        $prvId          = $data['proveedor'];
        $centroId       = $data['centro'];
        $almId          = $data['almacen'];
        $clasTipId      = $data['tipoDocumento'];
        $fechaPromesa   = Carbon::parse($data['fechaPromesaEntrega'])->format('Y-m-d');
        $documentoRelacionado = $data['documentoRelacionado'];
        $latitudEnvio   = $data['latitudEnvio'];
        $longitudEnvio  = $data['longitudEnvio'];
        $notas          = $data['notas'];


        $affected = OrdenProduccion::where('orpId', $data['orpId'])->update([
            'prvId'     => $prvId,
            'orpNumOc'  => $documentoRelacionado,
            'orpNumRea' => $documentoRelacionado,
            'orpFech'   => $fecha,
            'orpUsrG'   => $name,
            'orpObs'    => $notas,
            'orpTurns'  => '',
            'orpEst'    => 1,
            'orpEstPrc' => 1,
            'orpHdrCustShortText1' => str_replace(',', '.', $centroId), // 255, //
            'orpHdrCustShortText2' => str_replace(',', '.', $almId),// 100, // Clase documento descripcion
            'orpHdrCustShortText3' => $clasTipId['clasTipId'],// 100, // 
            'orpHdrCustShortText4' => $clasTipId['clasTipDes'], //100, // Fecha promesa entrega
            'orpHdrCustShortText5' => $latitudEnvio, // 100, //  Latitud
            'orpHdrCustShortText6' => $longitudEnvio, // 100, //  Longitud
            'orpHdrCustShortText7' => str_replace(',', '.', $data['total']), //100, //  Total
            'orpHdrCustShortText8' => str_replace(',', '.', $data['totalProducto']), //100, //  Total productos
            'orpHdrCustShortText9' => $fechaPromesa,//100, // 
            'orpHdrCustShortText10' => '', //20, // Clase documento
            'orpHdrCustShortText11' => '', //20, // 
            'orpHdrCustShortText12' => '', //20, // 
            'orpHdrCustShortText13' => '', //20, // 
            'orpHdrCustLongText1' => '' // 
        ]);

        $xid = $data['orpId'];

        if($affected){
            $affected = OrdProDet::where('orpId', $xid)->delete(); 
            foreach ($ordenes as $orddet) {
                OrdProDet::create([                   
                    'orpId'      => $xid,
                    'empId'      => $empId,
                    'orpdPrdCod' => $orddet['cod_pareo'],
                    'orpdPrdDes' => $orddet['descripcion'], 
                    'orpdCant'   => $orddet['cantidad'],
                    'orpdDtlCustShortText1'  => '',
                    'orpdDtlCustShortText2'  => '',  // 
                    'orpdDtlCustShortText3'  => '',  // 
                    'orpdDtlCustShortText4'  => '',  // 
                    'orpdDtlrCustShortText5' => '', //   
                ]);
            }

            $job = new LogSistema( $request->log['0']['optId'] , $request->log['0']['accId'] , $name , $empId , $request->log['0']['accDes'] , $request->log['0']['accTip']);
            dispatch($job);            
            $resources = array( 
                array("error" => '0', 'mensaje' => $request->log['0']['accMessage'], 'type' => $request->log['0']['accType'])
            );
            return response()->json($resources, 200);
        }else{
            return response()->json('error', 204);
        }
        
    }

    public function ins(Request $request)
    {
        $data   = $request->all();
        $empId  = $data['empId'];
        $name   = $data['name'];
        $idUser = $data['idUser'];  
        $fecha          = Carbon::parse($data['fechaCreacion'])->format('Y-m-d');       
        $ordenes        = $data['productos'];     
        $prvId          = $data['proveedor'];
        $centroId       = $data['centro'];
        $almId          = $data['almacen'];
        $clasTipId      = $data['tipoDocumento'];
        $fechaPromesa   = Carbon::parse($data['fechaPromesaEntrega'])->format('Y-m-d');
        $documentoRelacionado = $data['documentoRelacionado'];
        $latitudEnvio   = $data['latitudEnvio'];
        $longitudEnvio  = $data['longitudEnvio'];
        $notas          = $data['notas'];

     

        $affected       = OrdenProduccion::create([
              
            'empId'     => $empId,
            'prvId'     => $prvId,
            'orpNumOc'  => $documentoRelacionado,
            'orpNumRea' => $documentoRelacionado,
            'orpFech'   => $fecha,
            'orpUsrG'   => $name,
            'orpObs'    => $notas,
            'orpTurns'  => '',
            'orpEst'    => 1,
            'orpEstPrc' => 1,
            'orpHdrCustShortText1' => str_replace(',', '.', $centroId), // 255, //
            'orpHdrCustShortText2' => str_replace(',', '.', $almId),// 100, // Clase documento descripcion
            'orpHdrCustShortText3' => $clasTipId['clasTipId'],// 100, // 
            'orpHdrCustShortText4' => $clasTipId['clasTipDes'], //100, // Fecha promesa entrega
            'orpHdrCustShortText5' => $latitudEnvio, // 100, //  Latitud
            'orpHdrCustShortText6' => $longitudEnvio, // 100, //  Longitud
            'orpHdrCustShortText7' => str_replace(',', '.', $data['total']), //100, //  Total
            'orpHdrCustShortText8' => str_replace(',', '.', $data['totalProducto']), //100, //  Total productos
            'orpHdrCustShortText9' => $fechaPromesa,//100, // 
            'orpHdrCustShortText10' => '', //20, // Clase documento
            'orpHdrCustShortText11' => '', //20, // 
            'orpHdrCustShortText12' => '', //20, // 
            'orpHdrCustShortText13' => '', //20, // 
            'orpHdrCustLongText1' => '' // 

        ]);

        if($affected){
            $xid = $affected['id'];
            foreach ($ordenes as $orddet) {
                OrdProDet::create([                   
                    'orpId'      => $xid,
                    'empId'      => $empId,
                    'orpdPrdCod' => $orddet['cod_pareo'],
                    'orpdPrdDes' => $orddet['descripcion'],
                    'orpdCant'   => $orddet['cantidad'],
                    'orpdDtlCustShortText1'  => '',
                    'orpdDtlCustShortText2'  => '',  // 
                    'orpdDtlCustShortText3'  => '',  // 
                    'orpdDtlCustShortText4'  => '',  // 
                    'orpdDtlrCustShortText5' => '', //   
                ]);
            }

            
            $job = new LogSistema( $request->log['0']['optId'] , $request->log['0']['accId'] , $name , $empId , $request->log['0']['accDes'] , $request->log['0']['accTip']);
            dispatch($job);            
            $resources = array(
                array("error" => '0', 'mensaje' => $request->log['0']['accMessage'], 'type' => $request->log['0']['accType'])
            );
            return response()->json($resources, 200);
        }else{
            return response()->json('error', 204);
        }       
    /*    foreach ($data as $item) {
          
            

           
            $xid = $affected['id'];

            foreach ($ordenes as $orddet) {
                OrdProDet::create([                   
                    'orpId' => $xid,
                    'empId' => $empId,
                    'orpdPrdCod' => $orddet['prdCod'],
                    'orpdPrdDes' => $orddet['prdDes'],
                    'orpdCant' => $orddet['orpdCant'],
                    'orpdDtlCustShortText1'  => '',
                    'orpdDtlCustShortText2'  => '',  // 
                    'orpdDtlCustShortText3'  => '',  // 
                    'orpdDtlCustShortText4'  => '',  // 
                    'orpdDtlrCustShortText5' => '', // 
                    'orpdDtlCustShortText6' => '',  // 
                    'orpdDtlCustShortText7' => '',  // 
                    'orpdDtlCustShortText8' => '',  //  
                    'orpdDtlCustShortText9' => '',  // 
                    'orpdDtlCustShortText10' => '' // 
                ]);
            }
           
            if (isset($affected)) {
                $job = new LogSistema( $request->log['0']['optId'] , $request->log['0']['accId'] , $name , $empId , $request->log['0']['accDes'] , $request->log['0']['accTip']);
                dispatch($job);            
                $resources = array(
                    array("error" => '0', 'mensaje' => $request->log['0']['accMessage'], 'type' => $request->log['0']['accType'])
                );
                return response()->json($resources, 200);
            } else {
                return response()->json('error', 204);
            }
        }
    */}

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
    public function filopNumRea(Request $request)
    {

        $data   = request()->all();
        $resources = viewOrdenProduccion::select('*')->where('orden_produccion', $data['orpNumRea'])->get();
        if (isset($resources)) {
            return response()->json($resources, 200);
        } else {
            $resources = array(
                array(
                    "error" => "0", 'mensaje' => "No se encuentra coincidencia",
                    'type' => 'success'
                )
            );
            return response()->json($resources, 200);
        }
    }

    public function valCodNumRea(Request $request)
    {
        $header = $request->header('access-token');
        $val    = User::select('token', 'id', 'activado')->where('token', $header)->get();

        foreach ($val as $item) {
            if ($item->activado = 'A') {
                $id = $item->id;
            }
        }

        $data   = request()->all();
        $orpNumRea = $data['orpNumRea'];
        $val    = OrdenProduccion::select('orpNumRea')->where('orpNumRea', $orpNumRea)->get();
        $count  = 0;
        foreach ($val as $item) {
            $count = $count + 1;
        }

        return response()->json($count, 200);
    }

    public function opNumRea(Request $request)
    {

        return  OrdenProduccion::select('orpNumRea')->get();
    }


    public function OrdPDetDta(Request $request)
    {
                $data = $request->all();

                return  OrdProDet::select('*')->where('idOrp', $data['idOrp'])->whereNotExists(function ($query) {
                    $query->select(DB::raw(1))
                        ->from('ordenes_de_trabajos_det')
                        ->whereRaw('ord_produccion_det.orpId     = ordenes_de_trabajos_det.orden_produccion')
                        ->whereRaw('ord_produccion_det.orpdPrdCod = ordenes_de_trabajos_det.ordtdPrdCod');
                })->get();
          
    }


    public function valPrdOrd(Request $request)
    {
        $data = $request->all();
         $affected = Producto::all()->where('prdCod', $data['prdCod'])->take(1);
         if (sizeof($affected) > 0) {
             $error = 3;
             return response()->json($error, 200);
         } else {
             $error = 2;
             return response()->json($error, 200);
         }        
    }

    public function OrdPDet(Request $request)
    {        
        $data = $request->all();
        return  OrdProDet::select('*')->where('orpId', $data['orpId'])->get();
        
    }

    public function empresafilPdf(Request $request)
    {
        $data = $request->all();
        return  Empresa::select('empDes' , 'empDir' , 'empRut' , 'empGiro' , 'empFono' , 'empImg')->where('empId', $data['empId'])->get();
    }
}
