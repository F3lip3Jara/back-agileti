<?php

namespace App\Http\Middleware;

use App\Models\Seguridad\Acciones;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class postMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        
        $method = $request->method();
        $url    = $request->url();
        $ultimoSegmento = basename($url);
        $idUser = 0;
        $header = $request->header('access-token');
        $val = User::select('token', 'id', 'activado', 'name', 'rolId', 'empId', 'reinicio')->where('token', $header)->get();
        
        if ($header == '') {
            $resources = array(
                array(
                    "error" => "99", 'mensaje' => "Error",
                    'type' => 'danger'
                )
            );
            return response()->json($resources, 203);
        } else {
            foreach ($val as $item) {
                if ($item->activado == 'A') {
                    $idUser = $item->id;
                    $name = $item->name;
                    $rolId = $item->rolId;
                    $empId = $item->empId;
                }
            }
            if ($idUser > 0) {
                if ($method === 'POST') {    
                    $log    = Acciones::select('*')->where('accUrl', $ultimoSegmento)->get();           
                    $datos  =$request->all();
                    $datos['idUser'] = $idUser;
                    $datos['name']   = $name;
                    $datos['rolId']  = $rolId;
                    $datos['empId']  = $empId;  
                    $datos['log']    = $log;            
                    $request->replace([]);                   
                    $request->merge( $datos);

                }else{
                    $datos  =$request->all();
                    $datos['empId']  = $empId;
                    $datos['rolId']  = $rolId;
                    $datos['idUser'] = $idUser;
                    $request->replace([]);                   
                    $request->merge( $datos);
                }
                  
                return $next($request);
            } else {
                $resources = array(
                    array(
                        "error" => "99", 'mensaje' => "Usuario desactivado",
                        'type' => 'danger'
                    )
                );
                return response()->json($resources, 203);
            }
        }
    }    
}
