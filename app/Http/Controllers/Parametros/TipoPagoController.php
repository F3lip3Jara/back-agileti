<?php

namespace App\Http\Controllers\Parametros;

use App\Http\Controllers\Controller;
use App\Jobs\LogSistema;
use App\Models\OrdenVenta;
use App\Models\Parametros\TipoPago;
use Illuminate\Http\Request;

class TipoPagoController extends Controller
{
    public function index(Request $request)
    {

        return TipoPago::select('*')->get();
    }

    public function update(Request $request)
    {
        $name        = $request['name'];
        $empId       = $request['empId'];   
        $data        = $request->all();
    
        $affected = TipoPago::where('tipPagId', $data['tipPagId'])->update([
            'tipDes' => $data['tipDes'],
            'tipCod' => $data['tipCod']
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
        
        $affected = TipoPago::create([
            'tipCod' => $data['tipCod'],
            'tipDes' => $data['tipDes'],
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
        $xid         = $request->tipPagId;
       // $valida = OrdenVenta::all()->where('tiPagId', $xid)->take(1);
        $valida = []; 
       //si la variable es null o vacia elimino el rol
        if (sizeof($valida) > 0) {
            //en el caso que no se ecuentra vacia no puedo eliminar
            $resources = array(
                array(
                    "error" => "1", 'mensaje' => "El tipo de pago  no se puede eliminar , asociado a Región",
                    'type' => 'danger'
                )
            );
            return response()->json($resources, 200);
        } else {

                    $affected = TipoPago::where('tipPagId', $xid)->delete();
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

    public function valCodPai(Request $request)
    {

        $data   = request()->all();
        $tipCod   = $data['tipCod'];
        $val    = TipoPago::select('tipCod')->where('tipCod', $tipCod)->get();
        $count  = 0;
        foreach ($val as $item) {
            $count = $count + 1;
        }

        return response()->json($count, 200);
    }
}
