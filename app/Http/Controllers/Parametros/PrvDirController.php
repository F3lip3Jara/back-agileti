<?php

namespace App\Http\Controllers\Parametros;

use App\Http\Controllers\Controller;
use App\Jobs\LogSistema;
use App\Models\Parametros\PrvDirDes;
use App\Models\User;
use App\Models\viewProveedorDir;
use Illuminate\Http\Request;

class PrvDirController extends Controller
{
    public function index(Request $request)
    {

        return viewProveedorDir::all();
    }

    public function del(Request $request)
    {   
        $name        = $request['name'];
        $empId       = $request['empId'];
        $xid         = $request->id;
        $affected    = PrvDirDes::where('prvdId', $xid)->delete();

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

    public function ins(Request $request)
    {
        $name        = $request['name'];
        $empId       = $request['empId'];

        $affected = PrvDirDes::create([
            'empId'    => $empId,
            'prvId'    => $request->prvId,
            'prvdDir'  => $request->prvDir,
            'prvdNum'  => $request->prvNum,
            'paiId'    => $request->paiId,
            'regId'    => $request->regId,
            'comId'    => $request->comId,
            'ciuId'    => $request->ciuId
        ]);
        if (isset($affected)) {
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
