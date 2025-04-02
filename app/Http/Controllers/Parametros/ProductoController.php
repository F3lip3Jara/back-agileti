<?php

namespace App\Http\Controllers\Parametros;

use App\Http\Controllers\Controller;
use App\Jobs\LogSistema;
use App\Models\Parametros\Producto;
use App\Models\Parametros\Moneda;
use App\Models\viewProductos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class ProductoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index(Request $request)
    {

        $table   = 'productos';
        $columns = Schema::getColumnListing($table);
        $filtros = $request['filter'];
        $filtros = json_decode(base64_decode($filtros));  
       if(isset($filtros)){ 
      
        $data     = viewProductos::query()->filter($filtros)->get();
       }else{
         $data    = viewProductos::all()->take(1000);
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
        $codigo_externo = $data['id_ext'];
        $url = $data['url'];
        if($codigo_externo == null){
            $codigo_externo = 0;
        }
        if($url == null){
            $url = '';
        }
       
        $monId = Moneda::where('monCod', $data['moneda'])->first()->monId;

        $affected = Producto::where('prdId', $data['id'])
        ->where('empId', $empId)
        ->update([
            'prdEan'   => $data['cod_barra'],
            'prdCod'   => $data['cod_pareo'],
            'prdDes'   => $data['descripcion'],
            'prdObs'   => $data['observaciones'],
            'prdRap'   => substr($data['cod_pareo'], 0, 6),
            'prdTip'   => $data['tipo']['code'],
            'prdCost'  => $data['costo'],
            'prdNet'   => $data['neto'],
            'prdBrut'  => $data['bruto'],
            'prdInv'   => $data['inventariable'],  
            'prdPes'   => $data['peso'],
            'prdMin'   => $data['minimo'],
            'monId'    => $monId,
            'grpId'    => $data['grupo']['id'],
            'grpsId'   => $data['sub_grupo']['id'],
            'unId'     => $data['medida']['id'],
            'colId'    => $data['color']['id'],
            'empId'    => $empId,
            'prdIdExt' => $codigo_externo,
            'prdUrl'   => $url,           
            'tallaId'  => $data['talla']['id'],
            'prdAncho' => $data['ancho'],
            'prdLargo' => $data['largo'],
            'prdAlto'  => $data['alto'],
            'prdPeso'  => $data['peso'],
            'prdVolumen' => $data['volumen']
        ]);
        if( isset( $affected)){
            $job = new LogSistema( $request->log['0']['optId'] , $request->log['0']['accId'] , $name , $empId , $request->log['0']['accDes'], $request->log['0']['accTip']);
            dispatch($job); 
            $resources = array(
                array("error" => '0', 'mensaje' => $request->log['0']['accMessage'], 'type' => $request->log['0']['accType'])
            );
            return response()->json($resources, 200);
        }else{
            return response()->json('error' , 204);
        }
    }

    public function ins(Request $request)
    {

        $name        = $request['name'];
        $empId       = $request['empId'];   
        $data        = $request->all();
        $codigo_externo = $data['id_ext'];
        $url = $data['url'];
        if($codigo_externo == null){
            $codigo_externo = 0;
        }
        if($url == null){
            $url = '';
        }
       
        $monId = Moneda::where('monCod', $data['moneda'])->first()->monId;


        $affected = Producto::create([
            'prdEan'   => $data['cod_barra'],
            'prdCod'   => $data['cod_pareo'],
            'prdDes'   => $data['descripcion'],
            'prdObs'   => $data['observaciones'],
            'prdRap'   => substr($data['cod_pareo'], 0, 6),
            'prdTip'   => $data['tipo']['code'],
            'prdCost'  => $data['costo'],
            'prdNet'   => $data['neto'],
            'prdBrut'  => $data['bruto'],
            'prdInv'   => $data['inventariable'],  
            'prdPes'   => $data['peso'],
            'prdMin'   => $data['minimo'],
            'monId'    => $monId,
            'grpId'    => $data['grupo']['id'],
            'grpsId'   => $data['sub_grupo']['id'],
            'unId'     => $data['medida']['id'],
            'colId'    => $data['color']['id'],
            'empId'    => $empId,
            'prdIdExt' => $codigo_externo,
            'prdUrl'   => $url,           
            'tallaId'  => $data['talla']['id'],
            'prdAncho' => $data['ancho'],
            'prdLargo' => $data['largo'],
            'prdAlto'  => $data['alto'],
            'prdPeso'  => $data['peso'],
            'prdVolumen' => $data['volumen']
        ]);

        if( isset( $affected)){
            $job = new LogSistema( $request->log['0']['optId'] , $request->log['0']['accId'] , $name , $empId , $request->log['0']['accDes'], $request->log['0']['accTip']);
            dispatch($job); 
            $resources = array(
                array("error" => '0', 'mensaje' => $request->log['0']['accMessage'], 'type' => $request->log['0']['accType'])
            );
            return response()->json($resources, 200);
        }else{
            return response()->json('error' , 204);
        }
    }

    
   
}
