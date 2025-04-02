<?php

namespace App\Http\Controllers;

use App\Jobs\NotificacionesJob;
use App\Jobs\OrdenTrabajoCantProdJob;
use App\Models\Inyeccion;
use App\Models\InyeccionArchivo;
use App\Models\InyeccionDet;
use App\Models\InyeccionPeso;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class InyeccionController extends Controller
{
    public function index(Request $request)
    {

        return Inyeccion::all()->take(3000);
    }

    public function indexfil(Request $request)
    {

        $inyLotSal = $request->inyLotSal;
        if (strlen($inyLotSal) > 0) {
            $inyeccion = Inyeccion::select('*')->where('inyLotSal', $inyLotSal)->get();
            $idIny = 0;
            foreach ($inyeccion as $xidIny) {
                $idIny = $xidIny->idIny;
            }
        } else {
            $idIny = $request->idIny;
            $inyeccion = Inyeccion::select('*')->where('idIny', $idIny)->get();
        }

        $inyeccionDet = InyeccionDet::select(
            'idIny',
            'inyeccion_det.empId',
            'inydUso',
            'inydRol',
            'inydHorIni',
            'inydConmutacion',
            'inydPesoCaja',
            'inydCaja',
            'inydDefecto',
            'inydidMot',
            DB::raw('motDes as motivo'),
            'inydTipo',
            'inydEst'
        )
            ->join('mot_rechazo', 'mot_rechazo.idMot', '=', 'inyeccion_det.inydidMot')
            ->where('idIny', $idIny)
            ->where('inydTipo', 'O')
            ->get();

        $inyeccionDetC = InyeccionDet::select(
            'idIny',
            'inyeccion_det.empId',
            'inydUso',
            'inydRol',
            'inydHorIni',
            'inydLimp',
            'inydRechazo',
            'inydObs',
            'inydDefecto',
            'inydidMot',
            DB::raw('motDes as motivo'),
            'inydTipo',
            'inydEst'
        )
            ->join('mot_rechazo', 'mot_rechazo.idMot', '=', 'inyeccion_det.inydidMot')
            ->where('idIny', $idIny)
            ->where('inydTipo', 'C')
            ->get();

        $inyPeso = InyeccionPeso::select('*')->where('idIny', $idIny)->get();
        $inyArch = InyeccionArchivo::select('*')->where('idIny', $idIny)->get();


        $inyeccion = array(
            'inyeccion'    => $inyeccion,
            'inyeccionDet' => $inyeccionDet,
            'inyeccionDetC' => $inyeccionDetC,
            'inyPeso'      => $inyPeso,
            'inyeArch'     => $inyArch
        );

        return response()->json($inyeccion, 200);
    }


    public function ins(Request $request)
    {
        $data      = $request->all();


        foreach ($data as $item) {
            $inyMaq        = $item['inyMaq'];
            $inyPrdCaja    = $item['inyPrdCaja'];
            $inyPrdBolsa   = $item['inyPrdBolsa'];
            $inyLotCaja    = $item['inyLotCaja'];
            $inyLotBolsa   = $item['inyLotBolsa'];
            $extMez        = $item['extMez'];
            $inyTurn       = $item['inyTurn'];
            $inyDia        = $item['inyDia'];
            $idOt          = $item['idOt'];
            $idIny         = $item['idIny'];
            $name           = $item['name'];
            $rol            = $item['idRol'];
        }
        $inyUso    = $name;
        $fecha    = Carbon::now()->format('Y-m-d');
        $inyTip   = 'P';

        if ($inyTip == 'P') {
            $count    = Inyeccion::select("*")
                ->where('inyTurn', $inyTurn)
                ->where('inyMaq', $inyMaq)
                ->where(DB::raw("(DATE_FORMAT(created_at,'%Y-%m-%d'))"), "=", $fecha)
                ->where('idIny', '<>', $idIny)
                ->count();

            if ($count == 0) {
                $count  = 1;
                $digito = '0' . strval($count);
            } else {
                $count  = $count + 1;
                if ($count >= 10) {
                    $digito = strval($count);
                } else {
                    $digito = '0' . strval($count);
                }
            }
            $inyLotSal  = $inyMaq . '0' . $inyTurn . $inyDia . $digito;
        } else {
            /*   $cor = BinCol::select('colbnum')
                            ->where('idEta' , 5)
                            ->where('colbtip', $terTip)->get();

                       foreach($cor as $xcor){
                          $correlativo =   $xcor->colbnum  + 1;
                          $terLotSal   =strval($correlativo);

                       BinCol::where('idEta' , 5)
                       ->where('colbtip', $terTip)
                       ->update([
                           'colbnum'      => $correlativo
                           
                           ]);
                       }       */
        }

        $affected =  Inyeccion::where('idIny', $idIny)
            ->update([
                'inyUso'     => $inyUso,
                'inyEst'     => 'P',
                'inyEstCtl'  => 'P',
                'inyMaq'     => $inyMaq,
                'inyTip'     => $inyTip,
                'inyPrdCaja' => $inyPrdCaja,
                'inyPrdBolsa' => $inyPrdBolsa,
                'inyLotCaja' => $inyLotCaja,
                'inyLotBolsa' => $inyLotBolsa,
                'inyTurn'    => $inyTurn,
                'inyLotSal'  => $inyLotSal,
                'inyidMez'  => $extMez
            ]);

        if (isset($affected)) {
            $resources = array(
                array(
                    "error" => "0", 'mensaje' => "Inyección generada manera correcta",
                    'type' => 'success',
                    'data' => array(
                        array(
                            'inyLotSal' => $inyLotSal,
                            'idIny'   => $idIny
                        )
                    )
                )
            );
            return response()->json($resources, 200);
        } else {
            return response()->json('error', 204);
        }
    }

    public function insInyCierre(Request $request)
    {

        $data      = $request->all();

        foreach ($data as $item) {
            $idIny          = $item['id'];
            $inyDet         = $item['inyDet'];
            $inyCavTot      = $item['inyCavTot'];
            $inyCavAct      = $item['inyCavAct'];
            $inyLimpieza    = $item['inyLimpieza'];
            $inyReproceso   = $item['inyReproceso'];
            $inyMerma       = $item['inyMerma'];
            $name           = $item['name'];
            $rol            = $item['idRol'];
        }
        $inyUso    = $name;

        InyeccionDet::where('idIny', $idIny)
            ->where('inydTipo', 'O')->delete();

        if (sizeof($inyDet) > 0) {
            foreach ($inyDet as $det) {
                InyeccionDet::create([
                    'idIny'           => $idIny,
                    'empId'           => 1,
                    'inydUso'         => $name,
                    'inydRol'         => $rol,
                    'inydHorIni'      => $det['inydHorIni'],
                    'inydConmutacion' => $det['inydConmutacion'],
                    'inydPesoCaja'    => $det['inydPesoCaja'],
                    'inydCaja'        => $det['inydCaja'],
                    'inydDefecto'     => $det['inydDefecto'],
                    'inydidMot'       => $det['inydidMot'],
                    'inydTipo'        => 'O',
                    'inydEst'         => 'A'
                ]);
            }
        }

        $affected =   Inyeccion::where('idIny', $idIny)
            ->update([
                'inyCavTot'     => $inyCavTot,
                'inyCavAct'     => $inyCavAct,
                'inyLimpieza'   => $inyLimpieza,
                'inyReproceso'  => $inyReproceso,
                'inyMerma'      => $inyMerma,
                'inyEst'        => 'A'
            ]);

        $resources = array(
            array(
                "error" => "0", 'mensaje' => "Inyeccion generado manera correcta",
                'type' => 'success'
            )
        );
        return response()->json($resources, 200);
    }



    public function insTermCierreC(Request $request)
    {

        $data      = $request->all();


        foreach ($data as $item) {
            $idIny          = $item['id'];
            $inyDet         = $item['inyDet'];
            $inyPeso        = $item['inyPeso'];
            $name           = $item['name'];
            $rol            = $item['idRol'];
        }

        $inyUso    = $name;
        InyeccionDet::where('idIny', $idIny)
            ->where('inydTipo', 'C')->delete();
        if (sizeof($inyDet) > 0) {
            foreach ($inyDet as $det) {
                InyeccionDet::create([
                    'idIny'           => $idIny,
                    'empId'           => 1,
                    'inydUso'         => $name,
                    'inydRol'         => $rol,
                    'inydHorIni'      => $det['inydHorIni'],
                    'inydRechazo'     => $det['inydRechazo'],
                    'inydLimp'        => $det['inydLimp'],
                    'inydObs'         => $det['inydObs'],
                    'inydDefecto'     => $det['inydDefecto'],
                    'inydidMot'       => $det['inydidMot'],
                    'inydTipo'        => 'C',
                    'inydEst'         => 'A'
                ]);
            }
        }
        InyeccionPeso::where('idIny', $idIny)
            ->where('inyptip', 'C')->delete();

        if (sizeof($inyPeso) > 0) {
            foreach ($inyPeso as $peso) {
                InyeccionPeso::create([
                    'empId'     => 1,
                    'idIny'     => $idIny,
                    'inypUso'   => $name,
                    'inypRol'   => $rol,
                    'inyptip'   => 'C',
                    'inypPeso'  => $peso['inypPeso']
                ]);
            }
        }



        $resources = array(
            array(
                "error" => "0", 'mensaje' => "Inyeccion generado manera correcta",
                'type' => 'success'
            )
        );
        return response()->json($resources, 200);
    }

    public function insInyArcv(Request $request)
    {
        $data = $request->all();
        $archivo64 = $data['base64'];
        $idIny     = $data['idIny'];
        $archivophp = explode(',', $archivo64);
        $darchivo64 = base64_decode($archivophp[1]);
        $archivonom = $data['nombre'];
        // $filepath = '..\storage\app\public\calidad_archivos\ '. $archivonom;                  
        $valTermo   = InyeccionArchivo::select('idIny')
            ->where('idIny', $idIny)
            ->where('inyarlink', $archivonom)
            ->get();
        $val = 0;
        foreach ($valTermo as $item) {
            $val = $item->idTer;
        }
        if ($val > 0) {
            return $resources = array(
                array(
                    "error" => "1", 'mensaje' => "Archivo duplicado",
                    'type' => 'danger'
                )
            );
        } else {
            Storage::put('calidad_archivos/inyeccion/' . $archivonom, $darchivo64);
            $affected = InyeccionArchivo::create([
                'idIny'     => $idIny,
                'empId'     => 1,
                'inyarlink' => $archivonom
            ]);

            if (isset($affected)) {
                $resources = array(
                    array(
                        "error" => "0", 'mensaje' => "Archivo guardado de manera correcta",
                        'type' => 'success'
                    )
                );
                return response()->json($resources, 200);
            } else {
                return response()->json('error', 204);
            }
        }
    }


    public function delArcv(Request $request)
    {
        $data = $request->all();
        foreach ($data as $item) {
            $idIny   = $item['idIny'];
            $archivo = $item['archivo'];
        }
        $nombre = $archivo['nombre'];

        $valida = InyeccionArchivo::all()
            ->where('idIny', $idIny)
            ->where('inyarlink', $nombre)->take(1);

        if (sizeof($valida) <= 0) {
            $resources = array(
                array(
                    "error" => "1", 'mensaje' => "No existe arhivo",
                    'type' => 'danger'
                )
            );
            return response()->json($resources, 200);
        } else {
            $affected = InyeccionArchivo::where('idIny', $idIny)
                ->where('inyarlink', $nombre)
                ->delete();

            if ($affected > 0) {
                Storage::disk('public')->delete('calidad_archivos/inyeccion/' . $nombre);
                $resources = array(
                    array("error" => '0', 'mensaje' => "Archivo eliminado Correctamente", 'type' => 'warning')
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

    public function downloadFileIny(Request $request)
    {
        $archivo = $request->all();
        $url = Storage::url($archivo['nombre']);
        return $url;
    }


    public function InyConf(Request $request)
    {

        $data      = $request->all();

        foreach ($data as $item) {
            $lote_salida          = $item['lote_salida'];
            $idOrdt               = $item['idOrdt'];
            $name           = $item['name'];
            $rol            = $item['idRol'];
        }


        $inyeccion = Inyeccion::select('idIny')->where('inyLotSal', $lote_salida)->get();

        foreach ($inyeccion as $item) {
            $idIny = $item['idIny'];
        }

        if ($idIny > 0) {

            $affect = Inyeccion::where('idIny', $idIny)->update([
                'inyEst'   => 'A',
                'inyEstCtl' => 'A'
            ]);

            $job = new OrdenTrabajoCantProdJob($idIny, 7);
            dispatch($job);


            $job = new NotificacionesJob($lote_salida, 7, 'A', $name);
            dispatch($job);

            $resources = array(
                array(
                    "error" => "0", 'mensaje' => "Inyeccion Autorizado",
                    'type' => 'success'
                )
            );
            return $resources;
        } else {
            $resources = array(
                array(
                    "error" => "1", 'mensaje' => "Error en autorizar",
                    'type' => 'danger'
                )
            );
            return $resources;
        }
    }


    public function inyRechazo(Request $request)
    {

        $data      = $request->all();
        foreach ($data as $item) {
            $lote_salida    = $item['lote_salida'];
            $name           = $item['name'];
            $rol            = $item['idRol'];
        }

        $inyeccion = Inyeccion::select('idIny')->where('inyLotSal', $lote_salida)->get();

        foreach ($inyeccion as $item) {
            $idIny = $item['idIny'];
        }


        if ($idIny > 0) {
            $affect = Inyeccion::where('idIny', $idIny)->update([
                'inyEst'    => 'R',
                'inyEstCtl' => 'R'
            ]);

            $job = new NotificacionesJob($lote_salida, 7, 'R', $name);
            dispatch($job);

            $resources = array(
                array(
                    "error" => "0", 'mensaje' => "Inyeccion Rechazado",
                    'type' => 'success'
                )
            );

            return $resources;
        } else {
            $resources = array(
                array(
                    "error" => "1", 'mensaje' => "Error en Rechazar",
                    'type' => 'danger'
                )
            );

            return $resources;
        }
    }
}
