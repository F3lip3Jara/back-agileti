<?php

namespace App\Http\Controllers\Parametros;

use App\Http\Controllers\Controller;
use App\Jobs\LogSistema;
use App\Models\Parametros\Etapa;
use App\Models\Parametros\Maquinas;
use Illuminate\Http\Request;

class EtapasController extends Controller
{
    public function index(Request $request)
    {
        $empId       = $request['empId'];
        return Etapa::select('*')->where('empId', $empId)->get();
    }

    public function ins(Request $request)
    {
        $name        = $request['name'];
        $empId       = $request['empId'];        
        $affected = Etapa::create([
            'etaDes'  => $request->etaDes,
            'etaProd' => $request->etaProd,
            'empId'   => $empId   
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
        $name        = $request['name'];
        $empId       = $request['empId'];
        
        $xid       = $request->etaId;
        $validaMaq = Maquinas::all()->where('etaId', $xid)->take(1);
        //si la variable es null o vacia elimino el rol
            $affected = Etapa::where('etaId', $xid)
                             ->where('empId',$empId)
                             ->delete();
                             
            if ($affected > 0) {
                $job = new LogSistema( $request->log['0']['optId'] , $request->log['0']['accId'] , $name , $empId , $request->log['0']['accDes'], $request->log['0']['accTip']);
                dispatch($job);
                
                $resources = array(
                    array("error" => '0', 'mensaje' => $request->log['0']['accMessage'], 'type' => $request->log['0']['accType'])
                );
                return response()->json($resources, 200);
            } else {
                $resources = array(
                    array("error" => "2", 'mensaje' => "No se encuentra registro", 'type' => 'warning')
                );
                return response()->json($resources, 200);
            }
        
    }
    public function up(Request $request)
    {
        $name        = $request['name'];
        $empId       = $request['empId'];
        $affected = Etapa::where('etaId', $request->etaId)
                         ->where('empId',$empId)
                         ->update(['etaDes' => $request->etaDes, 'etaProd' => $request->etaProd]);

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

    public function index1(Request $request)
    {   $empId  = $request['empId'];
     
        $etapas =  Etapa::select('*')->where('empId', $empId)->where('etaProd', 'S')->get();
        return response()->json($etapas, 200);
    }
}
