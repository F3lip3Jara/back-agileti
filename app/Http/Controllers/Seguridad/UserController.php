<?php

namespace App\Http\Controllers\Seguridad;


use App\Http\Controllers\Controller;
use App\Jobs\LogSistema;
use App\Models\Parametros\Empleado;
use App\Models\Seguridad\Empresa;
use App\Models\Seguridad\Roles;
use App\Models\User;
use App\Models\viewTblUser;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;



class UserController extends Controller
{
    public function authenticateUser(Request $request)
    {

        try{

            $data     = $request->all();
            $rep      = json_decode(base64_decode($data['Authentication']));
            $email    = $rep->email;
            $password = $rep->password;
            $remember = '';
            $crf      = '';
            $empNom   = '';
            $empApe   = '';
            
            
        if (Auth::attempt(['name' => $email, 'password' => $password])) {
            $token = Str::random(60);
            $user  = Auth::user();
            $activo = trim($user->activado);
            if ($activo == 'A') {
                $idUser = $user->id;
                User::where('id', $idUser)
                    ->update(['token' => $token]);             
                 
                $crf = csrf_token();
                $imgx = Empleado::select('emploAvatar', 'emploNom' , 'emploApe')->where('id', $idUser)->get();

                if(sizeof($imgx) > 0){
                  $img    = $imgx[0]['emploAvatar'];
                  $empNom = $imgx[0]['emploNom'];
                  $empApe = $imgx[0]['emploApe'];
                }else{
                    $img = '';
                }
               
                $xrol           =  Roles::select('rolDes')->where('rolId', $user->rolId)->get();
                $rol            =  $xrol[0]['rolDes'];                
                $xempresa       =  Empresa::select('empDes', 'empImg')->where('empId', $user->empId)->get();
                $empresa        =  $xempresa[0]['empDes'];
                $imgEmp         =  '';
                $controller     =  new MenuController;
                $menu           =  $controller->index($user->empId , $user->rolId);   
              
                $resources =
                    array(
                        'id'       => $user->id,
                        'name'     => $user->name,
                        'token'    => $token,
                        'reinicio' => $user->reinicio,
                        'crf'      => $crf,
                        'img'      => $img,
                        'rol'      => $rol,
                        'empresa'  => $empresa,
                        'menu'     => $menu,
                        'imgEmp'   => $imgEmp,
                        'empNom'   => $empNom,
                        'empApe'   => $empApe,
                        'error'    => '0'
                    );
                
                  $etaId    = 1;
                  $etaDesId = 1;
                  $name     = $user->name;
                  $empId    = $user->empId; 
                 // $encrypted =bcrypt($resources);
                  $job = new LogSistema($etaId , $etaDesId , $name , $empId , 'LOGEO DE USUARIO' , 'success');
                  dispatch($job);
               //   event(new MensajeEvent('Hola desde el servidor'));
                
                  return response()->json($resources, 200);
            } else {
                $resources = array(
                    array(
                        "error" => "1", 'mensaje' => "Usuario desactivado",
                        'type' => 'danger'
                    )
                );
                return response()->json($resources, 203);
            }
        } else {
            $resources = array(
                array(
                    "error" => "1", 'mensaje' => "El usuario no logeado",
                    'type' => 'danger'
                )
            );
            return response()->json($resources, 200);
        }

        }catch(QueryException $ex){
            $resources = array(
                array(
                    "error" => "1", 'mensaje' => "Error en el servidor",
                    'type' => 'danger',
                    "des" => $ex
                )
            );
            return response()->json($resources, 500);
        }
    }

    public function authenticateUserPda(Request $request)
    {

        try{

            $data     = $request->all();
            $rep      = json_decode(base64_decode($data['Authentication']));
            $email    = $rep->email;
            $password = $rep->password;
            $remember = '';
            $crf      = '';

        if (Auth::attempt(['name' => $email, 'password' => $password], $remember)) {
            $token = Str::random(60);
            $user  = Auth::user();
            $activo = trim($user->activado);
            
            if ($activo == 'A') {
                $idUser = $user->id;
                User::where('id', $idUser)
                    ->update(['token' => $token]);             
                
                $crf = csrf_token();
                $imgx = Empleado::select('emploAvatar')->where('id', $idUser)->get();

                if(sizeof($imgx) > 0){
                  $img  = $imgx[0]['emploAvatar'];
                }else{
                    $img = '';
                }

                $xrol           =  Roles::select('rolDes')->where('rolId', $user->rolId)->get();
                $rol            =  $xrol[0]['rolDes'];                
                $xempresa       =  Empresa::select('empDes', 'empImg')->where('empId', $user->empId)->get();
                $empresa        =  $xempresa[0]['empDes'];
              
                
                $resources =
                    array(
                        'id'       => $user->id,
                        'name'     => $user->name,
                        'token'    => $token,
                        'reinicio' => $user->reinicio,
                        'crf'      => $crf,
                        'img'      => $img,
                        'rol'      => $rol,
                        'empresa'  => $empresa,
                        'error'    => '0'
                    );
                
                  $etaId    = 1;
                  $etaDesId = 1;
                  $name     = $user->name;
                  $empId    = $user->empId; 
                 
                  $job = new LogSistema($etaId , $etaDesId , $name , $empId , 'LOGEO DE USUARIO PDA' , 'success');
                  dispatch($job);
               //   event(new MensajeEvent('Hola desde el servidor'));
                  return response()->json($resources, 200);
            } else {
                $resources = array(
                    array(
                        "error" => "1", 'mensaje' => "Usuario desactivado",
                        'type' => 'danger'
                    )
                );
                return response()->json($resources, 203);
            }
        } else {
            $resources = array(
                array(
                    "error" => "1", 'mensaje' => "El usuario no logeado",
                    'type' => 'danger'
                )
            );
            return response()->json($resources, 200);
        }

        }catch(QueryException $ex){
            $resources = array(
                array(
                    "error" => "1", 'mensaje' => "Error en el servidor",
                    'type' => 'danger',
                    "des" => $ex
                )
            );
            return response()->json($resources, 200);
        }
    }

    public function trabUsuarios(Request $request)
    {

        return $data = viewTblUser::select('*')->where('empId', $request['empId'])->get();
    }

    public function trabUsuariosAmd(Request $request)
    {
        return $data = viewTblUser::select('*')->get();
    }

    public function getUser(Request $request)
    {
        $token = $request->header('access-token');
        $datos  = viewTblUser::select('*')
                ->where('empId', $request['empId'])
                ->where('id',$request['idUser'])
                ->get();
        return response()->json($datos, 200);
    }

    public function setUserSession(Request $request)
    {
        $id     =  0;
        $header =  $request->header('access-token');
        $val    =  User::all()->where('token', $header);
        $data   =  $request->all();

        if (isset($val)) {
            foreach ($val as $item) {
                if ($item->activado = 'A') {
                    $id   = $item->id;
                    $name = $item->name;
                    $token = $item->token;
                }
            }

            if ($id > 0) {
                foreach ($data as $itemx) {
                    $usuariox =    $itemx['usuario'];
                }

                if ($usuariox == $name && $header == $token) {
                    $resources = array(
                        array(
                            "error" => "99", 'mensaje' => "Usuario valido",
                            'type' => 'success'
                        )
                    );
                    return response()->json($resources, 200);
                } else {
                    $resources = array(
                        array(
                            "error" => "4", 'mensaje' => "Usuario invalido",
                            'type' => 'danger'
                        )
                    );
                    return response()->json($resources, 200);
                }
            } else {
                $resources = array(
                    array(
                        "error" => "3", 'mensaje' => "Sin datos encontrados",
                        'type' => 'danger'
                    )
                );
            }
        } else {
            $resources = array(
                array(
                    "error" => "2", 'mensaje' => "Usuario invalido",
                    'type' => 'danger'
                )
            );
        }
    }

    public function valUsuario(Request $request)
    {  
        $data   = request()->all();
        $name   = $data['emploName'];
        $empId  = $request['empId'];
        $val    = User::select('name')
                        ->where('name', $name)
                        ->where('empId', $empId)
                        ->get();
        $count  = 0;
            foreach ($val as $item) {
                    $count = $count + 1;
                }
        return $count;
    }

    public function ins_Users(Request $request)
    {         
        $data            = request()->all();
        $usuario         = $data['usuario'];
        $empId           = $request['empId'];
        $nameI           = $request['name'];
        $emp             = $request['emp'];       
        $name            = $usuario['name'];        
        $imgName         = $usuario['emploAvatar'];
        $password        = $name;
        $emploNom        = strtoupper($usuario['empName']);
        $emploApe        = strtoupper($usuario['emploApe']);     
        $fecha           = Carbon::parse( $usuario['emploFecNac']);
        $fechaFormateada = $fecha->format('Y-m-d'); // Formato: 2025-02-13
        try{
            $emploFecNac = $fechaFormateada;
        }catch(Exception $ex){
            $emploFecNac = $fecha;          
            //2025-02-13T03:00:00.000Z
        }
       
        $rolId       = $usuario['rol'];
        $gerId       = $usuario['gerId'];

            $affect =User::create([
                    'email'    => '',
                    'password' => bcrypt($password),
                    'name'     => $name,
                    'imgName'  => '',
                    'activado' => 'A',
                    'token'    => '',
                    'rolId'    => $rolId,
                    'reinicio' => 'S',
                    'empId'    => $empId

                ]);
              

                Empleado::create([
                    'id'          => $affect->id,
                    'emploNom'    => $emploNom,
                    'emploApe'    => $emploApe,
                    'emploFecNac' => $emploFecNac,
                    'emploAvatar' => $imgName,
                    'empId'       => $empId,
                    'gerId'       => $gerId

                ]);

                $job = new LogSistema( $request->log['0']['optId'] , $request->log['0']['accId'] , $name , $empId , $request->log['0']['accDes'], $request->log['0']['accTip']);
                 dispatch($job);                
                $resources = array(
                    array("error" => '0', 'mensaje' => $request->log['0']['accMessage'], 'type' => $request->log['0']['accType'])
                );
                return response()->json($resources, 200);
    }

    public function up(Request $request)
    {   
        $rest    = request()->all();
        $data    = json_decode(base64_decode($rest['user']));
        $usuario = $data->usuario;
    
        $user = User::find($usuario->id);

        if (!$user) {

            $resources = array(
                array("error" => '1', 'mensaje' => 'Usuario no encontrado', 'type' => 'danger')
            );

            return response()->json($resources, 404);
        }
    
        $empleado = Empleado::select('id')->where('id', $usuario->id)->get();

        if (!$empleado) {

           
        }
    
        $dataToUpdate = [
            'rolId' => $usuario->rol > 0 ? $usuario->rol : $user->rolId,
            'reinicio' => 'N',
        ];

      
    
        if ($usuario->mantenerPassword === 1) {
            $dataToUpdate['password'] = bcrypt($usuario->password);
        }
        
    
         $user->update($dataToUpdate);
        
        
        
        $valida = Empleado::where('id', $usuario->id)->update([
            'emploNom'    => $usuario->empName,
            'emploApe'    => $usuario->emploApe,
            'emploFecNac' => Carbon::parse($usuario->emploFecNac)->format('Y-m-d'),
            'gerId'       => $usuario->gerId ?: 0,
            'emploAvatar' => $usuario->emploAvatar,
        ]);
    
        $job = new LogSistema($request->log['0']['optId'], $request->log['0']['accId'], $request->name, $request->empId, $request->log['0']['accDes'] , $request->log['0']['accTip']);
        dispatch($job);    
        $resources = array(
            array("error" => '0', 'mensaje' => $request->log['0']['accMessage'], 'type' => $request->log['0']['accType'])
        );
        return response()->json($resources, 200);
    }

    function getUsuarios(Request $request)
    {
        $xid    = $request->userid;
        $datos   = User::select('emploAvatar', 'gerId' , 'users.id')
        ->join('parm_empleados', 'users.id', '=', 'parm_empleados.id')
        ->where('users.id', $xid)->get();
        return response()->json($datos, 200);

    }

    public function upUsuario2(Request $request)
    {
        try {
            $data = request()->all();
            return $data;
            $usuario = json_decode(base64_decode($data['usuario']));
            $imgName  =  $usuario->imgName;
            $name     =  $usuario->name;
            $emploNom = $usuario->empName;
            $emploApe = $usuario->empApe;
            $id = User::where('name', $name)->get();
           
            return $id;
            $valida = Empleado::where('id', $id)->update([
                'emploAvatar' => $imgName,
                'emploNom'    => $emploNom,
                'emploApe'    => $emploApe
            ]);
        
            if ($valida == 1) {
                $resources = array(
                    array(
                        "error" => "0", 'mensaje' => "Usuario actualizado",
                        'type' => 'success'
                    )
                );
                return response()->json($resources, 200);
            } else {
                $resources = array(
                    array(
                        "error" => "1", 'mensaje' => "Error en el servidor",
                        'type' => 'danger'
                    )
                );
                return response()->json($resources, 500);
            }
        } catch (Exception $ex) {
            return $ex;
        }
    }

    function getUsuario(Request $request)
    {
        return $request;

    }

    function reiniciar(Request $request){
        $rest        = request()->all();
        $data        = json_decode(base64_decode($rest['user'])); 
        $xname       = $data->name; 
        $xid         = $data->usrid;
        try{
            $empId       = $rest['empId'];

        }catch(Exception $error){
            $empId       = $data->empId; 

        }      
        $name        = $rest['name'];
        $user        = User::find($xid);       
        $valida      = $user->update([               
            'password' => bcrypt($xname),
            'reinicio' => 'S'
        ]);

        if ($valida == 1) {                        
             $job = new LogSistema( $request->log['0']['optId'] , $request->log['0']['accId'] , $name , $empId , $request->log['0']['accDes'], $request->log['0']['accTip']);
               dispatch($job);                
            $resources = array(
                array("error" => '0', 'mensaje' => $request->log['0']['accMessage'], 'type' => $request->log['0']['accType'])
            );
            return response()->json($resources, 200);                       
        } else {
            $resources = array(
                array(
                    "error" => "1", 'mensaje' => "Error en el servidor",
                    'type' => 'danger'
                )
            );
            return response()->json($resources, 200);
        }
    }


    function deshabilitar(Request $request){ 
      
        $rest        = request()->all();
        $data        = json_decode(base64_decode($rest['user'])); 
        $xname       = $data->name; 
        $xid         = $data->usrid;
        $name        = $rest['name'];
        $user        = User::find($xid);
       
        $valida      = $user->update([               
            'password' => bcrypt($xname),
            'reinicio' => 'S',
            'activado' => 'D'
        ]);

        try{
            $empId       = $rest['empId'];

        }catch(Exception $error){
            $empId       = $data->empId; 
        }


        if ($valida == 1) {                        
            $job = new LogSistema( $request->log['0']['optId'] , $request->log['0']['accId'] , $name , $empId , $request->log['0']['accDes'], $request->log['0']['accTip']);
            dispatch($job);                
            $resources = array(
                array("error" => '0', 'mensaje' => $request->log['0']['accMessage'], 'type' => $request->log['0']['accType'])
            );
            return response()->json($resources, 200);                       
        } else {
            $resources = array(
                array(
                    "error" => "1", 'mensaje' => "Error en el servidor",
                    'type' => 'danger'
                )
            );
            return response()->json($resources, 200);
        }
    }

    function habilitar(Request $request){ 
        $rest        = request()->all();
        $data        = json_decode(base64_decode($rest['user'])); 
        $xname       = $data->name; 
        $xid         = $data->usrid;
        $name        = $rest['name'];
        $user        = User::find($xid);

        try{
            $empId       = $rest['empId'];

        }catch(Exception $error){
            $empId       = $data->empId; 
        }

        $valida      = $user->update([               
            'password' => bcrypt($xname),
            'reinicio' => 'S',
            'activado' => 'A'
        ]);

        if ($valida == 1) {                        
            $job = new LogSistema( $request->log['0']['optId'] , $request->log['0']['accId'] , $name , $empId , $request->log['0']['accDes'], $request->log['0']['accTip']);

                dispatch($job);                
            $resources = array(
                array("error" => '0', 'mensaje' => $request->log['0']['accMessage'], 'type' => $request->log['0']['accType'])
            );
            return response()->json($resources, 200);                       
        } else {
            $resources = array(
                array(
                    "error" => "1", 'mensaje' => "Error en el servidor",
                    'type' => 'danger'
                )
            );
            return response()->json($resources, 200);
        }
    }

    function cambiarPassword(Request $request){
        $rest        = request()->all();
        $data        = json_decode(base64_decode($rest['Authentication']));
        $xname       = $rest['name']; 
        $xid         = $rest['idUser'];
        $user        = User::find($xid);
        $currentPassword = $data->currentPassword;

        if (!Hash::check($currentPassword, $user->password)) {
            return response()->json(['error' => '1', 'mensaje' => 'Contraseña actual incorrecta', 'type' => 'danger'], 200);
        }else{
            $valida = $user->update([
                'password' => bcrypt($data->newPassword),
                'reinicio' => 'N',
                'activado' => 'A'
            ]);
            return response()->json(['error' => '0', 'mensaje' => 'Contraseña actualizada correctamente', 'type' => 'success'], 200);
        }

       
    }
}
