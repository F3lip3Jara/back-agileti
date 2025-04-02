<?php

namespace App\Http\Controllers\Seguridad\Administracion;

use App\Http\Controllers\Controller;
use App\Jobs\LogSistema;
use App\Models\Seguridad\Acciones;
use Illuminate\Http\Request;

class AccionController extends Controller
{
    public function index(Request $request)
    {   
        $datos = Acciones::select('*')->where('optId', $request->optId)->get();
        return response()->json($datos, 200);
    }
    

    public function ins(Request $request)
    {   
        $name        = $request['name'];
        $empId       = $request['emp'];

        $affected = Acciones::create([
            'accDes'    => $request->accDes,
            'accUrl'    => $request->accUrl,
            'accetaDes' => $request->accetaDes,
            'acceVig'   => $request->acceVig, 
            'optId'     => $request->optId,
            'accType'   => $request->accType,
            'accMessage' => $request->accMessage
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
        $empId       = $request['emp'];

         $xid    = $request->accId;
      //  $valida = Producto::all()->where('monId', $xid)->take(1);
         $valida   = [];
        //si la variable es null o vacia elimino el rol
        if (sizeof($valida) > 0) {
            //en el caso que no se ecuentra vacia no puedo eliminar
            $resources = array(
                array(
                    "error" => "1", 'mensaje' => "La Moneda no se puede eliminar",
                    'type' => 'danger'
                )
            );
            return response()->json($resources, 200);
        } else {
            $affected = Acciones::where('accId', $xid)->delete();

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
    public function up(Request $request)
    {
        $name        = $request['name'];
        $empId       = $request['emp'];

        $affected = Acciones::where('optId', $request->optId)->
                              where('accId', $request->accId)->       
                              update([
                                    'accDes'    => $request->accDes,
                                    'accUrl'    => $request->accUrl,
                                    'accetaDes' => $request->accetaDes,
                                    'acceVig'   => $request->acceVig,                                     
                                    'accType'   => $request->accType,
                                    'accMessage' => $request->accMessage
                                ]);

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
