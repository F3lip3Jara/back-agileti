<?php

namespace App\Http\Controllers\Parametros;

use App\Http\Controllers\Controller;
use App\Jobs\LogSistema;
use App\Models\Extrusion;
use App\Models\Parametros\MovRechazo;
use Illuminate\Http\Request;

class MovRechazoController extends Controller
{
    public function index(Request $request)
    {
        return MovRechazo::select('*')->join('parm_etapa', 'parm_mot_rechazo.etaId', '=', 'parm_etapa.etaId')
            ->get();
    }

    public function ins(Request $request)
    {
        $name        = $request['name'];
        $empId       = $request['empId'];

        $affected = MovRechazo::create([
            'motDes' => $request->motDes,
            'empId'  => $empId,
            'etaId'  => $request->etaId
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



    public function update(Request $request)
    {
        $name        = $request['name'];
        $empId       = $request['empId'];

        $affected = MovRechazo::where('motId', $request->motId)->update(
            ['motDes' => $request->motDes]
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


    public function del(Request $request)
    {
       $xid         = $request->motId;
       $name        = $request['name'];
       $empId       = $request['empId'];

      //  $valida = Extrusion::all()->where('extIdMot', $xid)->take(1);
      $valida =[];
     //si la variable es null o vacia elimino el rol
        if (sizeof($valida) > 0) {
            //en el caso que no se ecuentra vacia no puedo eliminar
            $resources = array(
                array(
                    "error" => "1", 'mensaje' => "El motivo no se puede eliminar",
                    'type' => 'danger'
                )
            );
            return response()->json($resources, 200);
        } else {
            $affected = MovRechazo::where('motId', $xid)->delete();

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

    public function index1(Request $request)
    {
        $data = explode(',', $request->get('etapas'));
        return MovRechazo::Buscarpor('idEta', $data)->get();
    }
}
