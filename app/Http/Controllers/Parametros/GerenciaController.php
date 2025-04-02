<?php

namespace App\Http\Controllers\Parametros;

use App\Http\Controllers\Controller;
use App\Jobs\LogSistema;
use App\Models\Parametros\Empleado;
use App\Models\Parametros\Gerencia;
use Illuminate\Http\Request;

class GerenciaController extends Controller
{
    public function index(Request $request)
    {
        return Gerencia::select('*')->where('empId', $request['empId'])->get();
    }
    public function indexAdm(Request $request)
    {
        return Gerencia::select('*')->where('empId', $request['empId'])->get();
    }

    public function update(Request $request)
    {   
        $empId      = $request['empId'];
        $name       = $request['name'];

        $affected = Gerencia::where('gerId', $request->gerId)->update(['gerDes' => $request->gerDes]);
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
        $empId      = $request['empId'];
        $name       = $request['name'];
        
        $affected = Gerencia::create([
            'gerDes' => $request->gerDes,
            'empId'  => $empId
        ]);

        if (isset($affected)) {
            $job = new LogSistema( $request->log['0']['optId'] , $request->log['0']['accId'] , $name , $empId , $request->log['0']['accDes'], $request->log['0']['accTip']);
            dispatch($job);
            
            $resources = array(
                array("error" => '0', 'mensaje' => $request->log['0']['accMessage'], 'type' => $request->log['0']['accType'])
            );
            return response()->json($resources, 200);
        }
    }

    public function del(Request $request)
    {   
        $empId  = $request['empId'];
        $name   = $request['name'];
        $xid    = $request->gerId;
        $valida = Empleado::all()->where('gerId', $xid)->take(1);
        //si la variable es null o vacia elimino el rol
        if (sizeof($valida) > 0) {
            //en el caso que no se ecuentra vacia no puedo eliminar
            $resources = array(
                array(
                    "error" => "1", 'mensaje' => "La gerencia  no se puede eliminar",
                    'type' => 'danger'
                )
            );
            return response()->json($resources, 200);
        } else {
            $affected = Gerencia::where('gerId', $xid)->delete();

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
    }
}
