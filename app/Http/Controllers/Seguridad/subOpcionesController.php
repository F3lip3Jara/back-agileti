<?php

namespace App\Http\Controllers\Seguridad;

use App\Http\Controllers\Controller;
use App\Jobs\LogSistema;
use App\Models\Seguridad\ModuleOpt;
use App\Models\Seguridad\SubModulo;
use App\Models\Seguridad\SubModuloOpt;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class subOpcionesController extends Controller
{
    public function index(Request $request){
        $empId = $request['empId'];
        $modulo = json_decode($request['modulo']);  
        return SubModulo::select('*')->where('empId', $empId)->where('molId', $modulo->molId)->get();
    }

    public function sinAsig(Request $request){

        $empId  = $request['empId'];     
        $modulo = json_decode($request['modulo']);          
        
        $opcionesNoExistentes = DB::table('segu_emp_mol_opt')
        ->leftJoin('segu_emp_mol_submol_opt', function ($join) use ($empId , $modulo) {
            $join->on('segu_emp_mol_opt.optId', '=', 'segu_emp_mol_submol_opt.optId')
                 ->on('segu_emp_mol_opt.empId', '=', 'segu_emp_mol_submol_opt.empId')
                 ->on('segu_emp_mol_opt.molId', '=', 'segu_emp_mol_submol_opt.molId');
        
        })
        ->whereNull('segu_emp_mol_submol_opt.optId')       
        ->select('segu_emp_mol_opt.optId', 'segu_opciones.optDes' , 'segu_emp_mol_opt.molId')
        ->leftJoin('segu_opciones', 'segu_emp_mol_opt.optId','=', 'segu_opciones.optId')
        ->where('segu_emp_mol_opt.empId' , $empId)
        ->where('segu_emp_mol_opt.molId', $modulo->molId)
        ->get();
         return  $opcionesNoExistentes;
      
      
    }

    public function asig(Request $request){
        $empId  = $request['empId'];     
        $modulo = json_decode($request['modulo']);       
        $molsId =  json_decode($request['molsId']);
        $opcionesExistentes =SubModuloOpt::select('segu_emp_mol_submol_opt.optId', 'segu_opciones.optDes' , 'segu_emp_mol_submol_opt.molId')
        ->leftJoin('segu_opciones', 'segu_emp_mol_submol_opt.optId','=', 'segu_opciones.optId')
        ->where('segu_emp_mol_submol_opt.empId' , $empId)
        ->where('segu_emp_mol_submol_opt.molId', $modulo->molId)
        ->where('segu_emp_mol_submol_opt.molsId', $molsId)
        ->get();
         return  $opcionesExistentes;
      
    }
    public function ins(Request $request){
        
        $empId  = $request['empId'];     
        $modulo = $request['modulo'];       
        $opt    = $request['opt'];
        $molId  = $modulo['molId'];
        $molsDes= $request['molsDes'];
        $name   = $request['name'];
        $molsId = $request['molsId'];

        if($molsId > 0 ){
            $affected = SubModuloOpt::where('molsId', $molsId)
            ->where('empId', $empId)
            ->delete();

            $affected = SubModulo::where('molsId', $molsId)
            ->where('empId', $empId)
            ->delete();
        }
        
        $affected = SubModulo::create([
            'molId'   => $molId,
            'molsDes' => $molsDes,
            'empId'   => $empId
        ]);

        $molsId = $affected->id;

       
        foreach($opt as $item){
            $optId = $item['optId'];
            $affected1 = ModuleOpt::where('molId', $molId)
                                    ->where('optId', $optId)
                                    ->where('empId', $empId)
                                    ->delete();  

            $affected = SubModuloOpt::create([
                                                'empId' => $empId,
                                                'molId' => $molId,
                                                'optId' => $optId,
                                                'molsId'=> $molsId
                                    ]);

        }

        if( isset( $affected)){
                 $job = new LogSistema( $request->log['0']['optId'] , $request->log['0']['accId'] , $name , $empId , $request->log['0']['accDes'], $request->log['0']['accTip']);
                dispatch($job); 
                $resources = array(
                array("error" => '0', 'mensaje' => $request->log['0']['accMessage'], 'type' => $request->log['0']['accType'])
        );
        return response()->json($resources, 200);
      
     }
    }

  public function del(Request $request){
    $empId  = $request['empId'];     
    $modulo = $request['modulo'];    
    $molId  = $modulo['molId']; 
    $name   = $request['name'];
    $molsId = $request['molsId'];
   // $opt    = $request['opt'];
    $opt    = SubModuloOpt::all();

        try{

            foreach($opt as $item){
                $optId = $item['optId'];

                $affected1 = ModuleOpt::create([
                    'optId' => $optId,
                    'molId' => $molId,
                    'empId' => $empId
                 ]);

              
                }
    
         $affected2 = SubModuloOpt::where('empId', $empId)
        ->where('molsId', $molsId)
        ->delete(); 

        $affected = SubModulo::where('molsId', $molsId)
                    ->where('empId', $empId)
                    ->delete();


        if (isset($affected)) {
            $job = new LogSistema( $request->log['0']['optId'] , $request->log['0']['accId'] , $name , $empId , $request->log['0']['accDes'], $request->log['0']['accTip']);
            dispatch($job);            
            $resources = array(
                array("error" => '0', 'mensaje' => $request->log['0']['accMessage'], 'type' => $request->log['0']['accType'])
            ); 
            return response()->json($resources, 200);
        }

        }catch(Exception $ex){
            $resources = array(
                array("error" => "1", 'mensaje' => "El Sub módulo no se puede eliminar",
                'type'=> 'danger')
                );
                
            return $resources;
        }
        
   
  }
}
