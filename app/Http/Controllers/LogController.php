<?php

namespace App\Http\Controllers;

use App\Models\Seguridad\LogSys;
use App\Models\User;
use Illuminate\Http\Request;


class LogController extends Controller
{
    public function index(Request $request)
    {

        return response()->json(LogSys::all(), 200);
    }

    public function ins(Request $request)
    {
        $data     = $request->all();
        $id       = $data['idUser'];
        $name     = $data['name'];
        $empId    = $data['empId'];
        
        if ($data['lgName'] == '') {
                       
            $affected    = LogSys::create([
                'empId'      => $empId,
                'etaId'      => $data['etaId'],
                'etaDesId'   => $data['etaDesId'],
                'lgId'       => $id,
                'lgName'     => $name,
                'lgDes'      => $data['lgDes'],
                'lgDes1'     => $data['lgDes1'],
                'lgTip'      => 1
            ]);

            return response()->json('save', 200);
        } else {
            $lgID     = User::all()->where('name', $data['lgName']);
            foreach ($lgID as $item) {
                $id =  $item->id;
            }

            if ($id > 0) {
                $affected    = LogSys::create([
                    'empId'      => 1,
                    'etaId'      => $data['etaId'],
                    'etaDesId'   => $data['etaDesId'],
                    'lgId'       => $id,
                    'lgName'     => $data['lgName'],
                    'lgDes'      => $data['lgDes'],
                    'lgDes1'     => $data['lgDes1'],
                    'lgTip'      => 1
                ]);
                return response()->json('save', 200);
            } else {
                return response()->json('error', 203);
            }
        }
    }
}
