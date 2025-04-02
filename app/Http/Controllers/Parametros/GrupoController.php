<?php
namespace App\Http\Controllers\Parametros;

use App\Http\Controllers\Controller;
use App\Jobs\LogSistema;
use App\Models\Parametros\Grupo;
use App\Models\Parametros\Producto;
use App\Models\Parametros\SubGrupo;
use Illuminate\Http\Request;

class GrupoController extends Controller
{
    public function index(Request $request)
    {
        return Grupo::select('*')->get();
    }

    public function update(Request $request)
    {
        $name        = $request['name'];
        $empId       = $request['empId'];
        $data        = $request->all();

        $affected = Grupo::where('grpId', $data['grpId'])->update(
            [
                'grpCod' => $data['grpCod'],
                'grpDes' => $data['grpDes']

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
        $data        = $request->all();
        
        $affected = Grupo::create([
            'grpCod' => $data['grpCod'],
            'grpDes' => $data['grpDes'],
            'empId'  => $empId
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

        $xid    = $request->grpId;
        $valida = SubGrupo::all()->where('grpId', $xid)->take(1);;
        //si la variable es null o vacia elimino el rol
        if (sizeof($valida) > 0) {
            //en el caso que no se ecuentra vacia no puedo eliminar
            $resources = array(
                array(
                    "error" => "1", 'mensaje' => "El Grupo no se puede eliminar",
                    'type' => 'danger'
                )
            );
            return response()->json($resources, 200);
        } else {

            $valida = Producto::all()->where('grpId', $xid)->take(1);;
            if (sizeof($valida) > 0) {
                $resources = array(
                    array(
                        "error" => "1", 'mensaje' => "El Grupo no se puede eliminar",
                        'type' => 'danger'
                    )
                );
                return response()->json($resources, 200);
            } else {
                $affected = Grupo::where('grpId', $xid)->delete();

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

    public function valGrpCod(Request $request)
    {
        $data   = request()->all();
        $grpCod   = $data['grpCod'];
        $val    = Grupo::select('grpCod')->where('grpCod', $grpCod)->get();
        $count  = 0;

        foreach ($val as $item) {
            $count = $count + 1;
        }
        return response()->json($count, 200);
    }
}
