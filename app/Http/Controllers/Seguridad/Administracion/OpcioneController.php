<?php

namespace App\Http\Controllers\Seguridad\Administracion;

use App\Http\Controllers\Controller;
use App\Jobs\LogSistema;
use App\Models\Seguridad\ModuleOpt;
use App\Models\Seguridad\Opciones;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use App\Models\Seguridad\Acciones;
class OpcioneController extends Controller
{
    public function index(Request $request)
    {   
        $table   = 'segu_opciones';
        $columns = Schema::getColumnListing($table);
        $columns = array_filter($columns, function ($column) {
            return $column !== 'empId'; // Columna a excluir
        });
        $columns = array_values($columns); // Reindexar el array si es necesa
        $filtros = $request['filter'];
        $filtros = json_decode(base64_decode($filtros));
    
       if(isset($filtros)){       
        $data     =   Opciones::query()->filter($filtros)->get();
       }else{
         $data    =  Opciones::select('*')->take(1500)->get();
       }
       
        $resources = array(
                "data"   => $data,
                "colums" => $columns
        );
 
	
	  return response()->json($resources, 200); 
	

    }
    

    public function ins(Request $request)
    {   
        $name        = $request['name'];
        $empId       = $request['emp'];
        $affected = Opciones::create([
            'optDes' => $request->optDes,
            'optLink' => $request->optLink,
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
        $xid         = $request->optId;
        $valida      = ModuleOpt::where('optId', $xid)->take(1)->get();
        $valida2     = Acciones::where('optId', $xid)->take(1)->get();
        //si la variable es null o vacia elimino el rol
        if (sizeof($valida) > 0 || sizeof($valida2) > 0) {
            //en el caso que no se ecuentra vacia no puedo eliminar
            $resources = array(
                array(
                    "error" => "1", 'mensaje' => "La opción no se puede eliminar",
                    'type' => 'danger'
                )
            );
            return response()->json($resources, 200);
        } else {
            $affected = Opciones::where('optId', $xid)->delete();

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

        $affected = Opciones::where('optId', $request->optId)->update([
            'optDes' => $request->optDes,
            'optLink' => $request->optLink,
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
    public function idexfil(Request $request)
    {
    }
}
