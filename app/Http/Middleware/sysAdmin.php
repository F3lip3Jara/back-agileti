<?php

namespace App\Http\Middleware;

use App\Models\Seguridad\Acciones;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class sysAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
      
        $method         = $request->method();
        $url            = $request->url();
        $ultimoSegmento = basename($url);
        $idUser         = 0;
        $header         = $request->header('access-token');
        $val            = User::select('token', 'id', 'activado', 'name', 'rolId', 'empId')->where('token', $header)->get();
        $log            = Acciones::select('*')->where('accUrl', $ultimoSegmento)->get();

        if ($header == '') {
            return response()->json('error', 203);
        } else {
            foreach ($val as $item) {
                if ($item->activado == 'A' && $item->rolId == 1 && $item->empId == 1) {
                    $idUser = $item->id;
                    $name = $item->name;
                    $rolId = $item->rolId;
                    $emp   = $item->empId;
                }
            }
            if ($idUser > 0) {
                if ($method === 'POST') {               
                    $datos  =$request->all();
                    $datos['idUser'] = $idUser;
                    $datos['name']   = $name;    
                    $datos['log']    = $log;   
                    $datos['emp']    = $emp;                                          
                    $request->replace([]);                   
                    $request->merge( $datos);
                }
                return $next($request);
            } else {
                $resources = array(
                    array(
                        "error" => "1", 'mensaje' => "Usuario desactivado",
                        'type' => 'danger'
                    )
                );
                return response()->json($resources, 403);
            }
        }
    }    
}
