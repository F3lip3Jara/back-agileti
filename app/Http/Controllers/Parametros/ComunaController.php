<?php

namespace App\Http\Controllers\Parametros;

use App\Http\Controllers\Controller;
use App\Jobs\LogSistema;
use App\Jobs\ValidaEtiqueta;
use App\Models\FieldDefinition;
use App\Models\Parametros\Comuna;
use App\Models\viewComunas;
use Illuminate\Http\Request;
use  App\Models\Parametros\Proveedor;
use App\Models\Parametros\PrvDirDes;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\GuzzleException;

class ComunaController extends Controller
{
    public function index(Request $request)
    {
       /* $table   = 'comunas';
        $columns = Schema::getColumnListing($table);

        $columns = array_filter($columns, function ($column) {
            return $column !== 'empId'; // Columna a excluir
        });

        $columns = array_values($columns); // Reindexar el array si es necesa
        $filtros = $request['filter'];
        $filtros = json_decode(base64_decode($filtros));
        
       if(isset($filtros)){       
        $data     = viewComunas::query()->filter($filtros)->get();
       }else{
         $data    = viewComunas::select('*')->take(1500)->get();
       }
       
        $resources = array(
                "data"   => $data,
                "colums" => $columns
        );
       
        return response()->json($resources, 200);*/


        $query = viewComunas::select('*')
        ->orderBy('comunas.created_at', 'desc')
        ->first();

        if ($query) {
            $columns = array_keys($query->toArray());            
            // Obtener definiciones de campos filtrables
            $fieldDefinitions = FieldDefinition::whereIn('field_name', $columns)
                ->where('is_filterable', 1)
                ->get();
            // Crear array de definiciones formateado
            $columnDefinitions = [];
            foreach ($fieldDefinitions as $definition) {
                $columnDefinitions[] = [
                    'campo' => $definition->field_name,
                    'label' => $definition->label,
                    'data_type' => $definition->data_type
                ];
            }
        } else {
            $columnDefinitions = [];
        }

        $filtros = $request['filter'];
        $filtros = json_decode(base64_decode($filtros));
       // return $filtros;
        if(isset($filtros)){   
        
            $data     = viewComunas::query()->filter($filtros)
            ->orderBy('comunas.created_at', 'desc')
            ->get();
        } else {
            $data     = viewComunas::select('*')
                         ->orderBy('comunas.created_at', 'desc')
                        ->take(1500)->get();
        }
       
        $resources = array(
            "data" => $data,
            "columns" => $columnDefinitions
        );
        return response()->json($resources, 200); 	


    }

    public function update(Request $request)
    {
        $name        = $request['name'];
        $empId       = $request['empId'];

        $affected = Comuna::where('comId', $request->comId)->update([
            'comDes' => $request->comDes
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

        $affected = Comuna::create([
            'paiId'  => $request->paiId,
            'empId'  => 1,
            'regId'  => $request->regId,
            'ciuId'  => $request->ciuId,
            'comCod' => $request->comCod,
            'comDes' => $request->comDes
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
        $xid         = $request->comId;

        //si la variable es null o vacia elimino el rol
        $valida = Proveedor::all()->where('comId', $xid)->take(1);
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

            $valida = PrvDirDes::all()->where('comId', $xid)->take(1);
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

                $affected = Comuna::where('comId', $xid)->delete();
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


    public function valCodComuna(Request $request)
    {

        $data   = request()->all();
        $comCod   = $data['comCod'];
        $val    = Comuna::select('comCod')->where('comCod', $comCod)->get();
        $count  = 0;
        foreach ($val as $item) {
            $count = $count + 1;
        }

        return response()->json($count, 200);
    }


    public function indexFil(Request $request)
    {
        $data   = $request->all();
        $datos = Comuna::select(['comId', 'comDes'])
            ->where('ciuId', $data['ciuId'])
            ->where('paiId',  $data['paiId'])
            ->where('regId',  $data['regId'])
            ->get();
        return $datos;
    }

    function valEtiqeta(Request $request)
    {
        //Conectar a la api de etiqueta  por Guzzle
       $pedidos = [
      
'1559060503478-01',
'f1bce232-e726-47af-beba-487790ac4dd9',
'b7b2113c-b796-4307-9651-a343caf6f68c',
'cbb86ccf-348b-48c7-810a-2f0bef55288f',
'1560900504707-01',
'2d47f453-a09c-412c-898c-6ca72d7b2b3e',



        ];
        $job = new ValidaEtiqueta($pedidos, 'C');
        dispatch($job);
        return "OK";   
  
    
    }
}
