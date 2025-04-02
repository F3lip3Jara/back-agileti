<?php

namespace App\Http\Controllers\Parametros;

use App\Http\Controllers\Controller;
use App\Jobs\LogSistema;
use App\Models\Parametros\Proveedor;
use App\Models\viewProveedores;
use Exception;
use Freshwork\ChileanBundle\Facades\Rut;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class ProveedorController extends Controller
{
    public function index(Request $request)
    {
        $table   = 'proveedores';
        $columns = Schema::getColumnListing($table);
        $columns = array_filter($columns, function ($column) {
            return $column !== 'empId'; // Columna a excluir
        });
        $columns = array_values($columns); // Reindexar el array si es necesa
        $filtros = $request['filter'];
        $filtros = json_decode(base64_decode($filtros));
        
       if(isset($filtros)){       
        $data     = viewProveedores::query()->filter($filtros)->get();
       }else{
         $data    = viewProveedores::select('*')->take(1500)->get();
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
        $data        = $request->all();        
        $data['prvCli'] = $data['prvCli'] == 'true' ? 'S' : 'N';
        $data['prvPrv'] = $data['prvPrv'] == 'true' ? 'S' : 'N';
        $data['prvAct'] = $data['prvAct'] == 'true' ? 'S' : 'N';

        $affected = Proveedor::where('prvId',  $data['prvId'])->update([           
            'prvNom'   => $data['prvNom'],
            'prvNom2'  => $data['prvNom2'],
            'prvGiro'  => $data['prvGiro'],
            'prvDir'   => $data['prvDir'],
            'prvNum'   => $data['prvNum'],
            'prvTel'   => $data['prvTel'],
            'prvMail'  => $data['prvMail'],
            'prvCli'   => $data['prvCli'],
            'prvPrv'   => $data['prvPrv'],
            'paiId'    => $data['paiId']['paiId'],
            'regId'    => $data['regId']['regId'],
            'comId'    => $data['comId']['comId'],
            'ciuId'    => $data['ciuId']['ciuId'],
            'prvAct'   => $data['prvAct'],
            'prvLat'   => $data['prvLat'],
            'prvLong'  => $data['prvLong'],
            'prvPlace' => $data['prvPlace']
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
        $data['prvCli'] = $data['prvCli'] == 'true' ? 'S' : 'N';
        $data['prvPrv'] = $data['prvPrv'] == 'true' ? 'S' : 'N';
       
        $affected = Proveedor::create([
            'empId'    => $empId,
            'prvRut'   => $data['prvRut'],
            'prvNom'   => $data['prvNom'],
            'prvNom2'  => $data['prvNom2'],
            'prvGiro'  => $data['prvGiro'],
            'prvDir'   => $data['prvDir'],
            'prvNum'   => $data['prvNum'],
            'prvTel'   => $data['prvTel'],
            'prvMail'  => $data['prvMail'],
            'prvCli'   => $data['prvCli'],
            'prvPrv'   => $data['prvPrv'],
            'paiId'    => $data['paiId']['paiId'],
            'regId'    => $data['regId']['regId'],
            'comId'    => $data['comId']['comId'],
            'ciuId'    => $data['ciuId']['ciuId'],
            'prvAct'   => 'S',
            'prvLat'   => $data['prvLat'],
            'prvLong'  => $data['prvLong'],
            'prvPlace' => $data['prvPlace']
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


    public function valPrvRut(Request $request)
    {
        $data     = request()->all();
        $prvRut   = $data['prvRut'];
        $val      = Proveedor::select('prvRut')->where('prvRut', $prvRut)->get();
        $count  = 0;
        foreach ($val as $item) {
            $count = $count + 1;
        }

        if ($count >= 1) {
            $resources = array(
                array("error" => '2', 'mensaje' => "rut ya existe", 'type' => 'warning')
            );
            return response()->json($resources, 200);
        } else {
            try {

                $rut = Rut::parse($prvRut)->validate();

                if ($rut != 1) {
                    $resources = array(
                        array("error" => '1', 'mensaje' => "rut incorrecto", 'type' => 'warning')
                    );
                    return response()->json($resources, 200);
                } else {
                    $resources = array(
                        array("error" => '0', 'mensaje' => "rut correcto", 'type' => 'warning')
                    );
                    return response()->json($resources, 200);
                }
            } catch (Exception $ex) {
                $resources = array(
                    array("error" => '3', 'mensaje' => "rut incorrecto", 'type' => 'warning')
                );
                return response()->json($resources, 200);
            }
        }
    }


    public function datPrv(Request $request)
    {
        $data   = $request->all();
        $datos = Proveedor::select(['paiId', 'regId', 'comId', 'ciuId', 'prvAct'])->where('prvId', $data['prvId'])->get();

        foreach ($datos as $item) {
            $resources = 
                array(
                    'paiId'     => $item->paiId,
                    'regId'     => $item->regId,
                    'comId'     => $item->comId,
                    'ciuId'     => $item->ciuId,
                    'prvAct'    => $item->prvAct,
                )
            ;
        }
        return $resources;
    }

    public function selCliente(Request $request)
    {
        $data = $request->all();
        return viewProveedores::select('*')->get();
    }
}
