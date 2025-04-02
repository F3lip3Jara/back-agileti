<?php

namespace App\Http\Controllers;

use App\Models\Envasado;
use App\Models\EnvasadoArchivo;
use App\Models\EnvasadoDet;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class EnvasadoController extends Controller
{
    public function index(Request $request)
    {

        return Envasado::all()->take(3000);
    }

    public function indexfil(Request $request)
    {

        $envLotSal = $request->envLotSal;
        $envasado =  Envasado::select('*')->where('envLotSal', $envLotSal)->get();
        $idTer = 0;
        return response()->json($envasado, 200);
    }

    public function valEnv(Request $request)
    {

        $idTer    = $request->id;



        $envasado =  Envasado::select('*')->where('idTer', $idTer)->get();
        $idEnv    = 0;
        foreach ($envasado as $item) {
            $idEnv = $item['idEnv'];
        }
        return response()->json($idEnv, 200);
    }


    public function ins(Request $request)
    {

        $data      = $request->all();

        foreach ($data as $item) {
            $idTer    = $item['id_termo'];
        }
        $affected = Envasado::create([
            'empId'      => 1,
            'envTurn'    => 0,
            'envLotSal'  => '',
            'envPrdCaja' => '',
            'envPrdBolsa' => '',
            'envLotCaja' => '',
            'envLotBolsa' => '',
            'envMaq'     => '',
            'envEst'     => 'P',
            'envEstCtl'  => 'P',
            'idTer'     => $idTer
        ]);

        if (isset($affected)) {
            $resources = array(
                array(
                    "error" => "0", 'mensaje' => "Envasado generado manera correcta",
                    'type' => 'success'
                )

            );
            return response()->json($resources, 200);
        } else {
            return response()->json('error', 204);
        }
    }

    public function up(Request $request)
    {
        $data      = $request->all();
        $fecha    = Carbon::now()->format('Y-m-d');
        foreach ($data as $item) {
            $envTurn    = $item['envTurn'];
            $envPrdCaja = $item['envPrdCaja'];
            $envPrdBolsa = $item['envPrdBolsa'];
            $envLotCaja = $item['envLotCaja'];
            $envLotBolsa = $item['envLotBolsa'];
            $envMaq     = $item['envMaq'];
            $envDia     = $item['envDia'];
            $idEnv      = $item['idEnv'];
        }

        $count    = Envasado::select("*")
            ->where('envTurn', $envTurn)
            ->where('envMaq', $envMaq)
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

        $envLotSal  = $envMaq . '0' . $envTurn . $envDia . $digito;

        $affected = Envasado::where('idEnv', $idEnv)
            ->update([
                'envTurn'    => $envTurn,
                'envLotSal'  => $envLotSal,
                'envPrdCaja' => $envPrdCaja,
                'envPrdBolsa' => $envPrdBolsa,
                'envLotCaja' => $envLotCaja,
                'envLotBolsa' => $envLotBolsa,
                'envMaq'     => $envMaq
            ]);

        if (isset($affected)) {
            $resources = array(
                array(
                    "error" => "0", 'mensaje' => "Envasado generado manera correcta",
                    'type' => 'success',
                    'data' => array(
                        array(
                            'envLotSal' => $envLotSal
                        )
                    )
                )
            );
            return response()->json($resources, 200);
        } else {
            return response()->json('error', 204);
        }
    }


    public function insEnvDet(Request $request)
    {

        $data = $request->all();

        foreach ($data as $item) {
            $idEnv = $item['idEnv'];
            $envDet = $item['envDet'];
        }
        EnvasadoDet::where('idEnv', $idEnv)->delete();

        foreach ($envDet as $det) {

            $affected = EnvasadoDet::create([
                'idEnv'      => $idEnv,
                'empId'      => 1,
                'envdHorIni' => $det['envdHorIni'],
                'envdHorFin' => $det['envdHorFin'],
                'envdCaja'   => $det['envdCaja'],
                'envdPallet' => 0
            ]);
        }

        if (isset($affected)) {

            $affected = Envasado::where('idEnv', $idEnv)
                ->update([
                    'envEst' => 'A'
                ]);

            $resources = array(
                array(
                    "error" => "0", 'mensaje' => "Envasado generado manera correcta",
                    'type' => 'success',

                )
            );
            return response()->json($resources, 200);
        } else {
            return response()->json('error', 204);
        }
    }

    public function envDet(Request $request)
    {

        $idEnv = $request->idEnv;
        $envDet  = EnvasadoDet::select('*')->where('idEnv', $idEnv)->get();
        $envArcv = EnvasadoArchivo::select('*')->where('idEnv', $idEnv)->get();

        $envasado =  array(
            'envDet' => $envDet,
            'envArcv' => $envArcv
        );
        return response()->json($envasado, 200);
    }

    public function envRechazo(Request $request)
    {

        $data = $request->all();

        foreach ($data as $item) {
            $idEnv = $item['idEnv'];
        }
        $affected = Envasado::where('idEnv', $idEnv)
            ->update([
                'envEstCtl' => 'R'
            ]);

        if (isset($affected)) {
            $resources = array(
                array(
                    "error" => "0", 'mensaje' => "Envasado rechazado manera correcta",
                    'type' => 'danger',

                )
            );
            return response()->json($resources, 200);
        } else {
            return response()->json('error', 204);
        }
    }

    public function envConf(Request $request)
    {

        $data = $request->all();

        foreach ($data as $item) {
            $idEnv = $item['idEnv'];
        }
        $affected = Envasado::where('idEnv', $idEnv)
            ->update([
                'envEstCtl' => 'A'
            ]);

        if (isset($affected)) {
            $resources = array(
                array(
                    "error" => "0", 'mensaje' => "Envasado confirmado manera correcta",
                    'type' => 'success',

                )
            );
            return response()->json($resources, 200);
        } else {
            return response()->json('error', 204);
        }
    }

    public function upEnvC(Request $request)
    {

        $data = $request->all();

        foreach ($data as $item) {
            $idEnv = $item['idEnv'];
            $envObs = $item['envObs'];
        }

        $affected = Envasado::where('idEnv', $idEnv)
            ->update([
                'envObs' => $envObs
            ]);

        if (isset($affected)) {
            $resources = array(
                array(
                    "error" => "0", 'mensaje' => "Envasado confirmado manera correcta",
                    'type' => 'success',

                )
            );
            return response()->json($resources, 200);
        } else {
            return response()->json('error', 204);
        }
    }

    public function uploadArEnv(Request $request)
    {
        $data = $request->all();
        $archivo64 = $data['base64'];
        $idEnv     = $data['idEnv'];
        $archivophp = explode(',', $archivo64);
        $darchivo64 = base64_decode($archivophp[1]);
        $archivonom = $data['nombre'];
        // $filepath = '..\storage\app\public\calidad_archivos\ '. $archivonom;                  
        $valTermo   = EnvasadoArchivo::select('idEnv')
            ->where('idEnv', $idEnv)
            ->where('envLink', $archivonom)
            ->get();
        $val = 0;
        foreach ($valTermo as $item) {
            $val = $item->idEnv;
        }
        if ($val > 0) {
            return $resources = array(
                array(
                    "error" => "1", 'mensaje' => "Archivo duplicado",
                    'type' => 'danger'
                )
            );
        } else {
            Storage::put('calidad_archivos/envasado/' . $archivonom, $darchivo64);
            $affected = EnvasadoArchivo::create([
                'idEnv'     => $idEnv,
                'empId'     => 1,
                'envLink'   => $archivonom
            ]);

            if (isset($affected)) {
                $resources = array(
                    array(
                        "error"   => "0", 'mensaje' => "Archivo guardado de manera correcta",
                        'type'   => 'success',
                        "envArc" => $affected
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
            $idEnv   = $item['idEnv'];
            $archivo = $item['archivo'];
        }
        $nombre = $archivo['envLink'];

        $valida = EnvasadoArchivo::all()
            ->where('idEnv', $idEnv)
            ->where('envLink', $nombre)->take(1);


        //si la variable es null o vacia elimino el rol         
        if (sizeof($valida) <= 0) {
            //en el caso que no se ecuentra vacia no puedo eliminar
            $resources = array(
                array(
                    "error" => "1", 'mensaje' => "No existe arhivo",
                    'type' => 'danger'
                )
            );
            return response()->json($resources, 200);
        } else {
            $affected = EnvasadoArchivo::where('idEnv', $idEnv)
                ->where('envLink', $nombre)
                ->delete();

            if ($affected > 0) {
                Storage::disk('public')->delete('calidad_archivos/envasado/' . $nombre);
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
}
