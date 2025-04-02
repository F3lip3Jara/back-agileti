<?php

namespace App\Http\Controllers\Sd;

use App\Http\Controllers\Controller;
use App\Jobs\LogSistema;
use App\Models\Sd\Almacen;
use Illuminate\Http\Request;

class AlmacenController extends Controller
{
    public function index(Request $request)
    {   
       $data = $request->all();
       $centroId = $data['centroId'];
       $empId = $data['empId'];
        return Almacen::select('*')
        ->join('sd_centro', 'sd_centro.centroId', '=', 'sd_centro_alm.centroId')
        ->where('sd_centro_alm.empId', $empId)
        ->where('sd_centro_alm.centroId', $centroId)
        ->get();
    }


    public function indexFil(Request $request)
    {   
      $centroId  = $request['centroId'];
      $empId     = $request['empId'];

      return Almacen::select('*')      
      ->where('empId', $empId)
      ->where('centroId', $centroId)
      ->get();
        
    }

    public function update(Request $request)
    {   
        $name        = $request['name'];
        $empId       = $request['empId'];
        $data = $request->all();
        $centroId = $data['centroId'];
        $almId = $data['almId'];
        $almDes = $data['almDes'];

        $affected = Almacen::where('centroId', $centroId)
        ->where('empId', $empId)
        ->where('almId',  $almId)
        ->update(
            [
                
                'almDes' => $almDes,
                'almTip' => 'S',
                'almCap' => 0,
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
     
        $data = $request->all();
        $centroId = $data['centroId'];
        $almDes = $data['almDes'];  
    
        $affected = Almacen::create([
            'empId'    => $empId,
            'almDes' => $almDes,
            'almTip' => '',
            'almCap' => 0,
            'centroId'=> $centroId
        ]);

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
