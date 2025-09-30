<?php

namespace App\Http\Controllers;

use App\Models\Sd\SdPallet;
use App\Models\Sd\SdPalletDet;
use App\Models\Sd\SdStockIblpn;
use Illuminate\Http\Request;

class BultosController extends Controller
{
    public function searchBulto(Request $request)
    {
        $lpn = $request->lpn;


        $bulto = SdStockIblpn::
        join('sd_iblpns', 'sd_iblpns.iblpnId', '=', 'sd_stocks_iblpns.iblpnId')
        ->join('productos', 'productos.id', '=', 'sd_stocks_iblpns.prdId')
        ->where('iblpnOriginalBarcode', $lpn)
        ->orWhere('iblpnHdrCustShortText3', $lpn)
        ->select('sd_stocks_iblpns.*', 'sd_iblpns.iblpnOriginalBarcode', 'sd_iblpns.iblpnHdrCustShortText3', 'sd_iblpns.iblpnHdrCustShortText4', 'sd_iblpns.iblpnQty', 'productos.descripcion' , 'productos.cod_pareo' , 'productos.cod_barra')
        ->first();


        if($bulto){
            //Filtro que el bulto no este asignado a un pallet
            $pallet = SdPalletDet::
            join('sd_pallets', 'sd_pallets.palletId', '=', 'sd_pallets_det.palletId')
            ->where('iblpnId', $bulto->iblpnId)
            ->select('sd_pallets.pall_codigo')
            ->first();
           
            if($pallet){
              $mensaje = array(
                'palletcodigo' => $pallet->pall_codigo,
              );
              return response()->json($mensaje);
            }


            $bulto = [
                'lpn' => $bulto->iblpnHdrCustShortText3,
                'sku' => $bulto->cod_pareo,
                'descripcion' => $bulto->descripcion,
                'cantidad' => $bulto->iblpnQty,
                'ubicacionActual' => $bulto->iblpnHdrCustShortText5,
            ];
          
        }

        return response()->json($bulto);       
       
    }

 
}
