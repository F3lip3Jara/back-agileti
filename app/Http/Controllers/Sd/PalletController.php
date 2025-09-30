<?php

namespace App\Http\Controllers\Sd;

use App\Http\Controllers\Controller;
use App\Models\Sd\SdIblpns;
use App\Models\Sd\SdPallet;
use App\Models\Sd\SdPalletDet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class PalletController extends Controller
{
    public function index(Request $request)
    {
        $table   = 'sd_pallets';
        $data    = $request->all();
        $columns = Schema::getColumnListing($table);
        $columns = array_filter($columns, function ($column) {
            return $column !== 'empId'; // Columna a excluir
        });
       
        $columns = array_values($columns); // Reindexar el array si es necesario
        $filtros = $data['filter'];
        $filtros = json_decode(base64_decode($filtros));
       
    
        
       if(isset($filtros)){       
        $data     = SdPallet::query()->filter($filtros)  
                    ->where('sd_pallets.empId', $data['empId'])
                    ->get();
       }
       
        $resources = array(
                "data"   => $data,
                "colums" => $columns
        );
 
	
	  return response()->json($resources, 200); 



    }
    public function create(Request $request)
    {
     
            $data = $request->all();
            $bultos = $data['bultos'];
     
            //Crear un logica para el codigo del pallet
            $pall_codigo = 'PAL-' . date('Ymd') . '-' . rand(1000, 9999);

            //Según el lpn voy a buscar el amacen , sector y ubicacion 
            $registro = SdIblpns::select(
                'iblpnHdrCustShortText6 as centroId',
                'iblpnHdrCustShortText7 as almId',
                'iblpnHdrCustShortText8 as sectorId',
                'iblpnHdrCustShortText10 as ubicacionId'
            )
            ->where('iblpnOriginalBarcode', $bultos[0]['lpn'])
            ->orWhere('iblpnHdrCustShortText3', $bultos[0]['lpn'])
            ->first();

          

            if ($registro) {
                $centroId    = $registro->centroId;
                $almId       = $registro->almId;
                $sectorId    = $registro->sectorId;
                $ubicacionId = $registro->ubicacionId ?? 0;
            } else {
                // Valores por defecto si no se encuentra el registro
                $centroId    = null;
                $almId       = null;
                $sectorId    = null;
                $ubicacionId = null;
                Log::warning('No se encontró registro LPN para obtener ubicación: ' . $bultos[0]['lpn']);
            }

            $ubicacionId = ($ubicacionId  == null) ? 0 : $ubicacionId;

            
           
            $pallet = SdPallet::create([
                'pall_codigo' => $pall_codigo,
                'empId' => $request->empId,
                'centroId' => $centroId,
                'almId' => $almId,
                'sectorId' => $sectorId,
                'ubicacionId' => $ubicacionId,
                'pall_estado' => 'activo',
            ]);
    
            foreach($bultos as $bulto){

             //   dd($bulto);
                $iblpn = SdIblpns::select('iblpnId')
                                ->where('iblpnOriginalBarcode', $bulto['lpn'])
                                ->orWhere('iblpnHdrCustShortText3', $bulto['lpn'])
                                ->first();
                
                if ($iblpn) {
                    $iblpnId = $iblpn->iblpnId;
                    SdPalletDet::create([
                        'palletId' => $pallet->palletId,
                        'iblpnId' => strval($iblpnId),
                    ]);
                } else {
                    // Log del error o manejar el caso cuando no se encuentra el LPN
                    Log::warning('LPN no encontrado: ' . $bulto['lpn']);
                }
            }
            return response()->json($pallet);
    } 
    
    

}
