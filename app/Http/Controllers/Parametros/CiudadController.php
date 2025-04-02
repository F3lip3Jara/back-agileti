<?php

namespace App\Http\Controllers\Parametros;

use App\Http\Controllers\Controller;
use App\Jobs\LogSistema;
use App\Models\Parametros\Ciudad;
use App\Models\Parametros\Comuna;
use App\Models\viewCiudad;
use Illuminate\Http\Request;
use App\Models\Parametros\Proveedor;
use App\Models\Parametros\PrvDirDes;
use Illuminate\Support\Facades\Schema;

class CiudadController extends Controller
{
    public function index(Request $request)
    {
        $table   = 'ciudades';
        $columns = Schema::getColumnListing($table);
        $columns = array_filter($columns, function ($column) {
            return $column !== 'empId'; // Columna a excluir
        });
        $columns = array_values($columns); // Reindexar el array si es necesa
        $filtros = $request['filter'];
        $filtros = json_decode(base64_decode($filtros));        
       if(isset($filtros)){       
        $data     = viewCiudad::query()->filter($filtros)->get();
       }else{
         $data    = viewCiudad::select('*')->take(1500)->get();
       }       
        $resources = array(
                "data"   => $data,
                "colums" => $columns
        );
        return response()->json($resources, 200); 
    }

    public function update(Request $request)
    {
        $name        = $request['name'];
        $empId       = $request['empId']; 

        $affected = Ciudad::where('ciuId', $request->ciuId)->update([
            'ciuDes' => $request->ciuDes
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

        $affected = Ciudad::create([
            'paiId'   => $request->paiId,
            'empId'   => 1,
            'regId'   => $request->regId,
            'ciuCod'  => $request->ciuCod,
            'ciuDes'  => $request->ciuDes
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

        $xid    = $request->ciuId;
        $name   = $request['name'];
        $empId  = $request['empId']; 

        $valida = Comuna::all()->where('ciuId', $xid)->take(1);

        if (sizeof($valida) > 0) {
            //en el caso que no se ecuentra vacia no puedo eliminar
            $resources = array(
                array(
                    "error" => "1", 'mensaje' => "La Comuna no se puede eliminar, asociado a Comuna",
                    'type' => 'danger'
                )
            );
            return response()->json($resources, 200);
        } else {
            $valida = Proveedor::all()->where('ciuId', $xid)->take(1);

            if (sizeof($valida) > 0) {
                //en el caso que no se ecuentra vacia no puedo eliminar
                $resources = array(
                    array(
                        "error" => "1", 'mensaje' => "La Comuna no se puede eliminar, asociado a Proveedor",
                        'type' => 'danger'
                    )
                );
                return response()->json($resources, 200);
            } else {

                $valida = PrvDirDes::all()->where('ciuId', $xid)->take(1);
                if (sizeof($valida) > 0) {
                    //en el caso que no se ecuentra vacia no puedo eliminar
                    $resources = array(
                        array(
                            "error" => "1", 'mensaje' => "La Comuna no se puede eliminar, asociado a Dirección",
                            'type' => 'danger'
                        )
                    );
                    return response()->json($resources, 200);
                } else {
                    $affected = Ciudad::where('ciuId', $xid)->delete();
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
    }

    public function valCodCiudad(Request $request)
    {

        $data   = request()->all();
        $ciuCod   = $data['ciuCod'];
        $val    = Ciudad::select('ciuCod')->where('ciuCod', $ciuCod)->get();
        $count  = 0;
        foreach ($val as $item) {
            $count = $count + 1;
        }

        return response()->json($count, 200);
    }

    public function indexFil(Request $request)
    {   
        
        $data   = $request->all();
        $datos = Ciudad::select(['ciuId', 'ciuDes'])
            ->where('paiId', $data['paiId'])
            ->where('regId', $data['regId'])->get();
        return $datos;
    }
}
