<?php

namespace App\Http\Controllers\Parametros;

use App\Http\Controllers\Controller;
use App\Jobs\LogSistema;
use App\Models\Parametros\Maquinas;
use Illuminate\Http\Request;

class MaquinasController extends Controller
{
    public function index(Request $request)
    {

        $maquinas = Maquinas::select('*')
            ->join('parm_etapa', 'parm_maquinas.etaId', '=', 'parm_etapa.etaId')
            ->get();
        return response()->json($maquinas, 200);
    }

    public function ins(Request $request)
    {
        $name        = $request['name'];
        $empId       = $request['empId']; 

        $affected = Maquinas::create([
            'etaId'  => $request->etaId,
            'maqCod' => $request->maqCod,
            'maqTip' => $request->maqTip,
            'maqDes' => $request->maqDes,
            'empId'  => $empId
        ]);

        if (isset($affected)) {
            $job = new LogSistema( $request->log['0']['optId'] , $request->log['0']['accId'] , $name , $empId , $request->log['0']['accDes']);
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

        $xid    = $request->maqId;
        $name        = $request['name'];
        $empId       = $request['empId']; 
    
        /* $valida = Maquinas::all()->where('idMaq' , $xid)->take(1);
                //si la variable es null o vacia elimino el rol
                if(sizeof($valida) > 0 ){
                      //en el caso que no se ecuentra vacia no puedo eliminar
                      $resources = array(
                        array("error" => "1", 'mensaje' => "La Maquina no se puede eliminar",
                        'type'=> 'danger')
                        );
                       return response()->json($resources, 200);
                }else{*/
        $affected = Maquinas::where('maqId', $xid)->delete();

        if ($affected > 0) {
            $job = new LogSistema( $request->log['0']['optId'] , $request->log['0']['accId'] , $name , $empId , $request->log['0']['accDes']);
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
        //}


    }
    public function update(Request $request)
    {
        $name        = $request['name'];
        $empId       = $request['empId']; 
    
        $affected = Maquinas::where('maqId', $request->maqId)
            ->update([
               
                'maqDes' => $request->maqDes,
                'maqCod' => $request->maqCod,
               ]);

        if ($affected > 0) {
            $job = new LogSistema( $request->log['0']['optId'] , $request->log['0']['accId'] , $name , $empId , $request->log['0']['accDes']);
            dispatch($job);            
            $resources = array(
                array("error" => '0', 'mensaje' => $request->log['0']['accMessage'], 'type' => $request->log['0']['accType'])
            );
            return response()->json($resources, 200);
        } else {
            return response()->json('error', 204);
        }
    }


    public function filEta(Request $request)
    {


        $data = $request->all();
        $maquinas = Maquinas::select('*')
            ->join('etapasUser', 'maquinas.idEta', '=', 'etapasUser.idEta')
            ->where('maquinas.idEta', $data['idEta'])
            ->get();

        return response()->json($maquinas, 200);
    }
}
