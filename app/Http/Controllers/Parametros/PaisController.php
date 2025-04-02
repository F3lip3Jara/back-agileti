<?php

namespace App\Http\Controllers\Parametros;

use App\Http\Controllers\Controller;
use App\Jobs\LogSistema;
use Illuminate\Http\Request;
use App\Models\Parametros\Pais;
use App\Models\Parametros\Proveedor;
use App\Models\Parametros\PrvDirDes;
use App\Models\Parametros\Region;
use Illuminate\Support\Facades\Schema;

class PaisController extends Controller
{
    public function index(Request $request)
    {
        $table   = 'parm_pais';
        $columns = Schema::getColumnListing($table);

        $columns = array_filter($columns, function ($column) {
            return $column !== 'empId'; // Columna a excluir
        });

        $columns = array_values($columns); // Reindexar el array si es necesa
        $filtros = $request['filter'];
        $filtros = json_decode(base64_decode($filtros));
        
       if(isset($filtros)){       
        $data     = Pais::query()->filter($filtros)->get();
       }else{
         $data    = Pais::all()->take(1000);
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
        $affected = Pais::where('paiId', $request->paiId)->update([
            'paiCod' => $request->paiCod,
            'paiDes' => $request->paiDes
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
        
        $affected = Pais::create([
            'paiCod' => $request->paiCod,
            'paiDes' => $request->paiDes,
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

        $xid    = $request->paiId;
        $name        = $request['name'];
        $empId       = $request['empId']; 
        $valida = Region::all()->where('paiId', $xid)->take(1);
        //si la variable es null o vacia elimino el rol
        if (sizeof($valida) > 0) {
            //en el caso que no se ecuentra vacia no puedo eliminar
            $resources = array(
                array(
                    "error" => "1", 'mensaje' => "El País  no se puede eliminar , asociado a una Región",
                    'type' => 'danger'
                )
            );
        } else {

            $valida = Proveedor::all()->where('paiId', $xid)->take(1);

            if (sizeof($valida) > 0) {
                $resources = array(
                    array(
                        "error" => "1", 'mensaje' => "El País  no se puede eliminar , asociado a Proveedor",
                        'type' => 'danger'
                    )
                );
                return response()->json($resources, 200);
            } else {

                $valida = PrvDirDes::all()->where('paiId', $xid)->take(1);

                if (sizeof($valida) > 0) {
                    //en el caso que no se ecuentra vacia no puedo eliminar
                    $resources = array(
                        array(
                            "error" => "1", 'mensaje' => "La Comuna no se puede eliminar , asociado a Dirección",
                            'type' => 'danger'
                        )
                    );
                    return response()->json($resources, 200);
                } else {
                    $affected = Pais::where('paiId', $xid)->delete();

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

    public function valCodPai(Request $request)
    {

        $data   = request()->all();
        $paiCod   = $data['paiCod'];
        $val    = Pais::select('paiCod')->where('paiCod', $paiCod)->get();
        $count  = 0;
        foreach ($val as $item) {
            $count = $count + 1;
        }

        return response()->json($count, 200);
    }
}
