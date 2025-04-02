<?php

namespace App\Http\Controllers\Seguridad\Administracion;

use App\Http\Controllers\Controller;
use App\Jobs\LogSistema;
use App\Models\Seguridad\EmpresaOpciones;
use App\Models\Seguridad\Module;
use App\Models\Seguridad\ModuleOpt;
use App\Models\Seguridad\Roles;
use App\Models\Seguridad\Empresa;
use App\Models\Seguridad\ModuleRol;
use App\Models\Seguridad\SubModuloOpt;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EmpresaController extends Controller
{
    public function index(Request $request)
    {
        return Empresa::select('empId','empDes','empDir','empRut','empGiro','empFono', 'empTokenOMS')->get();
    } 
    
    public function index1(Request $request)
    {

       
        return Empresa::select('empId','empDes','empDir','empRut','empGiro','empFono', 'empTokenOMS')
                        ->where('empId', $request['empId'])
                        ->get();
    }  


    public function ins(Request $request)
    {   

        $name        = $request['name'];
        $empId       = $request['emp'];

        $affected = Empresa::create([
                'empDes'    => $request->empDes,
                'empDir'    => $request->empDir,
                'empRut'    => $request->empRut,
                'empGiro'   => $request->empGiro,
                'empFono'   => $request->empFono,
                'empImg'    => $request->empImg,
                'empTokenOMS'=>$request->empTokenOMS

        ]);

        if (isset($affected)) {
           $affected1 =Roles::create([             
                'rolDes' => 'ADMINISTRADOR',
                'empId'  => $affected->id,
            ]);

            $xname =  utf8_encode('ADM-'.$affected->id);
              User::create([
                'name'      => $xname,
                'email'     => 'adm@contacto.cl',
                'rolId'     => $affected1->id,
                'activado'  => 'A',
                'imgName'   => '',
                'token'     => '',
                'password'  => bcrypt($xname),
                'empId'     =>  $affected->id,
             ]);

            
            EmpresaOpciones::create([
                'empId' => $affected->id,
                'optId' =>4
            ]);
            EmpresaOpciones::create([
                'empId' => $affected->id,
                'optId' =>5
            ]);
            EmpresaOpciones::create([
                'empId' => $affected->id,
                'optId' =>6
            ]);

            $affected2 = Module:: create([
                'empId'  => $affected->id,
                'molDes' => 'Seguridad',
                'molIcon'=> 'cilShieldAlt'
    
            ]);        
    
            ModuleOpt:: create([
                'molId' => $affected2->id,
                'empId'  => $affected->id,
                'optId' => 4
            ]);
    
            ModuleOpt:: create([
                'molId' => $affected2->id,
                'empId'  => $affected->id,         
                'optId' => 5
            ]);
    
            ModuleOpt::create([
                'molId' => $affected2->id,
                'empId' => $affected->id,       
                'optId' => 6
            ]);

            ModuleRol::create([
                'empId' => $affected->id,
                'rolId' => $affected1->id,
                'molId' => $affected2->id
            ]);
            
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

    public function up(Request $request)
    {
        $name        = $request['name'];
        $empId       = $request['emp'];
        $empresa     = $request['empresa'];
      
        $affected = Empresa::where('empId', $empresa['empId'])->update(
            [
                'empDes'    => $empresa['empDes'],
                'empDir'    => $empresa['empDir'],
                'empRut'    => $empresa['empRut'],
                'empGiro'   => $empresa['empGiro'],
                'empFono'   => $empresa['empFono'],
                'empImg'    => $empresa['empImg'],
                'empTokenOMS'=>$empresa['empTokenOMS'],
                
             ]
        );

        $job = new LogSistema( $request->log['0']['optId'] , $request->log['0']['accId'] , $name , $empId , $request->log['0']['accDes'], $request->log['0']['accTip']);
        dispatch($job);

        if ($affected > 0) {
            $resources = array(
                array("error" => '0', 'mensaje' => $request->log['0']['accMessage'], 'type' => $request->log['0']['accType'])
            );
            return response()->json($resources, 200); 

        } else {
            return response()->json('error', 204);
        }
    }

    public function empOptSnAsig (Request $request){           
        $empId = $request['empId'];        
        $opcionesNoExistentes = DB::table('segu_opciones')
        ->leftJoin('segu_emp_opt', function ($join) use ($empId) {
            $join->on('segu_opciones.optId', '=', 'segu_emp_opt.optId')
                ->where('segu_emp_opt.empId', '=', $empId);
        })
        ->whereNull('segu_emp_opt.optId')
        ->select('segu_opciones.optId', 'segu_opciones.optDes')
        ->get();
         return  $opcionesNoExistentes;
    }

    public function empOptAsig (Request $request){
      
        $empId = $request['empId'];    

        $datos = EmpresaOpciones::select('segu_emp_opt.empId', 'segu_emp_opt.optId', 'optDes')
            ->join('segu_opciones', 'segu_opciones.optId', '=', 'segu_emp_opt.optId')
            ->where('segu_emp_opt.empId', '=', $empId)
            ->get();
        return response()->json($datos, 200);
    }

    public function insEmpOpt(Request $request){       

        $name        = $request['name'];
        $emp         = 1;
        $empId       = $request['empId'];   
        $affected    = EmpresaOpciones:: where('empId', $empId)->delete();      
        $opt         = $request['asig'];
        
      foreach($opt as $item){                           
            EmpresaOpciones::create([
                'optId' => $item['optId'],
                'empId' => $empId
            ]);  
        }
    
        $opcionesNoExistentes = DB::table('segu_opciones')
        ->leftJoin('segu_emp_opt', function ($join) use ($empId) {
            $join->on('segu_opciones.optId', '=', 'segu_emp_opt.optId')
                ->where('segu_emp_opt.empId', '=', $empId);
        })
        ->whereNull('segu_emp_opt.optId')
        ->select('segu_opciones.optId', 'segu_opciones.optDes')
        ->get();


        
        foreach($opcionesNoExistentes as $item ){
            SubModuloOpt::where('optId', $item->optId)
                        ->where('empId', $empId)
                        ->delete();

            ModuleOpt::where('optId', $item->optId)
                       ->where('empId', $empId)
                    ->delete();
        
        }
        

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
    
    public function upImg(Request $request){
        $data = $request->all();
        return Empresa::select('empImg')->where('empId', $data['empresa'])->get();

    }
}
