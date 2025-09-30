<?php

namespace App\Http\Controllers\sd;

use App\Http\Controllers\Controller;
use App\Jobs\LogSistema;
use Illuminate\Http\Request;
use App\Models\Sd\SdUbicaciones;
use App\Models\Sd\Sector;

class UbicacionesController extends Controller

    {
        public function index(Request $request)
        {
            $empId       = $request['empId'];       
            $parametros  = $request['filter'];
            $parametros  = json_decode(base64_decode($parametros));
            $sectorId    = $parametros[0]->values[0];    

            if($sectorId == 0){
            return SdUbicaciones::select('*')
            ->where('empId', $empId)
            ->get();
            }else{
                return SdUbicaciones::select('*')
                ->where('empId', $empId)
                ->where('sectorId', $sectorId)
                ->get();
            }
        }
    
        public function update(Request $request)
        {   
            $name        = $request['name'];
            $empId       = $request['empId'];
       
    
         
            $affected = SdUbicaciones::where('ubicacionId', $request->ubicacionId)
            ->where('empId', $empId)
            ->update(
                [
                    'ubiDes'             => $request->ubiDes,
                    'ubiCod'             => $request->ubiCod,           
                    'ubiAlto'            => $request->ubiAlto,         
                    'ubiAncho'           => $request->ubiAncho,
                    'ubiLargo'           => $request->ubiLargo,
                    'ubiVol'             => $request->ubiVol,
                    'ubiAct'             => $request->ubiAct
                ]
            );
    
            if ($affected > 0) {
                $job = new LogSistema( $request->log['0']['optId'] , $request->log['0']['accId'] , $name , $empId , $request->log['0']['accDes'], $request->log['0']['accTip']);
                dispatch($job);            
                $resources = array(
                   array("error" => '0', 'mensaje' => $request->log['0']['accMessage'], 'type' => $request->log['0']['accType'])
                );
                return response()->json($resources, 200);
            } else {
                return response()->json('error', 204);
            }
        }
    
        public function ins(Request $request)
        {
            $name        = $request['name'];
            $empId       = $request['empId'];
            $datos       = $request['datos'];
            $ubicaciones = $datos['ubicaciones'];
            $centroId    = $datos['centroId'];
            $almacenId   = $datos['almacenId'];

          
        
            foreach ($ubicaciones as $ubicacion) {
                //return $ubicacion['sector'];
                $sector=  Sector::sectorIdCodigo($ubicacion['sector']);           
                $affected = SdUbicaciones::updateOrCreate(                
                    [
                        'empId'          => $empId,
                        'centroId'       => $centroId,
                        'almId'          => $almacenId,
                        'sectorId'       => $sector->sectorId,        
                        'ubiCod'         => $ubicacion['ubicacion']
                    ],
                    [
                        'ubiDes'         => $ubicacion['sector'].' - '.$ubicacion['ubicacion'],
                        'ubiAlto'        => $ubicacion['alto'],
                        'ubiAncho'       => $ubicacion['ancho'], 
                        'ubiLargo'       => $ubicacion['largo'],
                        'ubiVol'         => $ubicacion['volumen'],
                        'ubiAct'         => $ubicacion['activo']
                    ]
                );
            }
          
            
    
            if (isset($affected)) {
                $job = new LogSistema( $request->log['0']['optId'] , $request->log['0']['accId'] , $name , $empId , $request->log['0']['accDes'], $request->log['0']['accTip']);
                dispatch($job);            
                $resources = array(
                   array("error" => '0', 'mensaje' => $request->log['0']['accMessage'], 'type' => $request->log['0']['accType'])
                );
                return response()->json($resources, 200);
            } else {
                return response()->json('error', 204);
            }
        }
    
        public function del(Request $request)
        {
           /* $name        = $request['name'];
            $empId       = $request['empId'];
    
    
            $xid    = $request->colId;
            $valida = Producto::all()->where('colId', $xid)->take(1);
            //si la variable es null o vacia elimino el rol
            if (sizeof($valida) > 0) {
                //en el caso que no se ecuentra vacia no puedo eliminar
                $resources = array(
                    array(
                        "error" => "1", 'mensaje' => "El Color  no se puede eliminar",
                        'type' => 'danger'
                    )
                );
                return response()->json($resources, 200);
            } else {
                $affected = Color::where('colId', $xid)->delete();
    
                if ($affected > 0) {
                    $job = new LogSistema( $request->log['0']['optId'] , $request->log['0']['accId'] , $name , $empId , $request->log['0']['accDes']);
                    dispatch($job);            
                    $resources = array(
                    array("error" => '0', 'mensaje' => $request->log['0']['accMessage'], 'type' => $request->log['0']['accType'])
                    );
                } else {
                    $resources = array(
                        array("error" => "2", 'mensaje' => "No se encuentra registro", 'type' => 'warning')
                    );
                    return response()->json($resources, 200);
                }
            }*/
        }
    }
    