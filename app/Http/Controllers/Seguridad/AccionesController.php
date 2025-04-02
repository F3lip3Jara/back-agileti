<?php

namespace App\Http\Controllers\Seguridad;

use App\Http\Controllers\Controller;
use App\Jobs\LogSistema;
use Illuminate\Http\Request;

class AccionesController extends Controller
{
    public function store(Request $request)
    {
        $affected = 0;
        $name = $request->log['0']['optId'];
        $empId = $request->log['0']['accId'];
        $accDes = $request->log['0']['accDes'];
        $accTip = $request->log['0']['accTip'];

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
} 