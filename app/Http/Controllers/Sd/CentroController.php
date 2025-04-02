<?php

namespace App\Http\Controllers\Sd;

use App\Http\Controllers\Controller;
use App\Jobs\LogSistema;
use App\Models\Sd\Centro;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CentroController extends Controller
{
    public function index(Request $request)
    {
        return Centro::select('*')->get();
    }

    public function update(Request $request)
    {   
        $name        = $request['name'];
        $empId       = $request['empId'];
   

     
        $affected = Centro::where('centroId', $request->centroId)
        ->where('empId', $empId)
        ->update(
            [
                'cenDes'             => $request->cenDes,
                'cenDir'             => $request->cenDir,           
                'cenCap'             => $request->cenCap,         
                'cenContacto'        => $request->cenContacto,
                'centEmail'          => $request->centEmail,
                'cenHoraApertura'    => $request->cenHoraApertura,
                'cenHoraCierre'      => $request->cenHoraCierre,
                'cenStockLimitWeb'   => $request->cenStockLimitWeb,
                'cenStockLimiteRepo' => $request->cenStockLimiteRepo, 
                'cenTelefono'        => $request->cenTelefono,
                'cenLat'             => $request->cenLat,
                'cenLong'            => $request->cenLong,      
                'cenPlace'           => json_encode($request->cenDiasLaborales)
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

        $horaApertura = Carbon::parse($request->cenHoraApertura)->format('Y-m-d H:i:s');
        $horaCierre = Carbon::parse($request->cenHoraCierre)->format('Y-m-d H:i:s');  
        $affected = Centro::create([        
            'empId'          => $request->empId,
            'cenDes'         => $request->cenDes,
            'cenDir'         => $request->cenDir,           
            'cenCap'         => $request->cenCap,         
            'cenContacto'    => $request->cenContacto,
            'centEmail'      => $request->centEmail,
            'cenHoraApertura' => $horaApertura,
            'cenHoraCierre'    => $horaCierre,
            'cenStockLimitWeb' => $request->cenStockLimitWeb,
            'cenStockLimiteRepo'=> $request->cenStockLimiteRepo, 
            'cenTelefono'    => $request->cenTelefono,
            'cenLat'         => $request->cenLat,
            'cenLong'        => $request->cenLong,      
            'cenPlace' => json_encode($request->cenDiasLaborales)
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
