<?php

namespace App\Http\Controllers;

use App\Models\Impresion;
use App\Models\Inyeccion;
use App\Models\OrdenProduccion;
use App\Models\OrdenTrabajo;
use App\Models\OrdTrabDet;
use App\Models\Termoformado;
use App\Models\User;
use App\Models\viewOrdenEnvasado;
use App\Models\viewOrdenImpresion;
use App\Models\viewOrdenInyeccion;
use App\Models\viewOrdenTrabajoAdm;
use App\Models\viewOrdenTrabajoTermo;
use Carbon\Carbon;
use Illuminate\Http\Request;

class OrdenTrabController extends Controller
{
    public function index(Request $request)
    {
        $id     = 0;
        $header = $request->header('access-token');
        $val    = User::select('token', 'id', 'activado')->where('token', $header)->get();
        if ($header == '') {
            return response()->json('error', 203);
        } else {
            foreach ($val as $item) {
                $id = $item->id;
            }

            if ($id > 0) {
                return viewOrdenTrabajoAdm::all();
            } else {
                return response()->json('error', 203);
            }
        }
    }

    public function indexTermo(Request $request)
    {
        $id     = 0;
        $header = $request->header('access-token');
        $val    = User::select('token', 'id', 'activado')->where('token', $header)->get();
        if ($header == '') {
            return response()->json('error', 203);
        } else {
            foreach ($val as $item) {
                $id = $item->id;
            }

            if ($id > 0) {
                return viewOrdenTrabajoTermo::all();
            } else {
                return response()->json('error', 203);
            }
        }
    }

    public function indexTermoFil(Request $request)
    {
        $id     = 0;
        $header = $request->header('access-token');
        $data   = request()->all();
        $val    = User::select('token', 'id', 'activado')->where('token', $header)->get();
        if ($header == '') {
            return response()->json('error', 203);
        } else {
            foreach ($val as $item) {
                $id = $item->id;
            }

            if ($id > 0) {
                return viewOrdenTrabajoTermo::all()->where('op', $data['orpNumRea']);
            } else {
                return response()->json('error', 203);
            }
        }
    }

    public function indexEnvasado(Request $request)
    {
        $id     = 0;
        $header = $request->header('access-token');
        $val    = User::select('token', 'id', 'activado')->where('token', $header)->get();
        if ($header == '') {
            return response()->json('error', 203);
        } else {
            foreach ($val as $item) {
                $id = $item->id;
            }

            if ($id > 0) {
                return viewOrdenEnvasado::all();
            } else {
                return response()->json('error', 203);
            }
        }
    }

    public function indexInyeccion(Request $request)
    {
        $id     = 0;
        $header = $request->header('access-token');
        $val    = User::select('token', 'id', 'activado')->where('token', $header)->get();
        if ($header == '') {
            return response()->json('error', 203);
        } else {
            foreach ($val as $item) {
                $id = $item->id;
            }

            if ($id > 0) {
                return viewOrdenInyeccion::all();
            } else {
                return response()->json('error', 203);
            }
        }
    }


    public function indexImpresion(Request $request)
    {
        $id     = 0;
        $header = $request->header('access-token');
        $val    = User::select('token', 'id', 'activado')->where('token', $header)->get();
        if ($header == '') {
            return response()->json('error', 203);
        } else {
            foreach ($val as $item) {
                $id = $item->id;
            }

            if ($id > 0) {
                return viewOrdenImpresion::all();
            } else {
                return response()->json('error', 203);
            }
        }
    }

    public function ins(Request $request)
    {
        $id     = 0;
        $header = $request->header('access-token');
        $val    = User::select('token', 'id', 'activado', 'name')->where('token', $header)->get();
        $data   = $request->all();
        foreach ($data as $item) {
            $fecha   =  Carbon::now()->format('Y-m-d');
            $ordenes = $item['otdet'];
            $Ordt    = OrdenTrabajo::create([
                'empId'     => 1,
                'idOrp'     => $item['idOrp'],
                'orptFech'  => $fecha,
                'orptUsrG'  => $item['name'],
                'orptTurns' => 'test',
                'orptEst'   => 1,
                'orptPrio'  => $item['orptPrio']
            ]);

            $idOrdt = $Ordt->id;

            foreach ($ordenes as $orddet) {

                $affected =   OrdTrabDet::create([
                    'idOrdt'       => $idOrdt,
                    'empId'        => 1,
                    'ordtdPrdCod'  => $orddet['orpdPrdCod'],
                    'ordtdPrdDes'  => $orddet['orpdPrdDes'],
                    'ortdSol'      => $orddet['orpdTotP'],
                    'ortdProd'     => 0,
                    'orpdObs'      => '',
                    'ortidOrpd'    => $orddet['idOrpd'],
                    'ortidOrp'     => $orddet['idOrp'],
                    'orpdidEta'    => $orddet['orpdidEta']
                ]);
            }

            if (isset($affected)) {
                $resources = array(
                    array(
                        "error" => "0", 'mensaje' => "Orden ingresada manera correcta",
                        'type' => 'success'
                    )
                );
                return response()->json($resources, 200);
            } else {
                return response()->json('error', 204);
            }
        }
    }



    public function filopNumRea(Request $request)
    {

        $data   = request()->all();
        $resources = viewOrdenTrabajoAdm::select('*')->where('orden_produccion', $data['orpNumRea'])->get();
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

    public function insOTT(Request $request)
    {

        $data      = $request->all();

        foreach ($data as $item) {
            $idOrdt    = $data['id'];
            $cod_etapa = $data['cod_etapa'];
            $idOrdtd   = $data['id_det'];
            $uso       = $data['name'];
        }

        $fecha    = Carbon::now()->format('Y-m-d');

        switch ($cod_etapa) {
            case 5:
                $affected = Termoformado::create([
                    'empId'      => 1,
                    'terUso'     => $uso,
                    'terEst'     => 'P',
                    'terEstCtl'  => 'P',
                    'idOrdt'     => $idOrdt,
                    'idOrdtd'     => $idOrdtd
                ]);
                break;
            case 7:
                $affected = Inyeccion::create([
                    'empId'      => 1,
                    'inyUso'     => $uso,
                    'inyEst'     => 'P',
                    'inyEstCtl'  => 'P',
                    'idOrdt'     => $idOrdt,
                    'idOrdtd'     => $idOrdtd
                ]);
                break;
            case 8:
                $affected = Impresion::create([
                    'empId'      => 1,
                    'impUso'     => $uso,
                    'impEst'     => 'P',
                    'impEstCtl'  => 'P',
                    'idOrdt'     => $idOrdt,
                    'idOrdtd'     => $idOrdtd

                ]);
                break;
        }
        if (isset($affected)) {
            $resources = array(
                array(
                    "error" => "0", 'mensaje' => "Orden generada manera correcta",
                    'type' => 'success'
                )
            );
            return response()->json($resources, 200);
        } else {
            return response()->json('error', 204);
        }
    }

    public function verOtIny(Request $request)
    {

        $idOrdt    = $request->idOrdt;
        $idOrdtd   = $request->idOrdtd;
        $inyeccion =  viewOrdenInyeccion::select('*')
            ->where('id', $idOrdt)
            ->where('id_det', $idOrdtd)
            ->get();

        return response()->json($inyeccion, 200);
    }

    public function verOtTer(Request $request)
    {
        $id     = 0;
        $header = $request->header('access-token');
        $val    = User::select('token', 'id', 'activado')->where('token', $header)->get();
        if ($header == '') {
            return response()->json('error', 203);
        } else {
            foreach ($val as $item) {
                $id = $item->id;
            }
            if ($id > 0) {
                $idOrdt       = $request->idOrdt;
                $idOrdtd      = $request->idOrdtd;
                $termoformado  =  viewOrdenTrabajoTermo::select('*')
                    ->where('id', $idOrdt)
                    ->where('id_det', $idOrdtd)
                    ->get();

                return response()->json($termoformado, 200);
            } else {
                return response()->json('error', 203);
            }
        }
    }


    public function verOtImp(Request $request)
    {

        $idOrdt       = $request->idOrdt;
        $idOrdtd      = $request->idOrdtd;
        $impresion    =  viewOrdenImpresion::select('*')
            ->where('id', $idOrdt)
            ->where('id_det', $idOrdtd)
            ->get();

        return response()->json($impresion, 200);
    }

    public function AprOt(Request $request)
    {

        $idOrdt       = $request->id;
        $idOrdtd      = $request->id_det;

        $orden    =  OrdenTrabajo::select('*')
            ->where('idOrdt', $idOrdt)
            ->get();

        foreach ($orden as $item) {
            $idOrp = $item->idOrp;
        }

        $affect = OrdenTrabajo::where('idOrdt', $idOrdt)->update([
            'orptEst' => 3
        ]);

        OrdenProduccion::where('idOrp', $idOrp)->update([
            'orpEst' => 3
        ]);

        $resources = array(
            array(
                "error" => "0", 'mensaje' => "Impresión Autorizado",
                'type' => 'success'
            )
        );
        return $resources;
    }
}
