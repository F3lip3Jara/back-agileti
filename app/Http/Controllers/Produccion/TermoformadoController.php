<?php

namespace App\Http\Controllers;

use App\Jobs\NotificacionesJob;
use App\Jobs\OrdenTrabajoCantProdJob;
use App\Models\BinCol;
use App\Models\EquivalenciaPrd;
use App\Models\Producto;
use App\Models\User;
use App\Models\Termoformado;
use App\Models\TermoformadoArch;
use App\Models\TermoformadoDet;
use App\Models\TermoformadoPallet;
use App\Models\TermoformadoPeso;
use App\Models\viewOrdenTrabajoTermo;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

use function GuzzleHttp\Promise\each;

class TermoformadoController extends Controller
{
    public function index(Request $request)
    {

        return Termoformado::all()->take(3000);
    }

    public function indexfil(Request $request)
    {

        $terLotSal = $request->terLotSal;

        if (strlen($terLotSal) > 0) {
            $termo =  Termoformado::select('*')->where('terLotSal', $terLotSal)->get();
            $idTer = 0;
            foreach ($termo as $xidTer) {
                $idTer = $xidTer->idTer;
            }
        } else {
            $idTer = $request->idTer;
            $termo =  Termoformado::select('*')->where('idTer', $idTer)->get();
        }

        $termoDet = TermoformadoDet::select(
            'idTer',
            'idTerd',
            'terdEst',
            'terdHorIni',
            'terdHorFin',
            'terdUso',
            'terdRol',
            'terdLotExt',
            DB::raw('extAnbob as ancho'),
            DB::raw('extKilApr as peso'),
            DB::raw('extFor as espesor'),
            'terdRechazo',
            'terdLimp',
            'terdTem',
            'terdCaja',
            'terdTipo',
            'terdPesoUn'
        )
            ->join('extrusion', 'extrusion.extLotSal', '=', 'termoformado_det.terdLotExt')
            ->where('idTer', $idTer)
            ->where('terdTipo', 'O')
            ->get();

        $termoDetC = TermoformadoDet::select(
            'idTerd',
            'idTer',
            'terdEst',
            'terdHorIni',
            'terdUso',
            'terdRol',
            'terdDefecto',
            'terdidMot',
            'terdRechazo',
            'terdSani',
            'terdPesoUn'
        )
            ->where('idTer', $idTer)
            ->where('terdTipo', 'C')
            ->get();

        $termoPeso = TermoformadoPeso::select('*')->where('idTer', $idTer)->get();
        $termoArch = TermoformadoArch::select('*')->where('idTer', $idTer)->get();
        $termoformado = array(
            'termoformado' => $termo,
            'termoDet'    => $termoDet,
            'termoDetC'   => $termoDetC,
            'termoPeso'   => $termoPeso,
            'termoArch'   => $termoArch
        );

        return response()->json($termoformado, 200);
    }


    public function ins(Request $request)
    {
        $data      = $request->all();

        foreach ($data as $item) {
            $terMaq        = $item['terMaq'];
            $terTip        = $item['terTip'];
            $terPrdCaja    = $item['terPrdCaja'];
            $terPrdBolsa   = $item['terPrdBolsa'];
            $terLotCaja    = $item['terLotCaja'];
            $terLotBolsa   = $item['terLotBolsa'];
            $terTurn       = $item['terTurn'];
            $terDia        = $item['terDia'];
            $idOt          = $item['idOt'];
            $idTer         = $item['idTer'];
            $terUso        = $item['name'];
        }

        $fecha    = Carbon::now()->format('Y-m-d');

        if ($terTip == 'P') {
            $count    = Termoformado::select("*")
                ->where('terTurn', $terTurn)
                ->where('terMaq', $terMaq)
                ->where('idTer', '<>', $idTer)
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
            $terLotSal  = $terMaq . '0' . $terTurn . $terDia . $digito;
        } else {
            $cor = BinCol::select('colbnum')
                ->where('idEta', 5)
                ->where('colbtip', $terTip)->get();

            foreach ($cor as $xcor) {
                $correlativo =   $xcor->colbnum  + 1;
                $terLotSal   = strval($correlativo);

                BinCol::where('idEta', 5)
                    ->where('colbtip', $terTip)
                    ->update([
                        'colbnum'      => $correlativo

                    ]);
            }
        }

        $affected = Termoformado::where('idTer', $idTer)
            ->update([
                'terEst'     => 'P',
                'terEstCtl'  => 'P',
                'terMaq'     => $terMaq,
                'terTip'     => $terTip,
                'terPrdCaja' => $terPrdCaja,
                'terPrdBolsa' => $terPrdBolsa,
                'terLotCaja' => $terLotCaja,
                'terLotBolsa' => $terLotBolsa,
                'terTurn'    => $terTurn,
                'terLotSal'  => $terLotSal

            ]);

        if (isset($affected)) {
            $resources = array(
                array(
                    "error" => "0", 'mensaje' => "Termoformado generada manera correcta",
                    'type' => 'success',
                    'data' => array(
                        array(
                            'terLotSal' => $terLotSal,
                            'idTer'   => $idTer
                        )
                    )
                )
            );
            return response()->json($resources, 200);
        } else {
            return response()->json('error', 204);
        }
    }

    public function insTermCierre(Request $request)
    {

        $data = $request->all();

        foreach ($data as $item) {
            $idTer          = $item['id'];
            $termoDet       = $item['termoDet'];
            $termoPeso      = $item['termoPeso'];
            $terCavTot      = $item['terCavTot'];
            $terCavAct      = $item['terCavAct'];
            $terMerma       = $item['terMerma'];
            $terRepro       = $item['terRepro'];
            $name           = $item['name'];
            $rol            = $item['idRol'];
        }

        TermoformadoPeso::where('idTer', $idTer)->delete();
        if (sizeof($termoPeso) > 0) {
            foreach ($termoPeso as $peso) {
                TermoformadoPeso::create([
                    'idTer'    => $idTer,
                    'empId'    => 1,
                    'terpUso'  => $name,
                    'terpRol'  => $rol,
                    'terpPeso' => $peso['terpPeso'],
                    'terptip'  => $peso['terptip']
                ]);
            }
        }

        $affected =   Termoformado::where('idTer', $idTer)
            ->update([
                'terCavAct'  => $terCavAct,
                'terCavTot'  => $terCavTot,
                'terMerma'   => $terMerma,
                'terRepro'   => $terRepro
            ]);

        $resources = array(
            array(
                "error" => "0", 'mensaje' => "Termoformado generado manera correcta",
                'type' => 'success'
            )
        );
        return response()->json($resources, 200);
    }



    public function insTermCierreC(Request $request)
    {
        $id     = 0;
        $header = $request->header('access-token');
        $val    = User::select('token', 'id', 'activado', 'name', 'idRol')->where('token', $header)->get();
        if ($header == '') {
            return response()->json('error', 203);
        } else {

            foreach ($val as $item) {
                $id   = $item->id;
                $name = $item->name;
                $rol  = $item->idRol;
            }

            if ($id > 0) {

                $data = $request->all();
                $terUso    = $name;

                foreach ($data as $item) {
                    $idTer          = $item['id'];
                    $termoDet       = $item['termoDetC'];
                    $termoPeso      = $item['termoPeso'];
                    $terObs         = $item['terObs'];
                }
                TermoformadoPeso::where('idTer', $idTer)->delete();
                if (sizeof($termoPeso) > 0) {
                    foreach ($termoPeso as $peso) {
                        TermoformadoPeso::create([
                            'idTer'    => $idTer,
                            'empId'    => 1,
                            'terpUso'  => $name,
                            'terpRol'  => $rol,
                            'terpPeso' => $peso['terpPeso'],
                            'terptip'  => $peso['terptip']
                        ]);
                    }
                }


                TermoformadoDet::where('idTer', $idTer)
                    ->where('terdTipo', 'C')
                    ->delete();

                if (sizeof($termoDet) > 0) {

                    foreach ($termoDet as $terDet) {

                        TermoformadoDet::create([
                            'idTer'      => $idTer,
                            'empId'      => 1,
                            'terdEst'    => 'A',
                            'terdHorIni' => $terDet['terdHorIni'],
                            'terdHorFin' => '',
                            'terdUso'    => $name,
                            'terdRol'    => $rol,
                            'terdTipo'   => 'C',
                            'terdDefecto' => $terDet['terdDefecto'],
                            'terdidMot'  => $terDet['terdidMot'],
                            'terdPesoUn' => $terDet['terdPesoUn'],
                            'terdRechazo' => $terDet['terdRechazo'],
                            'terdSani'   => $terDet['terdSani'],
                            'terdLotExt' => ''
                        ]);
                    }
                }
                $affected =   Termoformado::where('idTer', $idTer)
                    ->update([
                        'terObs'  => $terObs
                    ]);
                if (isset($affected)) {
                    $resources = array(
                        array(
                            "error" => "0", 'mensaje' => "Termoformado generada manera correcta",
                            'type' => 'success'
                        )
                    );
                    return response()->json($resources, 200);
                } else {
                    return response()->json('error', 204);
                }
            } else {
                return response()->json('error', 203);
            }
        }
    }

    public function insTermArcv(Request $request)
    {
        $data = $request->all();
        $archivo64 = $data['base64'];
        $idTer     = $data['idTer'];
        $archivophp = explode(',', $archivo64);
        $darchivo64 = base64_decode($archivophp[1]);
        $archivonom = $data['nombre'];
        // $filepath = '..\storage\app\public\calidad_archivos\ '. $archivonom;                  
        $valTermo   = TermoformadoArch::select('idTer')
            ->where('idTer', $idTer)
            ->where('terarlink', $archivonom)
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
            Storage::put('calidad_archivos/termoformado/' . $archivonom, $darchivo64);
            $affected = TermoformadoArch::create([
                'idTer'     => $idTer,
                'empId'     => 1,
                'terarlink' => $archivonom
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
            $idTer   = $item['idTer'];
            $archivo = $item['archivo'];
        }
        $nombre = $archivo['nombre'];

        $valida = TermoformadoArch::all()
            ->where('idTer', $idTer)
            ->where('terarlink', $nombre)->take(1);
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
            $affected = TermoformadoArch::where('idTer', $idTer)
                ->where('terarlink', $nombre)
                ->delete();

            if ($affected > 0) {
                Storage::disk('public')->delete('calidad_archivos/termoformado/' . $nombre);
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

    public function downloadFileTerm(Request $request)
    {
        $archivo = $request->all();
        $url = Storage::url($archivo['nombre']);
        return $url;
    }


    public function insTermDet(Request $request)
    {
        $id     = 0;
        $header = $request->header('access-token');
        $val    = User::select('token', 'id', 'activado', 'name', 'idRol')->where('token', $header)->get();

        if ($header == '') {
            return response()->json('error', 203);
        } else {
            foreach ($val as $item) {
                $id   = $item->id;
                $name = $item->name;
                $rol  = $item->idRol;
            }

            if ($id > 0) {
                $data = $request->all();

                foreach ($data as $item) {
                    $idTer          = $item['id'];
                    $termoDet       = $item['termoDet'];
                    $terTip         = $item['terTip'];
                    $prdcod         = $item['prdCod'];
                    $terLotSal      = $item['terLotSal'];
                }

                if (sizeof($termoDet) > 0) {

                    if ($terTip == 'P') {

                        $producto = Producto::select('idPrd')->where('prdDes', $prdcod)->get();
                        foreach ($producto as $prd) {
                            $idPrd = $prd['idPrd'];
                        }

                        $equivalencia = EquivalenciaPrd::select('equiBultPallet')->where('idPrd', $idPrd)->get();


                        foreach ($equivalencia as $equi) {
                            $equiPallet = $equi['equiBultPallet'];
                        }
                        $terdHorIni = $termoDet['terdHorIni'];
                        $terdLotExt = $termoDet['terdLotExt'];
                        $terdTem    = $termoDet['terdTem'];
                        $terdPesoUn = $termoDet['terdPesoUn'];
                        $terdTipo   = $termoDet['terdTipo'];
                        $terdCaja    = $termoDet['terdCaja'];
                        $terdEst     = $termoDet['terdEst'];
                        $total       = 0;
                        $total       = round($total);



                        $termo  = TermoformadoDet::create([
                            'idTer'     => $idTer,
                            'empId'     => 1,
                            'terdEst'   => 'A',
                            'terdHorIni' => $terdHorIni,
                            'terdHorFin' => '',
                            'terdUso'   => $name,
                            'terdRol'   => $rol,
                            'terdLotExt' => $terdLotExt,
                            'terdTem'   => $terdTem,
                            'terdPesoUn' => $terdPesoUn,
                            'terdTipo'  => $terdTipo,
                            'terdCaja'  => $terdCaja
                        ]);


                        if ($equiPallet > 0) {

                            $total       = $terdCaja / $equiPallet;

                            if ($total = 0) {
                                $total = 1;
                            }

                            for ($i = 1; $i <= $total; $i++) {

                                $correlativo = BinCol::select('colbnum')->where('idEta', 5)->where('colbtip', $terTip)->get();

                                foreach ($correlativo as $cor) {
                                    $corre = $cor['colbnum'] + 1;

                                    BinCol::where('idEta', 5)
                                        ->where('colbtip', $terTip)
                                        ->update(['colbnum' => $corre]);

                                    $affected = TermoformadoPallet::create([
                                        'idTer' => $idTer,
                                        'empId'  => 1,
                                        'idTerd' => $termo->id,
                                        'terpaCor' => $corre

                                    ]);

                                    if (isset($affected)) {
                                        $resources = array(
                                            array(
                                                "error" => "0", 'mensaje' => "Detalle ingresado de manera correcta",
                                                'type' => 'success'
                                            )
                                        );
                                    }
                                }
                            }
                        }
                    } else {
                        $terdHorIni = $termoDet['terdHorIni'];
                        $terdLotExt = $termoDet['terdLotExt'];
                        $terdTem    = $termoDet['terdTem'];
                        $terdPesoUn = $termoDet['terdPesoUn'];
                        $terdTipo   = $termoDet['terdTipo'];
                        $terdCaja   = $termoDet['terdCaja'];


                        $termo  = TermoformadoDet::create([
                            'idTer'     => $idTer,
                            'empId'     => 1,
                            'terdEst'   => 'A',
                            'terdHorIni' => $terdHorIni,
                            'terdHorFin' => '',
                            'terdUso'   => $name,
                            'terdRol'   => $rol,
                            'terdLotExt' => $terdLotExt,
                            'terdTem'   => $terdTem,
                            'terdPesoUn' => $terdPesoUn,
                            'terdTipo'  => $terdTipo,
                            'terdCaja'  => $terdCaja
                        ]);


                        $affected = TermoformadoPallet::create([
                            'idTer'   => $idTer,
                            'empId'   => 1,
                            'idTerd'  => $termo->id,
                            'terpaCor' => $terLotSal
                        ]);

                        if (isset($affected)) {
                            $resources = array(
                                array(
                                    "error" => "0", 'mensaje' => "Detalle ingresado de manera correcta",
                                    'type' => 'success'
                                )
                            );

                            return $resources;
                        }
                    }
                }
            } else {
                return response()->json('error', 203);
            }
        }
    }
    public function delTermDes(Request $request)
    {
        $id     = 0;
        $header = $request->header('access-token');
        $val    = User::select('token', 'id', 'activado', 'name', 'idRol')->where('token', $header)->get();

        if ($header == '') {
            return response()->json('error', 203);
        } else {
            foreach ($val as $item) {
                $id   = $item->id;
                $name = $item->name;
                $rol  = $item->idRol;
            }

            if ($id > 0) {
                $data      = $request->all();

                foreach ($data as $item) {
                    $idTer          = $item['id'];
                    $termoDet       = $item['termoDet'];
                }

                if (sizeof($termoDet) > 0) {

                    $ter = TermoformadoDet::select('idTerd')
                        ->where('idTer', $idTer)
                        ->where('terdHorIni', $termoDet['terdHorIni'])
                        ->where('terdLotExt', $termoDet['terdLotExt'])
                        ->get();

                    foreach ($ter as $item) {
                        $idTerd = $item['idTerd'];
                    }

                    TermoformadoPallet::where('idTer', $idTer)
                        ->where('idTerd', $idTerd)
                        ->delete();


                    $affect = TermoformadoDet::where('idTer', $idTer)
                        ->where('idTerd', $idTerd)
                        ->delete();

                    $resources = array(
                        array(
                            "error" => "0", 'mensaje' => "Detalle eliminado de manera correcta",
                            'type' => 'success'
                        )
                    );

                    return $resources;
                } else {
                    $resources = array(
                        array(
                            "error" => "1", 'mensaje' => "Error en eliminar",
                            'type' => 'danger'
                        )
                    );

                    return $resources;
                }
            }
        }
    }

    public function termPallet(Request $request)
    {
        $id     = 0;
        $header = $request->header('access-token');
        $val    = User::select('token', 'id', 'activado', 'name', 'idRol')->where('token', $header)->get();

        if ($header == '') {
            return response()->json('error', 203);
        } else {
            foreach ($val as $item) {
                $id   = $item->id;
                $name = $item->name;
                $rol  = $item->idRol;
            }

            if ($id > 0) {
                $data      = $request->all();

                foreach ($data as $item) {
                    $idTer          = $item['id'];
                    $termoDet       = $item['termoDet'];
                }

                if (sizeof($termoDet) > 0) {
                    $ter = TermoformadoDet::select('idTerd')
                        ->where('idTer', $idTer)
                        ->where('terdHorIni', $termoDet['terdHorIni'])
                        ->where('terdLotExt', $termoDet['terdLotExt'])
                        ->get();

                    foreach ($ter as $item) {
                        $idTerd = $item['idTerd'];
                    }

                    return TermoformadoPallet::select('*')->where('idTer', $idTer)
                        ->where('idTerd', $idTerd)
                        ->get();
                } else {
                    $resources = array(
                        array(
                            "error" => "1", 'mensaje' => "Error en eliminar",
                            'type' => 'danger'
                        )
                    );

                    return $resources;
                }
            }
        }
    }


    public function termConf(Request $request)
    {
        $id     = 0;
        $header = $request->header('access-token');
        $val    = User::select('token', 'id', 'activado', 'name', 'idRol')->where('token', $header)->get();

        if ($header == '') {
            return response()->json('error', 203);
        } else {
            foreach ($val as $item) {
                $id   = $item->id;
                $name = $item->name;
                $rol  = $item->idRol;
            }

            if ($id > 0) {
                $data      = $request->all();

                foreach ($data as $item) {
                    $lote_salida          = $item['lote_salida'];
                    $idOrdt               = $item['idOrdt'];
                }

                $termo = Termoformado::select('idTer', 'terTip')->where('terLotSal', $lote_salida)->get();

                foreach ($termo as $item) {
                    $idTer  = $item['idTer'];
                    $terTip = $item['terTip'];
                }

                if ($idTer > 0) {

                    $affect = Termoformado::where('idTer', $idTer)->update([
                        'terEst' => 'A',
                        'terEstCtl' => 'A'
                    ]);

                    $job = new NotificacionesJob($lote_salida, 5, 'A', $name);
                    dispatch($job);

                    if ($terTip == 'P') {
                        $job = new OrdenTrabajoCantProdJob($idTer, 5);
                        dispatch($job);
                    }

                    $resources = array(
                        array(
                            "error" => "0", 'mensaje' => "Termoformado Autorizado",
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
        }
    }


    public function termRechazo(Request $request)
    {
        $id     = 0;
        $header = $request->header('access-token');
        $val    = User::select('token', 'id', 'activado', 'name', 'idRol')->where('token', $header)->get();

        if ($header == '') {
            return response()->json('error', 203);
        } else {
            foreach ($val as $item) {
                $id   = $item->id;
                $name = $item->name;
                $rol  = $item->idRol;
            }

            if ($id > 0) {
                $data      = $request->all();
                foreach ($data as $item) {
                    $lote_salida          = $item['lote_salida'];
                }

                $termo = Termoformado::select('idTer')->where('terLotSal', $lote_salida)->get();

                foreach ($termo as $item) {
                    $idTer = $item['idTer'];
                }


                if ($idTer > 0) {
                    $affect = Termoformado::where('idTer', $idTer)->update([
                        'terEst' => 'R',
                        'terEstCtl' => 'R'
                    ]);
                    $job = new NotificacionesJob($lote_salida, 5, 'R', $name);
                    dispatch($job);

                    $resources = array(
                        array(
                            "error" => "0", 'mensaje' => "Termoformado Rechazado",
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
    }

    public function getBins(Request $request)
    {
        $resources = viewOrdenTrabajoTermo::select('id_termo', 'producto', 'estado_termo_ctl', 'lote_salida')
            ->where('estado_termo_ctl', 'APROBADA')
            ->where('tipo', 'Pallet')
            ->get();

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
}
