<?php

namespace App\Http\Controllers\Parametros;

use App\Http\Controllers\Controller;
use App\Jobs\CargadeRegiones;
use App\Jobs\Ciudades;
use App\Jobs\LogSistema;
use App\Models\Parametros\Ciudad;
use App\Models\Parametros\Pais;
use App\Models\Parametros\Proveedor;
use App\Models\Parametros\PrvDirDes;
use App\Models\Parametros\Region;
use App\Models\viewRegiones;
use Error;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class RegionController extends Controller
{
            public function index( Request $request)
        {  
                $table   = 'regiones';
                $columns = Schema::getColumnListing($table);

                $columns = array_filter($columns, function ($column) {
                    return $column !== 'empId'; // Columna a excluir
                });

                $columns = array_values($columns); // Reindexar el array si es necesa
                $filtros = $request['filter'];
                $filtros = json_decode(base64_decode($filtros));
                
            if(isset($filtros)){       
                $data     = viewRegiones::query()->filter($filtros)->get();
            }else{
                $data     = viewRegiones::select('*')->take(1500)->get();
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
          
                    $affected = Region::where('regId' , $request->regId)->update([
                        'regCod' => $request->regCod,
                        'regDes' => $request->regDes
                    ]);

                    if($affected > 0){
                        $job = new LogSistema( $request->log['0']['optId'] , $request->log['0']['accId'] , $name , $empId , $request->log['0']['accDes'], $request->log['0']['accTip']);
                         dispatch($job);            
                        $resources = array(
                            array("error" => '0', 'mensaje' => $request->log['0']['accMessage'], 'type' => $request->log['0']['accType'])
                        );
                        return response()->json($resources, 200);
                    }else{
                        return response()->json('error', 204);
                    }
                
        }

        public function ins(Request $request)
        {
            $name        = $request['name'];
            $empId       = $request['empId'];

                    $affected = Region::create([
                        'paiId'  => $request->paiId,
                        'empId'  => 1,
                        'regCod' => $request->regCod,
                        'regDes' => $request->regDes
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

        public function del(Request $request)
        {   
                $name        = $request['name'];
                $empId       = $request['empId'];
                $xid         = $request->regId;

                $valida = Ciudad::all()->where('regId' , $xid)->take(1);
                //si la variable es null o vacia elimino el rol
                if(sizeof($valida) > 0 ){
                      //en el caso que no se ecuentra vacia no puedo eliminar
                     $resources = array(
                        array("error" => "1", 'mensaje' => "La Región  no se puede eliminar, asociado a Ciudad",
                        'type'=> 'danger')
                        );
                       return response()->json($resources, 200);

                }else{

                    $valida = Proveedor::all()->where('regId' , $xid)->take(1);

                    if(sizeof($valida) > 0 ){
                        $resources = array(
                            array("error" => "1", 'mensaje' => "La región no se puede eliminar, asociado a Proveedor",
                            'type'=> 'danger')
                            );
                           return response()->json($resources, 200);

                    }else{

                        $valida = PrvDirDes::all()->where('regId', $xid)->take(1);
                        if(sizeof($valida) > 0 ){
                            //en el caso que no se ecuentra vacia no puedo eliminar
                           $resources = array(
                              array("error" => "1", 'mensaje' => "La Región no se puede eliminar, asociado a Dirección",
                              'type'=> 'danger')
                              );
                             return response()->json($resources, 200);
                        }else{

                            $affected = Region:: where('regId', $xid)->delete();

                            if($affected > 0){
                                $job = new LogSistema( $request->log['0']['optId'] , $request->log['0']['accId'] , $name , $empId , $request->log['0']['accDes'], $request->log['0']['accTip']);
                                dispatch($job);            
                                $resources = array(
                                    array("error" => '0', 'mensaje' => $request->log['0']['accMessage'], 'type' => $request->log['0']['accType'])
                                );
                                return response()->json($resources, 200);                
                            }else{
                                $resources = array(
                                array("error" => "2", 'mensaje' => "No se encuentra registro" ,'type'=> 'warning')
                                );
                                return response()->json($resources, 200);
                            }
                        }
                    }
                }
        }
        



    public function valCodReg(Request $request){
        $data   = request()-> all();
        $regCod   = $data['regCod'];
        $val    = Region::select('regCod')->where('regCod' , $regCod)->get();
        $count  = 0;
            foreach($val as $item){
                $count = $count + 1;
            }

        return response()->json($count , 200);
 
    }


    public function indexFil( Request $request)
    {     
        $data   = $request->all();
        $paiId  = $data['paiId'];
        $datos = Region::select(['regId' , 'regDes'])->where('paiId', $paiId)->get();
        
        if(sizeof($datos) > 0){           
            return $datos;
        }else{
            $resources = [];
        }
    }

    public function cargaMasiva(Request $request){

       try{
            $data        = $request->all();      
            $paiId       = $data['paiId'];
            $code        = Pais::select('paiCod')->where('paiId',$paiId)->get();      
            $iso2        = $code['0']['paiCod'];      
            $name        = $request['name'];
            $empId       = $request['empId'];
            $job         = new CargadeRegiones( $paiId , $iso2);
        
            dispatch($job);

            $job = new LogSistema( $request->log['0']['optId'] , $request->log['0']['accId'] , $name , $empId , $request->log['0']['accDes'], $request->log['0']['accTip']);
            dispatch($job);  
                    
            $resources = array(
                array("error" => '0', 'mensaje' => $request->log['0']['accMessage'], 'type' => $request->log['0']['accType'])
            );
            
        return response()->json($resources, 200);       
        }catch( Error $e){
            return response()->json($e->getMessage(), 500);
        }
    }

}
