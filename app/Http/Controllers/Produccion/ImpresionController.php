<?php

namespace App\Http\Controllers;

use App\Jobs\NotificacionesJob;
use App\Jobs\OrdenTrabajoCantProdJob;
use App\Models\Impresion;
use App\Models\ImpresionDet;
use App\Models\ImpresionPeso;
use App\Models\ImpresionTintas;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ImpresionController extends Controller
{
    public function index(Request $request)
    {

        return Impresion::all()->take(3000);
    }

    public function indexfil(Request $request)
    {

        $impLotSal = $request->impLotSal;
        if (strlen($impLotSal) > 0) {
            $impresion = Impresion::select('*')->where('impLotSal', $impLotSal)->get();
            $idImp = 0;
            foreach ($impresion as $xidImp) {
                $idImp = $xidImp->idImp;
            }
        } else {
            $idImp = $request->idImp;
            $impresion = Impresion::select('*')->where('idImp', $idImp)->get();
        }
        $impresionDet = ImpresionDet::select(
            'idImpd',
            'impresion_det.empId',
            'impdUso',
            'impdRol',
            'impdHorIni',
            'impdPesoCaja',
            'impdCajaAcu',
            'impdDefecto',
            'impdidMot',
            DB::raw('motDes as motivo'),
            'impdTip',
            'impdEst'
        )
            ->join('mot_rechazo', 'mot_rechazo.idMot', '=', 'impresion_det.impdidMot')
            ->where('idImp', $idImp)
            ->where('impdTip', 'O')
            ->get();

        $impPeso = ImpresionPeso::select('*')->where('idImp', $idImp)->get();
        $impTinta = ImpresionTintas::select(
            DB::raw('imptPrd as idPrd'),
            DB::raw('prdCod as imptPrdCod'),
            DB::raw('prdDes as imptPrdDes'),
            'imptPrdLote'
        )
            ->join('producto', 'producto.idPrd', '=', 'impresion_tinta.imptPrd')
            ->where('idImp', $idImp)->get();

        $impresion = array(
            'impresion'    => $impresion,
            'impresionDet' => $impresionDet,
            'impPeso'      => $impPeso,
            'impTinta'     => $impTinta
        );

        return response()->json($impresion, 200);
    }

    public function ins(Request $request)
    {
        $data      = $request->all();
        foreach ($data as $item) {
            $impMaq        = $item['impMaq'];
            $impPrdCaja    = $item['impPrdCaja'];
            $impPrdBolsa   = $item['impPrdBolsa'];
            $impLotCaja    = $item['impLotCaja'];
            $impLotBolsa   = $item['impLotBolsa'];
            $impTurn       = $item['impTurn'];
            $impDia        = $item['impDia'];
            $idOt          = $item['idOt'];
            $extMez        = $item['extMez'];
            $idImp         = $item['idImp'];
            $inyUso        = $item['name'];
        }

        $fecha    = Carbon::now()->format('Y-m-d');
        $impTip   = 'P';

        if ($impTip == 'P') {
            $count    = Impresion::select("*")
                ->where('impTurn', $impTurn)
                ->where('impMaq', $impMaq)
                ->where('idImp', '<>',  $idImp)
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
            $impLotSal  = $impMaq . '0' . $impTurn . $impDia . $digito;
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

        $affected = Impresion::where('idImp', $idImp)
            ->update([
                'impUso'     => $inyUso,
                'impEst'     => 'P',
                'impEstCtl'  => 'P',
                'impMaq'     => $impMaq,
                'impTip'     => $impTip,
                'impPrdCaja' => $impPrdCaja,
                'impPrdBolsa' => $impPrdBolsa,
                'impLotCaja' => $impLotCaja,
                'impLotBolsa' => $impLotBolsa,
                'impTurn'    => $impTurn,
                'impLotSal'  => $impLotSal,
                'idOrdt'     => $idOt,
                'impidTer'   => $extMez
            ]);

        if (isset($affected)) {
            $resources = array(
                array(
                    "error" => "0", 'mensaje' => "impresión generada manera correcta",
                    'type' => 'success',
                    'data' => array(
                        array(
                            'impLotSal' => $impLotSal,
                            'idImp'   => $idImp
                        )
                    )
                )
            );
            return response()->json($resources, 200);
        } else {
            return response()->json('error', 204);
        }
    }
    public function insImpCierreC(Request $request)
    {

        $data      = $request->all();
        foreach ($data as $item) {
            $idImp          = $item['id'];
            $impPeso        = $item['impPeso'];
            $name           = $item['name'];
            $rol            = $item['idRol'];
        }

        ImpresionPeso::where('idImp', $idImp)->delete();

        if (sizeof($impPeso) > 0) {
            foreach ($impPeso as $peso) {
                ImpresionPeso::create([
                    'empId'     => 1,
                    'idImp'     => $idImp,
                    'imppUso'   => $name,
                    'imppRol'   => $rol,
                    'impptip'   => $peso['imppTip'],
                    'impPeso'  => $peso['impPeso']
                ]);
            }
        }

        $resources = array(
            array(
                "error" => "0", 'mensaje' => "Impresión generado manera correcta",
                'type' => 'success'
            )
        );
        return response()->json($resources, 200);
    }



    public function insImpCierre(Request $request)
    {

        $data      = $request->all();


        foreach ($data as $item) {
            $idImp          = $item['id'];
            $impTinta       = $item['impTinta'];
            $impReproceso   = $item['impReproceso'];
            $impBasura      = $item['impBasura'];
            $impMerma       = $item['impMerma'];
            $impDet         = $item['impDet'];
            $name           = $item['name'];
            $rol            = $item['idRol'];
        }



        ImpresionTintas::where('idImp', $idImp)->delete();

        if (sizeof($impTinta) > 0) {
            foreach ($impTinta as $tinta) {
                ImpresionTintas::create([
                    'empId'       => 1,
                    'idImp'       => $idImp,
                    'imptPrd'     => $tinta['idPrd'],
                    'imptPrdLote' => $tinta['imptPrdLote']
                ]);
            }
        }

        if (sizeof($impDet) > 0) {
            ImpresionDet::where('idImp', $idImp)
                ->where('impdTip', 'O')
                ->delete();

            foreach ($impDet as $det) {
                ImpresionDet::create([
                    'idImp'           => $idImp,
                    'empId'           => 1,
                    'impdUso'         => $name,
                    'impdRol'         => $rol,
                    'impdHorIni'      => $det['impdHorIni'],
                    'impdPesoCaja'    => $det['impdPesoCaja'],
                    'impdDefecto'     => $det['impdDefecto'],
                    'impdidMot'       => $det['impdidMot'],
                    'impdTip'         => 'O',
                    'impdEst'         => 'A',
                    'impdCajaAcu'     => $det['impdCajaAcu']
                ]);
            }
        }

        $affected =   Impresion::where('idImp', $idImp)
            ->update([
                'impReproceso'  => $impReproceso,
                'impBasura'     => $impBasura,
                'impMerma'      => $impMerma,
                'impEst'        => 'A'
            ]);
        $resources = array(
            array(
                "error" => "0", 'mensaje' => "Impresión generado manera correcta",
                'type' => 'success'
            )
        );
        return response()->json($resources, 200);
    }




    public function ImpConf(Request $request)
    {

        $data      = $request->all();

        foreach ($data as $item) {
            $lote_salida          = $item['lote_salida'];
            $idOrdt               = $item['idOrdt'];
            $name                 = $item['name'];
        }

        $impresion = Impresion::select('idImp')->where('impLotSal', $lote_salida)->get();

        foreach ($impresion as $item) {
            $idImp = $item['idImp'];
        }

        if ($idImp > 0) {
            $affect = Impresion::where('idImp', $idImp)->update([
                'impEst'   => 'A',
                'impEstCtl' => 'A'
            ]);

            $job = new NotificacionesJob($lote_salida, 8, 'A', $name);
            dispatch($job);

            $job = new OrdenTrabajoCantProdJob($idImp, 8);
            dispatch($job);

            $resources = array(
                array(
                    "error" => "0", 'mensaje' => "Impresión Autorizado",
                    'type' => 'success'
                )
            );
            return $resources;
        } else {
            $resources = array(
                array(
                    "error" => "1", 'mensaje' => "Error en autprizar",
                    'type' => 'danger'
                )
            );
            return $resources;
        }
    }


    public function impRechazo(Request $request)
    {


        $data      = $request->all();
        foreach ($data as $item) {
            $lote_salida          = $item['lote_salida'];
        }

        $impresion = Impresion::select('idImp')->where('impLotSal', $lote_salida)->get();

        foreach ($impresion as $item) {
            $idImp = $item['idImp'];
            $name  = $item['name'];
        }


        if ($idImp > 0) {
            $affect = Impresion::where('idImp', $idImp)->update([
                'impEst'    => 'R',
                'impEstCtl' => 'R'
            ]);

            $job = new NotificacionesJob($lote_salida, 8, 'R', $name);
            dispatch($job);

            $resources = array(
                array(
                    "error" => "0", 'mensaje' => "Impresión Rechazado",
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
