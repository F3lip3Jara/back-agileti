<?php

namespace App\Http\Controllers;

use App\Models\BinCol;
use App\Models\BinsHist;
use Illuminate\Http\Request;

class BinController extends Controller
{
    public function index( Request $request)
    {            
            return  BinCol::select(['idColb',
                'colbnum',
                'bins_col.idEta',               
                'etaDes',            
                'colbtip'
                ])->join('etapasUser', 'etapasUser.idEta' , '=' , 'bins_col.idEta')->get();
    }

    public function update(Request $request)
    {
        $data = $request->all();   
        $bins = $data['0']['bins'];
        $colbnumnew = $data['0']['colbnumnew'];  
        
        $affected = BinCol::where('idColb' , $bins['idColb'])->update([
                    'colbnum' =>   $colbnumnew
        ]);

        if($affected > 0){
            BinsHist::create([                            
                            'idColb'     => $bins['idColb'],
                            'empId'      => 1,
                            'idEta'      =>  $bins['idEta'],
                            'colbnum_h'  =>  $bins['colbnum'],
                            'colbtip_h'  =>  $bins['colbtip']
            ]);
        $resources = array(
                        array("error" => "0", 'mensaje' => "Correlativo actualizado de manera correcta",
                        'type'=> 'success')
                        );
        return response()->json($resources, 200);
                
        }else{
            return response()->json('error' , 203);
        }
    }


    public function getIdBin(Request $request){
            $colbnum = BinCol::select('colbnum')->take(1)->get();
            $bin     = $colbnum + 1;
            $affected = BinCol::where('idColb' , 1)->update([
                    'colbnum' =>   $bin
            ]);
            
            if($affected > 0){
                    $resources = array(
                        array("error" => "0", 'mensaje' => "Problemas en correlativo",
                        'type'=> 'success')
                        );
            }else{
                   return $bin;
            }
        
    }
    public function verHist(Request $request)
    {
                $data   = $request->all();
                $idColb = $data['idColb'];
                $resources= BinsHist::select('*')->where( 'idColb' , $idColb)->get();                                
                 return response()->json($resources, 200);
    }
}



