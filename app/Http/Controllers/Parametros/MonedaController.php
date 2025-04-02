<?php

namespace App\Http\Controllers\Parametros;

use App\Http\Controllers\Controller;
use App\Jobs\LogSistema;
use App\Models\Parametros\Moneda;
use App\Models\Parametros\MonedaConversion;
use App\Models\Parametros\Producto;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Support\Facades\Log;

class MonedaController extends Controller
{
    public function index(Request $request)
    {

        return Moneda::select('*')->get();
    }

    public function update(Request $request)
    {
        $name        = $request['name'];
        $empId       = $request['empId'];
        
        $affected = Moneda::where('monId', $request->monId)->update(
            [
                'monCod'   => $request->monCod,
                'monDes'   => $request->monDes,
                'monIntVal'=> $request->monIntVal,
                'monInt'   => $request->monInt,
                'monIntArray'=> $request->monIntArray,
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
        $name        = $request['name'];
        $empId       = $request['empId'];

        $affected = Moneda::create([
            'monCod'     => $request->monCod,
            'monDes'     => $request->monDes,
            'monIntVal'  => $request->monIntVal,
            'monInt'     => $request->monInt,
            'monIntArray'=> $request->monIntArray,
            'empId'      => 1
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
        $xid         = $request->monId;
        $name        = $request['name'];
        $empId       = $request['empId'];

        $valida = Producto::all()->where('monId', $xid)->take(1);
        //si la variable es null o vacia elimino el rol
        if (sizeof($valida) > 0) {
            //en el caso que no se ecuentra vacia no puedo eliminar
            $resources = array(
                array(
                    "error" => "1", 'mensaje' => "La Moneda no se puede eliminar",
                    'type' => 'danger'
                )
            );
            return response()->json($resources, 200);
        } else {
            $affected = Moneda::where('monId', $xid)->delete();

            if ($affected > 0) {
                $job = new LogSistema( $request->log['0']['optId'] , $request->log['0']['accId'] , $name , $empId , $request->log['0']['accDes'], $request->log['0']['accTip']);
                dispatch($job);            
                $resources = array(
                    array("error" => '0', 'mensaje' => $request->log['0']['accMessage'], 'type' => $request->log['0']['accType'])
                );
             return response()->json($resources, 200);
            } else {
                $resources = array(
                    array("error" => "2", 'mensaje' => "No se encuentra registro", 'type' => 'warning')
                );
                return response()->json($resources, 200);
            }
        }
    }

    public function valMonCod(Request $request)
    {

        $data   = request()->all();
        $monCod   = $data['monCod'];
        $val    = Moneda::select('monCod')->where('monCod', $monCod)->get();
        $count  = 0;

        foreach ($val as $item) {
            $count = $count + 1;
        }

        return response()->json($count, 200);
    }

    public function indicadores(){
        $hoy = Carbon::now('America/Santiago');
        
        // Si es fin de semana, retrocedemos al último día hábil
        if ($hoy->isWeekend()) {
            $hoy = $hoy->copy()->previousWeekday();
        }
        
        // Obtenemos el día hábil anterior
        $diaAnterior = $hoy->copy()->previousWeekday();

        // Verificar si hay datos en MonedaConversion
        $existenDatos = MonedaConversion::exists();

        if (!$existenDatos) {
            // Si no hay datos, consultamos el histórico
            $anioActual = $hoy->year;
            $anioAnterior = $anioActual - 1;
            
            $data = Moneda::where('monInt', 'S')->get();
            
            foreach($data as $item) {
                try {
                    $client = new \GuzzleHttp\Client([
                        'verify' => false,
                        'timeout' => 30,
                        'connect_timeout' => 30
                    ]);

                    // Consultar datos del año actual
                    $urlActual = 'http://api.cmfchile.cl/api-sbifv3/recursos_api/'.$item['monIntVal'].'/'.$anioActual.'?apikey=80e3f542faaf21efc24dd8111aca2eeb7dd28b28&formato=json';
                    $responseActual = $client->request('GET', $urlActual);
                    $datosActual = json_decode($responseActual->getBody(), true);

                    // Consultar datos del año anterior
                   /* $urlAnterior = 'http://api.cmfchile.cl/api-sbifv3/recursos_api/'.$item['monIntVal'].'/'.$anioAnterior.'?apikey=80e3f542faaf21efc24dd8111aca2eeb7dd28b28&formato=json';
                    $responseAnterior = $client->request('GET', $urlAnterior);
                    $datosAnterior = json_decode($responseAnterior->getBody(), true);*/

                    // Procesar y guardar los datos
                    $arr = $item['monIntArray'];
                    if (isset($datosActual[$arr])) {
                        foreach($datosActual[$arr] as $valor) {
                            MonedaConversion::updateOrCreate(
                                [
                                    'monId' => $item['monId'],
                                    'moncFecha' => Carbon::createFromFormat('Y-m-d', $valor['Fecha'])->format('Y-m-d')
                                ],
                                [
                                    'moncValor' => str_replace(',', '.', str_replace('.', '', $valor['Valor']))
                                ]
                            );
                        }
                    }

                   /* if (isset($datosAnterior[$arr])) {
                        foreach($datosAnterior[$arr] as $valor) {
                            MonedaConversion::updateOrCreate(
                                [
                                    'monId' => $item['monId'],
                                    'moncFecha' => Carbon::createFromFormat('Y-m-d', $valor['Fecha'])->format('Y-m-d')
                                ],
                                [
                                    'moncValor' => str_replace(',', '.', str_replace('.', '', $valor['Valor']))
                                ]
                            );
                        }
                    }*/

                } catch (\GuzzleHttp\Exception\ConnectException $e) {
                    return response()->json([
                        'error' => true,
                        'mensaje' => 'Error de conexión con API SBIF: ' . $e->getMessage()
                    ], 500);
                } catch (\Exception $e) {
                    return response()->json([
                        'error' => true,
                        'mensaje' => 'Error al consultar API SBIF: ' . $e->getMessage()
                    ], 500);
                }
            }
        }

        $validaHoy = MonedaConversion::where('moncFecha', $hoy->format('Y-m-d'))->exists();
        $validaAnterior = MonedaConversion::where('moncFecha', $diaAnterior->format('Y-m-d'))->exists();

        if ($validaHoy && $validaAnterior) {
            $resultado = $this->obtenerResultadoFormateado($hoy, $diaAnterior);
            return response()->json($resultado, 200);
        } else {
            $data = Moneda::where('monInt', 'S')->get();
           
            foreach($data as $item){
                $client = new \GuzzleHttp\Client([
                    'verify' => false,
                    'timeout' => 30,
                    'connect_timeout' => 30
                ]);
                
                $fechasAConsultar = [];
                if (!$validaHoy) $fechasAConsultar[] = $hoy;
                if (!$validaAnterior) $fechasAConsultar[] = $diaAnterior;
            
                foreach($fechasAConsultar as $fecha) {
                    if ($fecha->isWeekend()) {
                        continue;
                    }
                    
                    try {
                        $url = 'http://api.cmfchile.cl/api-sbifv3/recursos_api/'.$item['monIntVal'].'?apikey=80e3f542faaf21efc24dd8111aca2eeb7dd28b28&formato=json';
                        $response = $client->request('GET', $url);
                        $datos = json_decode($response->getBody(), true);
                        $arr = $item['monIntArray'];

                        if (isset($datos[$arr])) {
                            foreach($datos[$arr] as $valor) {
                                MonedaConversion::updateOrCreate(
                                    [
                                        'monId' => $item['monId'],
                                        'moncFecha' => Carbon::createFromFormat('Y-m-d', $valor['Fecha'])->format('Y-m-d')
                                    ],
                                    [
                                        'moncValor' => str_replace(',', '.', str_replace('.', '', $valor['Valor']))
                                    ]
                                );
                            }
                        }
                    } catch (\GuzzleHttp\Exception\ConnectException $e) {
                        return response()->json([
                            'error' => true,
                            'mensaje' => 'Error de conexión con API SBIF: ' . $e->getMessage()
                        ], 500);
                    } catch (\Exception $e) {
                        return response()->json([
                            'error' => true,
                            'mensaje' => 'Error al consultar API SBIF: ' . $e->getMessage()
                        ], 500);
                    }
                }
            }

            $resultado = $this->obtenerResultadoFormateado($hoy, $diaAnterior);
            return response()->json($resultado, 200);
        }
    }

    private function obtenerResultadoFormateado($hoy, $diaAnterior) {
        $monedas = MonedaConversion::select(
                'parm_moneda.monDes as nombre',
                'parm_moneda.monCod as codigo',
                'mc1.moncValor as valor_actual',
                'mc2.moncValor as valor_anterior',
                'mc1.moncFecha as fecha_actual',
                'mc2.moncFecha as fecha_anterior'
            )
            ->from('parm_moneda_conversion as mc1')
            ->join('parm_moneda', 'parm_moneda.monId', '=', 'mc1.monId')
            ->leftJoin('parm_moneda_conversion as mc2', function($join) use ($diaAnterior) {
                $join->on('mc2.monId', '=', 'mc1.monId')
                    ->where('mc2.moncFecha', '=', $diaAnterior->format('Y-m-d'));
            })
            ->where('mc1.moncFecha', '=', $hoy->format('Y-m-d'))
            ->get();

        $resultado = [];
        foreach ($monedas as $moneda) {
            $resultado[] = [
                'nombre' => $moneda->nombre,
                'codigo' => $moneda->codigo,
                'valor_actual' => [
                    'fecha' => $moneda->fecha_actual,
                    'valor' => floatval($moneda->valor_actual)
                ],
                'valor_anterior' => [
                    'fecha' => $moneda->fecha_anterior,
                    'valor' => floatval($moneda->valor_anterior)
                ]
            ];
        }

        return $resultado;
    }
}
