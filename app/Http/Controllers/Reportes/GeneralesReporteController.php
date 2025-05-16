<?php

namespace App\Http\Controllers\Reportes;

use App\Http\Controllers\Controller;
use App\Models\Parametros\Ciudad;
use App\Models\Parametros\Color;
use App\Models\Parametros\Comuna;
use App\Models\Parametros\Moneda;
use App\Models\Parametros\Pais;
use App\Models\Parametros\Producto;
use App\Models\Parametros\Proveedor;
use App\Models\Parametros\Region;
use App\Models\Parametros\SubGrupo;
use App\Models\Parametros\Talla;
use App\Models\Parametros\TipoPago;
use App\Models\Parametros\UnidadMed;
use App\Models\Sd\SdTipClase;
use App\Models\Seguridad\Empresa;
use Illuminate\Http\Request;
use App\Models\Seguridad\LogSys;
use App\Models\Seguridad\Roles;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Laravel\Telescope\Storage\EntryModel;


class GeneralesReporteController extends Controller
{
    public function getReporteSeguridad(Request $request){
        $empId       = $request['empId'];
        $fechaActual = Carbon::now();
        $fechaDosMesesAtras = Carbon::now()->subMonths(2);

        // Obtener estadísticas de usuarios
        $totalUsuarios = User::where('empId', $empId)->count();
        $usuariosActivos = User::where('activado', 'A')->where('empId', $empId)->count();
        $usuariosInactivos = User::where('activado', 'D')->where('empId', $empId)->count();

        // Calcular incremento de usuarios
        $usuariosDosMesesAtras = User::where('created_at', '<', $fechaDosMesesAtras)->where('empId', $empId)->count();
        $incrementoUsuarios = $totalUsuarios - $usuariosDosMesesAtras;
        $porcentajeIncrementoUsuarios = $usuariosDosMesesAtras > 0 
            ? round(($incrementoUsuarios / $usuariosDosMesesAtras) * 100, 2)
            : 0;

        // Obtener distribución de roles
        $distribucionRoles = Roles::select('rolDes as rol', DB::raw('count(*) as cantidad'))
            ->join('users', 'users.rolId', '=', 'segu_roles.rolId')
            ->where('users.empId', $empId)
            ->groupBy('rolDes')
            ->get();

        // Obtener logs del sistema
        $logs = LogSys::select(
            'logId as id',
            'lgTip as tipo',
            'lgDes as mensaje',
            'lgName as usuario',
            'created_at as fecha',
            'lgDes1 as ip'
        )
        ->where('empId', $empId)
        ->orderBy('created_at', 'desc')
        ->limit(1000)
        ->get();

        // Obtener requests de Telescope
        $requests = EntryModel::where('type', 'request')
            ->orderBy('created_at', 'desc')
            ->limit(1000)
            ->get()
            ->map(function ($entry) {
                $content = $entry->content;
                return [
                    'id' => $entry->uuid,
                    'metodo' => $content['method'] ?? '',
                    'ruta' => $content['uri'] ?? '',
                    'estado' => (int)($content['response_status'] ?? 0),
                    'duracion' => round($content['duration'] ?? 0, 2) . 'ms',
                    'fecha' => Carbon::parse($entry->created_at)->format('Y-m-d H:i:s')
                ];
            });

        // Obtener estadísticas de actividad
        $sesionesUltimaSemana = LogSys::where('etaId', 1)
            ->where('etaDesId', 1)
            ->where('created_at', '>=', Carbon::now()->subWeek())
            ->where('empId', $empId)
            ->count();

        // Calcular incremento de sesiones
        $sesionesDosMesesAtras = LogSys::where('etaId', 1)
            ->where('etaDesId', 1)
            ->whereBetween('created_at', [$fechaDosMesesAtras, $fechaDosMesesAtras->copy()->addWeek()])
            ->count();

        $incrementoSesiones = $sesionesUltimaSemana - $sesionesDosMesesAtras;
        $porcentajeIncrementoSesiones = $sesionesDosMesesAtras > 0 
            ? round(($incrementoSesiones / $sesionesDosMesesAtras) * 100, 2)
            : 0;

        // Obtener usuarios más activos
        $usuariosMasActivos = LogSys::select('lgName as usuario', DB::raw('count(*) as sesiones'))
            ->where('etaId', 1)
            ->where('etaDesId', 1)
            ->where('created_at', '>=', Carbon::now()->subWeek())
            ->where('empId', $empId)
            ->groupBy('lgName')
            ->orderBy('sesiones', 'desc')
            ->limit(1000)
            ->get();

        // Obtener intentos fallidos y errores de plataforma desde Telescope
        $erroresTelescope = EntryModel::where('type', 'request')
            ->where('created_at', '>=', Carbon::now()->subWeek())
            ->get()
            ->filter(function ($entry) {
                $status = $entry->content['response_status'] ?? 0;
                return in_array($status, [400, 401, 500]);
            });

        $intentosFallidos = $erroresTelescope->filter(function ($entry) {
            $status = $entry->content['response_status'] ?? 0;
            return in_array($status, [400, 401, 500]);
        })->count();

        // Calcular incremento de intentos fallidos
        $intentosFallidosDosMesesAtras = EntryModel::where('type', 'request')
            ->where('created_at', '>=', $fechaDosMesesAtras)
            ->where('created_at', '<', $fechaDosMesesAtras->copy()->addWeek())
            ->get()
            ->filter(function ($entry) {
                $status = $entry->content['response_status'] ?? 0;
                return in_array($status, [400, 401, 500]);
            })->count();

        $incrementoIntentosFallidos = $intentosFallidos - $intentosFallidosDosMesesAtras;
        $porcentajeIncrementoIntentosFallidos = $intentosFallidosDosMesesAtras > 0 
            ? round(($incrementoIntentosFallidos / $intentosFallidosDosMesesAtras) * 100, 2)
            : 0;

        $erroresPlataforma = $erroresTelescope->filter(function ($entry) {
            $status = $entry->content['response_status'] ?? 0;
            return $status === 500;
        })->count();

        // Obtener ubicaciones de acceso
        $ubicacionesAcceso = LogSys::select('lgDes as ubicacion', DB::raw('count(*) as cantidad'))
            ->whereNotNull('lgDes')
            ->where('empId', $empId)
            ->groupBy('lgDes')
            ->orderBy('cantidad', 'desc')
            ->limit(1000)
            ->get();

        // Calcular actividad mensual
        $trimestres = [
            Carbon::now()->startOfQuarter(),
            Carbon::now()->startOfQuarter()->addMonths(3),
            Carbon::now()->startOfQuarter()->addMonths(6),
            Carbon::now()->startOfQuarter()->addMonths(9)
        ];

        $accesos = [];
        $operaciones = [];
        $errores = [];

        foreach ($trimestres as $trimestre) {
            // Accesos (sesiones exitosas)
            $accesos[] = LogSys::where('etaId', 1)
                ->where('etaDesId', 1)
                ->whereBetween('created_at', [$trimestre, $trimestre->copy()->addMonths(3)])
                ->where('empId', $empId)
                ->count();

            // Operaciones (requests exitosos)
            $operaciones[] = EntryModel::where('type', 'request')
                ->whereBetween('created_at', [$trimestre, $trimestre->copy()->addMonths(3)])
                ->where(function($query) {
                    $query->whereRaw("JSON_EXTRACT(content, '$.response_status') BETWEEN 200 AND 299");
                })
                ->count();

            // Errores (requests fallidos)
            $errores[] = EntryModel::where('type', 'request')
                ->whereBetween('created_at', [$trimestre, $trimestre->copy()->addMonths(3)])
                ->where(function($query) {
                    $query->whereRaw("JSON_EXTRACT(content, '$.response_status') IN (400, 401, 500)");
                })
                ->count();
        }

        // Construir respuesta
        $response = [
            'estadisticasUsuarios' => [
                'totalUsuarios' => $totalUsuarios,
                'usuariosActivos' => $usuariosActivos,
                'usuariosInactivos' => $usuariosInactivos,
                'distribucionRoles' => $distribucionRoles,
                'incrementoUsuarios' => [
                    'valor' => $incrementoUsuarios,
                    'porcentaje' => $porcentajeIncrementoUsuarios
                ]
            ],
            'estadisticasActividad' => [
                'sesionesUltimaSemana' => $sesionesUltimaSemana,
                'tiempoPromedioSesion' => '45 minutos',
                'usuariosMasActivos' => $usuariosMasActivos,
                'incrementoSesiones' => [
                    'valor' => $incrementoSesiones,
                    'porcentaje' => $porcentajeIncrementoSesiones
                ]
            ],
            'estadisticasSeguridad' => [
                'intentosFallidos' => $intentosFallidos,
                'ubicacionesAcceso' => $ubicacionesAcceso,
                'erroresPlataforma' => $erroresPlataforma,
                'incrementoIntentosFallidos' => [
                    'valor' => $incrementoIntentosFallidos,
                    'porcentaje' => $porcentajeIncrementoIntentosFallidos
                ]
            ],
            'logs' => $logs,
            'requests' => $requests,
            'actividadMensual' => [
                'labels' => ['Q1', 'Q2', 'Q3', 'Q4'],
                'datasets' => [
                    [
                        'label' => 'Accesos',
                        'data' => $accesos,
                        'backgroundColor' => '#4CAF50'
                    ],
                    [
                        'label' => 'Operaciones',
                        'data' => $operaciones,
                        'backgroundColor' => '#2196F3'
                    ],
                    [
                        'label' => 'Errores',
                        'data' => $errores,
                        'backgroundColor' => '#FF5722'
                    ]
                ]
            ]
        ];

        return response()->json($response);
    }

    public function getReporteParametros(Request $request){ 
             $empId       = $request['empId'];
            // Lista de etaId según la imagen
            $etaIds = [3,5,12,35,29,30,31,6,22,37,39,28,40,32,14];
            $actividades =[4,11,25,26,27,28,30,32,35,40,44,45,47,48,49,52,53,54,55,57,58,60,61,65,66];
            $actualizacion = [2,27,28,45,47,48,53,55,58,61,66, 40];
         
            // 1. DashboardCard
            $totalParametros = Producto::where('empId', $empId)->count() +
                               Moneda::where('empId', $empId)->count() +
                               UnidadMed::where('empId', $empId)->count() +
                               Color::where('empId', $empId)->count() +
                               Proveedor::where('empId', $empId)->count() +
                               Pais::count() +
                               Region::count() +
                               Ciudad::count() +
                               Comuna::count() +
                               Talla::where('empId', $empId)->count() +
                               TipoPago::where('empId', $empId)->count() +
                               SubGrupo::where('empId', $empId)->count();

            $totalParametrosMes = Producto::where('empId', $empId)->whereMonth('created_at', Carbon::now()->month)->count() +
                                  Moneda::where('empId', $empId)->whereMonth('created_at', Carbon::now()->month)->count() +
                                  UnidadMed::where('empId', $empId)->whereMonth('created_at', Carbon::now()->month)->count() +
                                  Color::where('empId', $empId)->whereMonth('created_at', Carbon::now()->month)->count() +
                                  Proveedor::where('empId', $empId)->whereMonth('created_at', Carbon::now()->month)->count() +
                                  Pais::whereMonth('created_at', Carbon::now()->month)->count() +                               
                                  Region::whereMonth('created_at', Carbon::now()->month)->count() +
                                  Ciudad::whereMonth('created_at', Carbon::now()->month)->count() +
                                  Comuna::whereMonth('created_at', Carbon::now()->month)->count() +
                                  SdTipClase::where('empId', $empId)->whereMonth('created_at', Carbon::now()->month)->count() +
                                  Talla::where('empId', $empId)->whereMonth('created_at', Carbon::now()->month)->count() +
                                  TipoPago::where('empId', $empId)->whereMonth('created_at', Carbon::now()->month)->count() +
                                  SubGrupo::where('empId', $empId)->whereMonth('created_at', Carbon::now()->month)->count() +
        
            $modificaciones = LogSys::
                                     where('empId', $empId)
                                    ->whereIn('etaDesId', $actualizacion)
                                    ->whereMonth('created_at', Carbon::now()->month)
                                    ->where('empId', $empId)
                                    ->count();


            $personalizaciones = Producto::where('empId', $empId)
                           ->where('prdIdExt', '>', 0)                         
                            ->count(); // Ejemplo: productos migrados/personalizados
            $empresasActivas =Empresa::count('empId');

        
            $dashboardCards = [
                [
                    'titulo' => 'Total Parámetros',
                    'valor' => $totalParametros,
                    'incremento' => '+'.($totalParametrosMes- 0).' este mes', // Puedes calcular el incremento real si tienes la lógica
                    'icono' => 'pi pi-cog',
                    'color' => 'blue'
                ],
                [
                    'titulo' => 'Modificaciones',
                    'valor' => $modificaciones,
                    'incremento' => '+'.($modificaciones - 0).' última mes', // Puedes calcular el incremento real si tienes la lógica
                    'icono' => 'pi pi-sync',
                    'color' => 'orange'
                ],
                [
                    'titulo' => 'Personalizaciones',
                    'valor' => $personalizaciones,
                    'incremento' => '+'.($personalizaciones - 0).' nuevas', // Puedes calcular el incremento real si tienes la lógica
                    'icono' => 'pi pi-pencil',
                    'color' => 'green'
                ],
                [
                    'titulo' => 'Empresas Activas',
                    'valor' => $empresasActivas,
                    'incremento' => '+1 este mes', // Puedes calcular el incremento real si tienes la lógica
                    'icono' => 'pi pi-building',
                    'color' => 'purple'
                ]
            ];
        
            // 2. LogParametro
            $logs = LogSys::
                 whereIn('etaDesId', $actividades)
                ->where('empId', $empId)
                ->orderBy('created_at', 'desc')
                ->limit(20)
                ->get()
                ->map(function($log) {
                    return [
                        'id' => $log->logId,
                        'tipo' => $log->lgTip,
                        'parametro' => $log->lgDes,
                        'valor_anterior' => $log->lgDes1 ?? '-',
                        'valor_nuevo' => $log->lgDes2 ?? '-',
                        'usuario' => $log->lgName,
                        'fecha' => $log->created_at,
                        'empresa' => $log->empId ?? '-'
                    ];
                });
        
            // 3. ParametroPopular (ejemplo: los más usados en logs)
            $parametrosPopulares = LogSys::whereIn('etaDesId', $actividades)
                ->select('lgDes as nombre',DB::raw('count(*) as usos'))
                ->groupBy('lgDes')
                ->orderByDesc('usos')
                ->where('empId', $empId)
                ->limit(3)
                ->get()
                ->map(function($item) {
                    return [
                        'nombre' => $item->nombre,
                        'usos' => $item->usos,
                        'tendencia' => 'stable', // Puedes calcular tendencia si tienes datos históricos
                        'descripcion' => 'Parámetro con alta actividad'
                    ];
                });
        
            // 4. Cambios mensuales (ejemplo: cantidad de logs por mes)
            $cambiosMensuales = [
                'labels' => ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
                'datasets' => [
                    [
                        'label' => 'Modificaciones',
                        'data' => array_map(function($mes) use ($actualizacion, $empId) {
                            return LogSys::whereIn('etaDesId', $actualizacion)
                                ->whereMonth('created_at', $mes)
                                ->where('empId', $empId)
                                ->count();
                        }, range(1, 12)),
                        'backgroundColor' => '#42A5F5'
                    ]
                ]
            ];
        
            return response()->json([
                'dashboardCards' => $dashboardCards,
                'logs' => $logs,
                'parametrosPopulares' => $parametrosPopulares,
                'cambiosMensuales' => $cambiosMensuales
            ]);
        }
    
}
