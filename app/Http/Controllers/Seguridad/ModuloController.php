<?php

namespace App\Http\Controllers\Seguridad;
use App\Http\Controllers\Controller;
use App\Models\Seguridad\Module;
use Illuminate\Http\Request;
use App\Jobs\LogSistema;

class ModuloController extends Controller
{
    public function index(Request $request)
    {   
        $empId=  $request['empId'];
        $datos = Module::select('*')->where('empId', $empId)->get();
        return response()->json($datos, 200);
    }

    public function log(Request $request)
    {
        $empId = $request['empId'];
        $name = $request['name'];
        $affected = 0;

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
}
