<?php

namespace App\Http\Controllers\Parametros;

use App\Http\Controllers\Controller;
use App\Jobs\LogSistema;
use App\Models\Parametros\Producto;
use App\Models\Parametros\SubGrupo;
use Illuminate\Http\Request;

class SubGrupoController extends Controller
{
    public function index(Request $request)
    {

        $var = SubGrupo::select([
            'grpsId',
            'parm_sub_grupo.empId',
            'parm_sub_grupo.grpId',
            'grpsCod',
            'grpsDes',
            'grpCod',
            'grpDes'
        ])->join('parm_grupo', 'parm_sub_grupo.grpId', '=', 'parm_grupo.grpId')->get();

        return response()->json($var, 200);
    }

    public function update(Request $request)
    {
        $name        = $request['name'];
        $empId       = $request['empId'];               
        $data        = $request->all();
        $affected = SubGrupo::where('grpsId', $data['grpsId'])->update([
            'grpsCod' => $data['grpsCod'],
            'grpsDes' => $data['grpsDes']
        ]);

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
        $data        = $request->all();

        $affected = SubGrupo::create([
            'grpId'   => $data['grpId'],
            'empId'   => $empId,
            'grpsCod' => $data['grpsCod'],
            'grpsDes' => $data['grpsDes']
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
        $xid    = $request->grpsId;
        $valida = Producto::all()->where('grpsId', $xid)->take(1);
        //si la variable es null o vacia elimino el rol
        if (sizeof($valida) > 0) {
            //en el caso que no se ecuentra vacia no puedo eliminar
            $resources = array(
                array(
                    "error" => "1", 'mensaje' => "El sub grupo  no se puede eliminar",
                    'type' => 'danger'
                )
            );
            return response()->json($resources, 200);
        } else {
            $affected = SubGrupo::where('grpsId', $xid)->delete();

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



    public function valCodSubGrp(Request $request)
    {
        $data   = request()->all();
        $grpsCod   = $data['grpsCod'];
        $val    = SubGrupo::select('grpsCod')->where('grpsCod', $grpsCod)->get();
        $count  = 0;
        foreach ($val as $item) {
            $count = $count + 1;
        }

        return response()->json($count, 200);
    }


    public function indexFil(Request $request)
    {
        $data = $request->all();
        $datos = SubGrupo::select(['grpsId', 'grpsDes'])->where('grpId', $data['grpId'])->get();

        foreach ($datos as $item) {
            $resources = array(
                array(
                    'grpsId'     => $item->grpsId,
                    'grpsDes'    => $item->grpsDes
                )
            );
        }
        return $datos;
    }
}
