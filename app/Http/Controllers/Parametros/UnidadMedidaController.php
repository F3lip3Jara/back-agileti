<?php


namespace App\Http\Controllers\Parametros;

use App\Http\Controllers\Controller;
use App\Jobs\LogSistema;
use App\Models\Parametros\Producto;
use App\Models\Parametros\UnidadMed;
use Illuminate\Http\Request;


class UnidadMedidaController extends Controller
{
    public function index(Request $request)
    {

        return UnidadMed::select('*')->get();
    }

    public function update(Request $request)
    {
        $name        = $request['name'];
        $empId       = $request['empId'];

        $affected = UnidadMed::where('unId', $request->unId)->update(
            [
                'unCod' => $request->unCod,
                'unDes' => $request->unDes

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

        $affected = UnidadMed::create([
            'unCod' => $request->unCod,
            'unDes' => $request->unDes,
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

        $xid         = $request->unId;
        $name        = $request['name'];
        $empId       = $request['empId'];


        $valida = Producto::all()
                    ->where('unId', $xid)
                    ->where('empId', $empId)
                    ->take(1);
        //si la variable es null o vacia elimino el rol
        if (sizeof($valida) > 0) {
            //en el caso que no se ecuentra vacia no puedo eliminar
            $resources = array(
                array(
                    "error" => "1", 'mensaje' => "La unidad de medida no se puede eliminar",
                    'type' => 'danger'
                )
            );
            return response()->json($resources, 200);
        } else {
            $affected = UnidadMed::where('unId', $xid)->delete();

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

    public function valUnCod(Request $request)
    {

        $data   = request()->all();
        $unCod  = $data['unCod'];
        $val    = UnidadMed::select('unCod')->where('unCod', $unCod)->get();
        $count  = 0;

        foreach ($val as $item) {
            $count = $count + 1;
        }

        return response()->json($count, 200);
    }
}
