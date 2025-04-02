<?php

namespace App\Http\Controllers;

use App\Models\BinCol;
use App\Models\Mezcla ;
use App\Models\MezclaDet;
use App\Models\User;
use App\Models\viewMezclas;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Jobs\NotificacionesJob;

class MezclaController extends Controller
{

    public function index( Request $request )
    {
       
                return viewMezclas:: all()->take(3000);
           
    }


    public function ins(Request $request)
    {
                    $data      = $request->all();
                    $name      = $data['name'];
                    $mezUsu    = $name;
                    $mezLotSal = $data['mezLotSal'];
                    $mezKil    = $data['mezKil'];
                    $mezTip    = $data['mezTip'];
                    $mezDes    = '';
                    $mezEst    = 'A';
                    $mezEstCtl = 'P';
                    $mezMaq    = $data['mezMaq'];
                    $mezidEta  = $data['etapa'];
                    $producto  = $data['producto'];
                    $mezidPrd  = '';
                    $mezprdCod = '';
                    $mezprdDes = '';
                    $mezdManual= '';
                    $mezTurn   = $data['mezTurn'];

                    if($mezLotSal == ''){
                        $colbnum = BinCol::select('colbnum')->take(1)->get();

                        foreach( $colbnum as $item){
                           $bin =  $item->colbnum + 1;
                           $affected = BinCol::where('idColb' , 1)->update([
                            'colbnum' =>   $bin
                            ]);
                        }

                        if($affected){
                            $mezLotSal = strval($bin);
                        }
                     }else{

                        $fecha    = Carbon::now()->format('Y-m-d');
                        $count    = Mezcla::select("*")
                        ->where(DB::raw("(DATE_FORMAT(created_at,'%Y-%m-%d'))"), "=", $fecha)
                        ->where('mezTurn', $mezTurn)
                        ->where('mezMaq', $mezMaq)
                        ->count();

                        if($count == 0){
                            $count  = 1;
                            $digito = '0'.strval($count);
                        }else{
                            $count  = $count + 1;
                           if($count >=10){
                               $digito = strval($count);
                            }else{
                               $digito = '0'.strval($count);
                            }
                        }

                        $mezLotSal  = $mezLotSal.$digito;
                     }

                    if(sizeof($producto) > 0 ){
                        $mezidPrd  = $producto['idPrd'];
                        $mezprdCod = $producto['prdCod'];
                        $mezprdDes = $producto['prdDes'];
                    }

                    $mezProd   = $data['mezProd'];


                    $affected = Mezcla::create([
                        'empId'      => 1,
                        'mezUsu'     => $mezUsu,
                        'mezLotSal'  =>$mezLotSal,
                        'mezKil'     =>$mezKil,
                        'mezTip'     =>$mezTip,
                        'mezEst'     =>$mezEst,
                        'mezEstCtl'  =>$mezEstCtl,
                        'mezMaq'     =>$mezMaq,
                        'mezidEta'   =>$mezidEta,
                        'mezprdCod'  =>$mezprdCod,
                        'mezidPrd'   =>$mezidPrd,
                        'mezprdDes'  =>$mezprdDes,
                        'mezTurn'    =>$mezTurn,
                        'mezObs'     => ''
                    ]);



                    if( isset( $affected)){
                       $idMez = $affected->id;
                       foreach($mezProd as $item){

                        $idPrd = intval($item['idPrd']);


                            MezclaDet::create([
                                   'idMez'      => $idMez,
                                   'empId'      => 1,
                                   'mezdidPrd'  => $idPrd,
                                   'mezdprdCod' => $item['prdCod'],
                                   'mezdprdTip' => $item['prdTip'],
                                   'mezdprdDes' => $item['prdDes'],
                                   'mezdLotIng' => $item['mezLotIng'],
                                   'mezdUso'    => $item['mezdUso'],
                                   'mezdKil'    => $item['mezdKil'],
                                   'mezdManual' => $item['mezdManual']
                            ]);
                       }

                     $resources = array(
                        array("error" => "0", 'mensaje' => "Mezcla ingresada manera correcta",
                        'type'=> 'success')
                        );
                    return response()->json($resources, 200);


                    }else{
                        return response()->json('error' , 204);
                    }



    }

    function mezclaDet(Request $request){
      
                $data = $request->all();
                $idMez = $data['idMez'];

                return mezclaDet::select('*')->where('idMez' , $idMez)->get();

        
    }

    function confMezcla(Request $request){
        $id     = 0;
        $header = $request->header('access-token');
        $val    = User::select('token' , 'id', 'activado', 'idRol', 'name')->where('token' , $header)->get();
        $rol    = 0;

       
            foreach($val as $item){
                $id   = $item->id;
                $rol  = $item->idRol;
                $name = $item->name;
            }
         
                if($rol == 1 || $rol == 2){                  
                    $affected = Mezcla::where('idMez' , $request->id)->update(['mezEstCtl' => 'A']);
                    if($affected > 0){
                           //Ejecuto jonb de notificación -- MEZCLA ETAPA : 3
                            $job = new NotificacionesJob($request->lote_salida,3,'A',$name);
                            dispatch($job);

                        $resources = array(
                            array("error" => "0", 'mensaje' => "Mezcla autorizada de manera correcta",
                            'type'=> 'success')
                            );
                        return response()->json($resources, 200);
                    }else{
                        return response()->json('error', 204);
                    }
                }else{
                    $resources = array(
                        array("error" => "1", 'mensaje' => "No posee privilegio",
                        'type'=> 'danger')
                        );
                    return response()->json($resources , 200);
                }
           
    }

    function rechaMezcla(Request $request){
        $id     = 0;
        $header = $request->header('access-token');
        $val    = User::select('token' , 'id', 'activado', 'idRol' , 'name')->where('token' , $header)->get();
        $rol    = 0;

    
            foreach($val as $item){
                $id   = $item->id;
                $rol  = $item->idRol;
                $name = $item->name;
        
                if($rol == 1 || $rol == 2){
                    $affected = Mezcla::where('idMez' , $request->id)
                                        ->update([
                                                'mezEstCtl' => 'R',
                                                'mezObs' => $request->observaciones,
                                                    ]);
                    if($affected > 0){

                        $job = new NotificacionesJob($request->lote_salida,3 , 'R', $name);
                        dispatch($job);
                        $resources = array(
                            array("error" => "0", 'mensaje' => "Mezcla rechazada de manera correcta",
                            'type'=> 'success')
                            );
                        return response()->json($resources, 200);
                    }else{
                        return response()->json('error', 204);
                    }
                }else{
                    $resources = array(
                        array("error" => "1", 'mensaje' => "No posee privilegio",
                        'type'=> 'danger')
                        );
                    return response()->json($resources , 200);
                }
            }
    }

    public function filLotSal(Request $request)
    {
        
                $data   = request()-> all();
                $resources = viewMezclas::select('*')->where('lote_salida','like', $data['lote_salida'].'%')->get()->take(10);

                if(isset($resources)){
                        return response()->json($resources, 200);
                }else{
                    $resources = array(
                        array("error" => "0", 'mensaje' => "No se encuentra coincidencia",
                        'type'=> 'success')
                        );
                    return response()->json($resources, 200);
                }
          
    }

    public function getSaca(Request $request){
       
                    $resources = Mezcla::select('idMez', 'mezLotSal')
                    ->where('mezTip' , 'S')
                    ->where('mezEstCtl', 'A')
                    ->get();

                    if(isset($resources)){
                            return response()->json($resources, 200);
                    }else{
                        $resources = array(
                            array("error" => "0", 'mensaje' => "No se encuentra coincidencia",
                            'type'=> 'success')
                            );
                        return response()->json($resources, 200);
                    }
            
           
    }

    public function getSacaBins(Request $request){
       
                    $resources = Mezcla::select('idMez', 'mezLotSal')
                    ->where('mezTip' , 'B')
                    ->where('mezEstCtl', 'A')
                    ->get();

                    if(isset($resources)){
                            return response()->json($resources, 200);
                    }else{
                        $resources = array(
                            array("error" => "0", 'mensaje' => "No se encuentra coincidencia",
                            'type'=> 'success')
                            );
                        return response()->json($resources, 200);
                    }
          
    }


}
