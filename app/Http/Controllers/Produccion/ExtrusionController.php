<?php

namespace App\Http\Controllers;

use App\Jobs\NotificacionesJob;
use App\Models\Extrusion;
use App\Models\ExtrusionDet;
use App\Models\User;
use App\Models\viewExtrusion;
use Carbon\Carbon;
use Error;
use Extursion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExtrusionController extends Controller
{

    public function index(Request $request)
    {
        return viewExtrusion::all()->take(3000);
    }


    public function ins(Request $request)
    {

        $data      = $request->all();
        foreach ($data as $item) {
            $extMaq    = $item['extMaq'];
            $extTurn   = $item['extTurn'];
            $diaJul    = $item['diaJul'];
            $extIdMez  = $item['extMez'];
            $name      = $item['name'];
        }

        $extUsu    = $name;

        $fecha    = Carbon::now()->format('Y-m-d');
        $count    = Extrusion::select("*")
            ->where('extTurn', $extTurn)
            ->where('extMaq', $extMaq)
            ->where(DB::raw("(DATE_FORMAT(created_at,'%Y-%m-%d'))"), "=", $fecha)
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

        $extLotSa  = $extMaq . '0' . $extTurn . $diaJul . $digito;

        $affected = Extrusion::create([
            'empId'      => 1,
            'extUsu'     => $extUsu,
            'extLotSal'  => $extLotSa,
            'extEstCtl'  => 'P',
            'extEst'     => 'P',
            'extMaq'     => $extMaq,
            'extTurn'    => $extTurn,
            'extIdMez'   => $extIdMez
        ]);

        if (isset($affected)) {
            $resources = array(
                array(
                    "error" => "0", 'mensaje' => "Extrusión generada manera correcta",
                    'type' => 'success',
                    'data' => array(
                        array(
                            'extLotSal' => $extLotSa,
                            'idExt'   => $affected->id
                        )
                    )
                )
            );

            return response()->json($resources, 200);
        } else {
            return response()->json('error', 204);
        }
    }

    public function insConfirma(Request $request)
    {
        $data      = $request->all();

        foreach ($data as $item) {
            $idExt     =  $item['idExt'];
            $extAnbob  =  $item['extAnbob'];
            $extFor    =  $item['extFor'];
            $extDet    =  $item['extrusionDet'];
            $idEta     =  $item['idEta'];
            $producto  =  $item['producto'];
            $name      = $item['name'];
            $rol        = $item['idRol'];
        }

        $extUsu    = $name;
        $extIdPrd  = 0;
        $extPrdCod = '';
        $extPrdDes = '';

        try {
            if (sizeof($producto) > 0) {
                $extIdPrd  = $producto['idPrd'];
                $extPrdCod = $producto['prdCod'];
                $extPrdDes = $producto['prdDes'];
            }
        } catch (Error $error) {
            $extIdPrd  = 0;
            $extPrdCod = '';
            $extPrdDes = '';
        }

        $affected = Extrusion::where('idExt', $idExt)
            ->update([
                'extEst'   => 'A',
                'extAnbob' => $extAnbob,
                'extFor'   => $extFor,
                'extidEta' => $idEta,
                'extIdPrd' => $extIdPrd,
                'extPrdCod' => $extPrdCod,
                'extPrdDes' => $extPrdDes
            ]);

        if ($rol <> 4) {
            $extdTip = 'J';
        } else {
            $extdTip = 'O';
        }

        if ($affected > 0) {
            foreach ($extDet as $item) {
                ExtrusionDet::create([
                    'idExt'      => $idExt,
                    'empId'      => 1,
                    'extdIzq'    => $item['extdIzq'],
                    'extdCen'    => $item['extdCen'],
                    'extdDer'    => $item['extdDer'],
                    'extdEst'    => 'A',
                    'extdHorIni' => $item['extdHorIni'],
                    'extdHorFin' => $item['extdHorFin'],
                    'extdUso'    => $extUsu,
                    'extdRol'    => $rol,
                    'extdObs'    => $item['extdObs'],
                    'extdTip'   => $extdTip
                ]);
            }
        }

        if (isset($affected)) {
            $resources = array(
                array(
                    "error" => "0", 'mensaje' => "Extrusión generada manera correcta",
                    'type' => 'success'

                )
            );
            return response()->json($resources, 200);
        } else {
            return response()->json('error', 204);
        }
    }


    function confExtru(Request $request)
    {

        $data = $request->all();
        $rol  = $data['idRol'];
        $name = $data['name'];

        if ($rol == 1 || $rol == 2) {

            $affected = Extrusion::where('idExt', $data['id'])->update([
                'extEstCtl' => 'A',
                'extObs'    => $data['extObs']
            ]);
            if ($affected > 0) {
                //Disparo job de notificacion
                $job = new NotificacionesJob($data['lote_salida'], 4, 'A', $name);
                dispatch($job);
                $resources = array(
                    array(
                        "error" => "0", 'mensaje' => "Extrusión autorizada de manera correcta",
                        'type' => 'success'
                    )
                );
                return response()->json($resources, 200);
            } else {
                return response()->json('error', 204);
            }
        } else {
            $resources = array(
                array(
                    "error" => "1", 'mensaje' => "No posee privilegio",
                    'type' => 'danger'
                )
            );
            return response()->json($resources, 200);
        }
    }

    function confExtruO(Request $request)
    {
        $data = $request->all();
        $rol  = $data['idRol'];
        $name = $data['name'];

        if ($rol == 1 || $rol == 2) {
            $affected = Extrusion::where('idExt', $data['id'])->update([
                'extKilApr' => $data['extKilApr'],
                'extKilR'   => $data['extKilR'],
                'extObs'    => $data['extObs']
            ]);

            if ($affected > 0) {
                $resources = array(
                    array(
                        "error" => "0", 'mensaje' => "Extrusión actualizada de manera correcta",
                        'type' => 'success'
                    )
                );
                return response()->json($resources, 200);
            } else {
                return response()->json('error', 204);
            }
        } else {
            $resources = array(
                array(
                    "error" => "1", 'mensaje' => "No posee privilegio",
                    'type' => 'danger'
                )
            );
            return response()->json($resources, 200);
        }
    }


    public function filLotSal(Request $request)
    {

        $data   = request()->all();
        $resources = viewExtrusion::select('*')->where('lote_bobina', 'like', $data['lote_salida'] . '%')->get()->take(10);

        if (isset($resources)) {
            return response()->json($resources, 200);
        } else {
            $resources = array(
                array(
                    "error" => "0", 'mensaje' => "No se encuentra coincidencia",
                    'type' => 'success'
                )
            );
            return response()->json($resources, 200);
        }
    }

    public function indexFil(Request $request)
    {

        $data         = request()->all();
        $extrusion    = Extrusion::select('*')->where('idExt', $data['idExt'])->get()->take(1);
        $extrusionDet = ExtrusionDet::select('extdCen', 'extdDer', 'extdEst', 'extdHorFin', 'extdHorIni', 'extdIzq', 'extdObs')->where('idExt', $data['idExt'])->get();

        $resources    = array(
            "extrusion" => $extrusion,
            'extrusionDet' => $extrusionDet
        );


        if (isset($resources)) {
            return response()->json($resources, 200);
        } else {
            $resources = array(
                array(
                    "error" => "0", 'mensaje' => "No se encuentra coincidencia",
                    'type' => 'success'
                )
            );
            return response()->json($resources, 200);
        }
    }

    function extDet(Request $request)
    {

        $data = $request->all();
        $idExt = $data['idExt'];

        return ExtrusionDet::select('*')->where('idExt', $idExt)->get();
    }

    public function insConfirmaO(Request $request)
    {
        $data = $request->all();
        $rol  = $data['idRol'];
        $name = $data['name'];
        $extUsu    = $name;

        foreach ($data as $item) {
            $idExt     =  $item['idExt'];
            $extAnbob  =  $item['extAnbob'];
            $extFor    =  $item['extFor'];
            $extDet    =  $item['extrusionDet'];
            $idEta     =  $item['idEta'];
            $producto  =  $item['producto'];
        }

        $extIdPrd  = 0;
        $extPrdCod = '';
        $extPrdDes = '';

        try {
            if (sizeof($producto) > 0) {

                $extIdPrd  = $producto['idPrd'];
                $extPrdCod = $producto['prdCod'];
                $extPrdDes = $producto['prdDes'];
            }
        } catch (Error $error) {
            $extIdPrd  = 0;
            $extPrdCod = '';
            $extPrdDes = '';
        }
        $affected = Extrusion::where('idExt', $idExt)
            ->update([
                'extEst'   => 'A',
                'extAnbob' => $extAnbob,
                'extFor'   => $extFor,
                'extidEta' => $idEta,
                'extIdPrd' => $extIdPrd,
                'extPrdCod' => $extPrdCod,
                'extPrdDes' => $extPrdDes
            ]);

        if ($affected > 0) {

            $affected = ExtrusionDet::where('idExt', $idExt)->delete();

            if ($rol <> 4) {
                $extdTip = 'J';
            } else {
                $extdTip = 'O';
            }

            foreach ($extDet as $item) {
                ExtrusionDet::create([
                    'idExt'      => $idExt,
                    'empId'      => 1,
                    'extdIzq'    => $item['extdIzq'],
                    'extdCen'    => $item['extdCen'],
                    'extdDer'    => $item['extdDer'],
                    'extdEst'    => 'A',
                    'extdHorIni' => $item['extdHorIni'],
                    'extdHorFin' => $item['extdHorFin'],
                    'extdUso'    => $extUsu,
                    'extdRol'    => $rol,
                    'extdObs'    => $item['extdObs'],
                    'extdTip'    =>  $extdTip
                ]);
            }
        }

        if (isset($affected)) {
            $resources = array(
                array(
                    "error" => "0", 'mensaje' => "Extrusión generada manera correcta",
                    'type' => 'success'

                )
            );
            return response()->json($resources, 200);
        } else {
            return response()->json('error', 204);
        }
    }
    public function insConfirmaC(Request $request)
    {

        $data = $request->all();
        foreach ($data as $item) {
            $idExt     =  $item['idExt'];
            $extDet    =  $item['extrusionDet'];
            $rol        = $item['idRol'];
            $name       = $item['name'];
        }
        $extUsu    = $name;

        $affected = ExtrusionDet::where('idExt', $idExt)->delete();

        if ($rol <> 4) {
            $extdTip = 'J';
        } else {
            $extdTip = 'O';
        }

        foreach ($extDet as $item) {
            ExtrusionDet::create([
                'idExt'      => $idExt,
                'empId'      => 1,
                'extdIzq'    => $item['extdIzq'],
                'extdCen'    => $item['extdCen'],
                'extdDer'    => $item['extdDer'],
                'extdEst'    => 'A',
                'extdHorIni' => $item['extdHorIni'],
                'extdHorFin' => $item['extdHorFin'],
                'extdUso'    => $extUsu,
                'extdRol'    => $rol,
                'extdObs'    => $item['extdObs'],
                'extdTip'    =>  $extdTip
            ]);
        }

        if (isset($affected)) {
            $resources = array(
                array(
                    "error" => "0", 'mensaje' => "Extrusión generada manera correcta",
                    'type' => 'success'

                )
            );
            return response()->json($resources, 200);
        }
    }

    public function rechaExtru(Request $request)
    {

        $data = $request->all();
        $rol  = $data['idRol'];
        $name = $data['name'];

        if ($rol == 1 || $rol == 2) {

            //Disparo job de notificacion
            $job = new NotificacionesJob($data['lote_salida'], 4, 'R', $name);
            dispatch($job);
            $affected = Extrusion::where('idExt', $request->id)->update([
                'extEstCtl' => 'R',
                'extObs'    => $data['extObs'],
                'extIdMot'  => $data['idMot']
            ]);
            if ($affected > 0) {
                $resources = array(
                    array(
                        "error" => "0", 'mensaje' => "Extrusión rechazada de manera correcta",
                        'type' => 'success'
                    )
                );
                return response()->json($resources, 200);
            } else {
                return response()->json('error', 204);
            }
        } else {
            $resources = array(
                array(
                    "error" => "1", 'mensaje' => "No posee privilegio",
                    'type' => 'danger'
                )
            );
            return response()->json($resources, 200);
        }
    }

    public function extruDis(Request $request)
    {
        $data = $request->all();
        $affected = Extrusion::select('*')
            ->where('extEstCtl', 'A')
            ->where('extidEta', $data['idEta'])
            ->take(3000)
            ->get();

        return response()->json($affected, 200);
    }
}
