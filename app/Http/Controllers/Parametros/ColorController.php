<?php


namespace App\Http\Controllers\Parametros;

use App\Http\Controllers\Controller;
use App\Jobs\LogSistema;
use App\Models\Parametros\Color;
use App\Models\Parametros\Producto;
use Illuminate\Http\Request;

class ColorController extends Controller
{
    public function index(Request $request)
    {
        return Color::select('*')->get();
    }

    public function update(Request $request)    {   
        $name        = $request['name'];
        $empId       = $request['empId'];
        $data        = $request->all();

        $affected = Color::where('colId', $data['colId'])->update([
            'colCod' => $data['colCod'],
            'colDes' => $data['colDes'],
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
       // $name        = $request->name;
       // $empId       = $request->empId;

        $data  = $request->all();
        $name        = $data['name'];
        $empId       = $data['empId'];

   
        $affected = Color::create([
            'colCod' => $data['colCod'],
            'colDes' => $data['colDes'],
            'empId'  => $data['empId']
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
                $job = new LogSistema( $request->log['0']['optId'] , $request->log['0']['accId'] , $name , $empId , $request->log['0']['accDes'], $request->log['0']['accTip']);
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
        }
    }

    public function valColCod(Request $request)
    {
        $data   = request()->all();
        $colCod   = $data['colCod'];
        $val    = Color::select('colCod')->where('colCod', $colCod)->get();
        $count  = 0;

        foreach ($val as $item) {
            $count = $count + 1;
        }

        return response()->json($count, 200);
    }

    public function colorInfo(){

        $data = request()->all();
        $colCod = $data['colCod'];

        $response = [];

        $json = file_get_contents(__DIR__ . '/colorjson.json');
        $data = json_decode($json);
        $colorFound = collect($data)->firstWhere('hex', $colCod);
        if($colorFound){
            $response = $colorFound;
            return response()->json($response , 200);
        }else{
            return response()->json($response , 200);
        }

  
    }
}
