<?php

namespace App\Http\Controllers\Seguridad;

use App\Http\Controllers\Controller;
use App\Jobs\LogSistema;
use App\Models\Seguridad\MenuRol;
use App\Models\Seguridad\MenuSubModulo;
use App\Models\Seguridad\Module;
use App\Models\Seguridad\RolesModule;
use Illuminate\Http\Request;
use App\Models\Seguridad\ModuleOpt;
use App\Models\Seguridad\ModuleRol;
use Exception;
use Illuminate\Support\Facades\DB;

class RolesModuloController extends Controller
{
    public function index(Request $request)
    {
        $empId = $request['empId'];
        $datos = RolesModule::select( 'rolDes', 'molDes')
            ->join('segu_roles', 'segu_emp_mol_rol.rolId', '=', 'segu_roles.rolId')
            ->join('segu_module', 'segu_emp_mol_rol.molId', '=', 'segu_module.molId')
            ->where('segu_emp_mol_rol.empId', $empId )
            ->get();
        return response()->json($datos, 200);
    }

    public function ins(Request $request)
    {
       $molDes      = $request['molDes'];
       $molIcon     = $request['molIcon'];
       $opt         = $request['opt'];
       $name        = $request['name'];
       $empId       = $request['empId'];
       $roles       = $request['roles'];

       if($empId == null){
            $empId = 1;
       }

       $affected = Module::create([
        'molDes' => $molDes,
        'molIcon' => $molIcon,
        'empId'  => $empId
        ]);

        foreach($opt as $item){
           
            ModuleOpt::create([
                'optId' => $item['optId'],
                'molId' => $affected->id,
                'empId' => $empId
            ]);
        }

        foreach($roles as $item){
            RolesModule::create([
                'empId' => $empId,
                'rolId' => $item['rolId'],
                'molId' => $affected->id
            ]);
        }

        if (isset($affected)) {
            $job = new LogSistema( $request->log['0']['optId'] , $request->log['0']['accId'] , $name , $empId , $request->log['0']['accetaDes'],$request->log['0']['accType']);
                    dispatch($job); 
            $resources = array(
                      array("error" => '0', 'mensaje' => $request->log['0']['accMessage'], 'type' => $request->log['0']['accType'])
            );
            return response()->json($resources, 200); 
        } else {
            return response()->json('error', 204);
        }

    }

    public function up(Request $request){

        $molDes      = $request['molDes'];
        $molIcon     = $request['molIcon'];
        $opt         = $request['opt'];
        $molId       = $request['molId'];
        $ok          = $request['ok'];
        $name        = $request['name'];
        $empId       = $request['empId'];
        $roles       = $request['roles'];

        if($ok =='N'){      
            $valida = ModuleRol::all()->where('molId' , $molId)->take(1);           
            if(sizeof($valida) > 0 ){                
                $resources = array(
                    array("error" => "1", 'mensaje' => "El módulo no se puede modificar",
                    'type'=> 'danger')
                    );              
                return response()->json($resources, 200);                
        }else{
                $affected = ModuleOpt:: where('molId', $molId)->delete();
                $affected = ModuleRol:: where('molId', $molId)->delete();                           
                $affected = Module::where('molId', $molId)->update([
                        'molDes' => $molDes,
                        'molIcon'=> $molIcon,
                        'empId'  => $empId
                        ]);
                
                    foreach($opt as $item){                           
                            ModuleOpt::create([
                                'optId' => $item['optId'],
                                'molId' => $molId,
                                'empId' => $empId
                            ]);
                    }
                    foreach($roles as $item){                           
                        RolesModule::create([
                            'empId' => $empId,
                            'rolId' => $item['rolId'],
                            'molId' => $molId
                        ]);
                }
                
                if (isset($affected)) {
                    $job = new LogSistema( $request->log['0']['optId'] , $request->log['0']['accId'] , $name , $empId , $request->log['0']['accetaDes'],$request->log['0']['accType']);
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

        }else{
            $affected = ModuleOpt:: where('molId', $molId)->delete();
            $affected = ModuleRol:: where('molId', $molId)->delete();   
            $affected = Module::where('molId', $molId)->update([
                'molDes' => $molDes,
                'molIcon' =>$molIcon,
                'empId'  => $empId
                ]);

                    foreach($opt as $item){                       
                        ModuleOpt::create([
                            'optId' => $item['optId'],
                            'molId' => $molId,
                            'empId' => $empId
                        ]);
                    }
                    foreach($roles as $item){                           
                        RolesModule::create([
                            'empId' => $empId,
                            'rolId' => $item['rolId'],
                            'molId' => $molId
                        ]);
                    }
            
                    if (isset($affected)) {
                        $job = new LogSistema( $request->log['0']['optId'] , $request->log['0']['accId'] , $name , $empId , $request->log['0']['accetaDes'],$request->log['0']['accType']);
                        dispatch($job); 
                        $resources = array(
                            array("error" => '0', 'mensaje' => $request->log['0']['accMessage'], 'type' => $request->log['0']['accType'])
                        );
                    return response()->json($resources, 200);    
                    
                    }
           
        }
    }

    public function del(Request $request){

        $molId  = $request['molId'];        
        $name   = $request['name'];
        $empId  = $request['empId'];
       
        $valida = ModuleRol::select('*')
                 ->where('molId' , $molId)
                 ->where('empId' , $empId)
                 ->take(1)->get();
        //si la variable es null o vacia elimino el rol
        if(sizeof($valida) > 0 ){
            //en el caso que no se ecuentra vacia no puedo eliminar
            $resources = array(
                array("error" => "1", 'mensaje' => "El módulo no se puede modificar",
                'type'=> 'danger')
                );
            return response()->json($resources, 200);
        }else{
            try{
                $affected = ModuleOpt::where('molId', $molId)->delete();  
                $affected2 =RolesModule::where('molId', $molId)->delete();   
                $affected2 =Module::where('molId', $molId)->delete();  

            if (isset($affected2)) {
                
                $job = new LogSistema( $request->log['0']['optId'] , $request->log['0']['accId'] , $name , $empId , $request->log['0']['accetaDes'],$request->log['0']['accType']);
                dispatch($job); 
                $resources = array(
                    array("error" => '0', 'mensaje' => $request->log['0']['accMessage'], 'type' => $request->log['0']['accType'])
                );
                return response()->json($resources, 200); 
            
            }
            }catch(Exception $ex){
                $resources = array(
                    array("error" => "1", 'mensaje' => "El módulo no se puede modificar",
                    'type'=> 'danger')
                    );
                    
                return $resources;
            }
            
        }
    }    
    
    public function moduleSnAsig (Request $request){    
        $molId = $request['molId'];
        $empId = $request['empId'];

        $opcionesNoExistentes = DB::table('segu_emp_opt')       
        ->leftJoin('segu_emp_mol_opt', function ($join) use ($empId, $molId) {
            $join->on('segu_emp_mol_opt.optId', '=', 'segu_emp_opt.optId')      
                ->where('segu_emp_mol_opt.molId' , '=' , $molId )  
                ->where('segu_emp_opt.empId', '=', $empId);
        })
        ->leftJoin('segu_emp_mol_submol_opt', function ($join) use ($empId, $molId) {
            $join->on('segu_emp_mol_submol_opt.optId', '=', 'segu_emp_opt.optId')      
                ->where('segu_emp_mol_submol_opt.molId' , '=' , $molId )  
                ->where('segu_emp_mol_submol_opt.empId', '=', $empId);
        })
        ->whereNull('segu_emp_mol_opt.molId' )
        ->whereNull('segu_emp_mol_submol_opt.molId' )
        ->select('segu_emp_opt.optId', 'segu_opciones.optDes')
        ->join('segu_opciones' , 'segu_emp_opt.optId' , '=', 'segu_opciones.optId' )
        ->where('segu_emp_opt.empId' , $empId)
        ->get();

        return  $opcionesNoExistentes;

    }

    public function moduleAsig (Request $request){
        $empId = $request['empId'];
        $molId = $request['molId'];
    
        // Realiza la consulta de opciones
        $datos = ModuleOpt::select('segu_emp_mol_opt.molId', 'molDes', 'segu_emp_mol_opt.optId', 'optDes')
            ->join('segu_opciones', 'segu_opciones.optId', '=', 'segu_emp_mol_opt.optId')
            ->join('segu_modulo', 'segu_modulo.molId', '=', 'segu_emp_mol_opt.molId')
            ->where('segu_emp_mol_opt.molId', $molId)
            ->where('segu_emp_mol_opt.empId', $empId)
            ->get();
    
        // Realiza la consulta de subopciones
         $sub = MenuSubModulo::select('menu_roles_sub.molsDes')
            ->where('menu_roles_sub.molId', $molId)
            ->where('menu_roles_sub.empId', $empId)
            ->groupBy('menu_roles_sub.molsDes')
            ->get();
       
        $resultado = array('opt'=> $datos ,
                            'sub'=>$sub);
    
        // Devuelve la respuesta JSON
        return response()->json($resultado, 200);
    }

    public function menuAsig(Request $request){
            $empId  = $request['empId'];
            $rolId  = $request['rolId'];
    
            $menu = MenuRol::select('*')
                ->where('rolId', $rolId)
                ->where('empId', $empId)
                ->orderBy('optId', 'asc')
                ->get();
            
            $datos = [];
            
            $menu->groupBy('molId')->each(function ($items, $molId) use (&$datos) {
            $modulo = $items->first();    
            $opciones = $items->groupBy('optId')->map(function ($opts, $items) {
                    $opcion = $opts->first();     
                    return [
                        'optId'  => $opcion->optId,
                        'optDes' => $opcion->optDes,
                        'optLink'=> $opcion->optLink,
                        'optSub' => $opcion->optSub,
                        'molId'  => $opcion->molId
                    ];
                })->values()->all();
            
                $datos[] = [
                    'menu' => [
                        'molId'   => $modulo->molId,
                        'rolId'   => $modulo->rolId,
                        'molDes'  => $modulo->molDes,
                        'molIcon' => $modulo->molIcon,
                        'opciones' => $opciones
                    ],
                   
                ];
            });
            
            // Imprimir o retornar el resultado
            return response()->json($datos, 200);
    
    }
    
   
}
