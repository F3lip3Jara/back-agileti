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

use Illuminate\Support\Facades\Storage;

class ComunaController extends Controller
{
    public function index(Request $request)
    {
    

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
            $filters = $filtros->filters;
            $sorting = $filtros->sorting;
        
            $data     = viewComunas::query()->filter($filters, $sorting)
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
      'P1-c1575510512771-01',
'P1-c1579420529403-01',
'P1-c1579680532637-01',
'P1-c1579930535333-01',
'P1-c1580080536895-01',
'P1-c1580110537343-01',
'P1-c1580160538541-01',
'P1-c1580270540207-01',
'P1-c1580300540629-01',
'P1-c1580380541959-01',
'P1-c1581140545280-01',
'P1-c1582000546877-01',
'P1-c1582180547375-01',
'P1-c1582270547607-01',
'P1-c1582450548023-01',
'P1-c1582780548945-01',
'P1-c3063326230',
'P1-c3214764169',
'P1-c3214765158',
'P1-c3216037257',
'P1-c3216041483',
'P1-c3216145942',
'P1-c3218021210',
'P1-v1579400535068-01',
'P1-v1580100538451-01',
'P1-v1581390541351-01',
'P1-v1582330542487-01',
'P1-v1582440542533-01',
'P1-v2000010566676753',
'P1-v2000014194318544',
'P1-v3216042290',
'P1-v3216761650'

      ];


      $chunks = array_chunk($pedidos, 20);
      $count = count($chunks);  
     
      
        foreach ($chunks as $chunk) {
         $job =new ValidaEtiqueta($chunk, 'C', 'CD01');
         dispatch($job);
        }

        $fecha = now()->format('Y-m-d H:i:s');      
        return "OK "."/".$count;
    
    }
}
