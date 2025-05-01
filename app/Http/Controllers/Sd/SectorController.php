<?php

namespace App\Http\Controllers\Sd;

use App\Http\Controllers\Controller;
use App\Jobs\LogSistema;
use App\Models\Sd\Sector;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class SectorController extends Controller
{
    public function index(Request $request)
    {   
        $table   = 'sd_cent_alm_sector';
        $data    = $request->all();
        $columns = Schema::getColumnListing($table);
        $columns = array_filter($columns, function ($column) {
            return $column !== 'empId'; // Columna a excluir
        });
       
        $columns = array_values($columns); // Reindexar el array si es necesario
        $filtros = $data['filter'];
        $filtros = json_decode(base64_decode($filtros));
       
    
        
       if(isset($filtros)){       
        $data     = Sector::query()->filter($filtros)  
                    ->where('sd_cent_alm_sector.empId', $data['empId'])
                    ->get();
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
        $affected = Sector::where('sectorId', $request['sectorId'])
        ->where('empId', $empId)
        ->update(
            [   'secDes' => $request['secDes'],
                'secCod' => $request['secCod'],
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
       
       
        $affected = Sector::create([
            'empId'     => $empId,
            'centroId'  => $request['centroId'],
            'almId'     => $request['almId'],
            'secDes'    => $request['secDes'],
            'secCod'    => $request['secCod'],
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
