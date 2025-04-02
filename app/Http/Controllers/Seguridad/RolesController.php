<?php

namespace App\Http\Controllers\Seguridad;

use App\Http\Controllers\Controller;
use App\Jobs\LogSistema;
use App\Models\Seguridad\Roles;
use App\Models\Seguridad\RolesModule;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RolesController extends Controller
{
    public function index( Request $request )
    {         
        return Roles:: select('*')->where('empId', $request['empId'])->get();
       
    }

    public function indexAdm( Request $request )
    {       
        $empId = $request['empId'];
        return Roles:: select('*')->where('empId', $empId)->get();
       
    }

    public function update(Request $request)
    {           
                $empId    = $request['empId'];
                $name     = $request['name'];       
                $affected = Roles::where('rolId' , $request->idRol)
                                  ->where('empId', $empId)
                                  ->update(['rolDes' => $request->rolDes]);


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
                $empId    = $request['empId'];
                $name     = $request['name'];
                $affected = Roles::create(['rolDes' => $request->rolDes,
                                            'empId' => $empId
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
            $empId    = $request['empId'];
            $name     = $request['name'];
            $xid      = $request->rolDes;
            $valida    = User::all()->where('rolId' , $xid)->take(1);
                //si la variable es null o vacia elimino el rol
                if(sizeof($valida) > 0 ){
                    //en el caso que no se ecuentra vacia no puedo eliminar
                    $resources = array(
                        array("error" => "1", 'mensaje' => "El rol no se puede eliminar",
                        'type'=> 'danger')
                        );
                    return response()->json($resources, 200);
                }else{
                    $affected = Roles::where('rolDes', $xid)
                                        ->where('empId', $empId)
                                        ->delete();

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

    public function rolSnAsig (Request $request){    
        $molId = $request['molId'];
        $empId = $request['empId'];     
        $rolesNoExistente = DB::table('segu_roles')       
        ->leftJoin('segu_emp_mol_rol', function ($join) use ($empId, $molId) {
            $join->on('segu_emp_mol_rol.rolId', '=', 'segu_roles.rolId')      
                ->where('segu_emp_mol_rol.molId' , '=' , $molId )  
                ->where('segu_emp_mol_rol.empId', '=', $empId);
        })
        ->whereNull('segu_emp_mol_rol.molId' )
        ->select('segu_roles.rolId', 'segu_roles.rolDes')
        ->where('segu_roles.empId', '=', $empId)
        ->get();

        return  $rolesNoExistente;

    }

    public function rolAsig (Request $request){
        $empId = $request['empId'];
        $molId = $request['molId'];
        
        $datos = RolesModule::select('segu_emp_mol_rol.rolId','rolDes')
            ->join('segu_roles', 'segu_emp_mol_rol.rolId', '=', 'segu_roles.rolId')
            ->where('segu_emp_mol_rol.molId', $molId)
            ->where('segu_emp_mol_rol.empId', $empId )
            ->get();
        return response()->json($datos, 200);
    }




}
