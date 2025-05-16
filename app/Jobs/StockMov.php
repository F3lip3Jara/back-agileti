<?php

namespace App\Jobs;

use App\Models\Parametros\Producto;
use App\Models\Produccion\OrdProDet;
use App\Models\Sd\Almacen;
use App\Models\Sd\SdIblpns;
use App\Models\Sd\SdMovStocks;
use App\Models\Sd\SdOrden;
use App\Models\Sd\SdOrdenDet;
use App\Models\Sd\SdStockIblpn;
use App\Models\Sd\SdStocks;
use App\Models\Sd\SdTIblns;
use App\Models\Sd\SdTraslado;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Produccion\OrdenProduccion;
class StockMov implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    private $empId;
    private $idUser;
    private $name;

    public function __construct($empId , $idUser , $name)
    {
        $this->empId  = $empId;
        $this->idUser = $idUser;
        $this->name   = $name;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info('Job de actualizacion de ordenes temporales');
        $data = SdTIblns::where('stockTstatus', 'P')
                            ->where('empId', $this->empId)
                            ->get();
        $ordId = 0;
        $centroId = 0;
        $almId = 0;
        $productos = [];
        $ordenInfo = [];
        $orddId = 0;

        foreach($data as $item){ 
            $dataJson         = json_decode($item->stockTblpnJson);
            $stockTblpnId     = $item->stockTblpnId;               
            $ordenInfo        = $dataJson->ordenInfo;
            $productos        = $dataJson->productos;
            $ordId            = $ordenInfo->ordId;
            $centroId         = $ordenInfo->centroId;
            $almId            = $ordenInfo->almId;
            $recepcion        = $item->recepcion;
            $orddNumber       = $ordenInfo->numeroOT;      
            $sectorId         = $recepcion->sectorId ?? '';
            $sectorCod        = $recepcion->sector ?? '';
            $usuarioRecepcion = $recepcion->usuarioRecepcion ?? '';
            $orpId            = SdOrden::where('ordId', $ordId)->value('ordHdrCustShortText1');
           

            foreach($productos as $producto){
                $orddId            = $producto->orddId;
                $ordId             = $producto->ordId;
                $prdCod            = $producto->sku;
                $prdId             = Producto::where('prdCod', $prdCod)->value('prdId');
                $cantidadRecibida  = $producto->cantidadRecibida;
                $stockQty          = SdStocks::where('prdId', $prdId)->where('centroId', $centroId)->where('almId', $almId)->get();
                $lpnsku            = $producto->lpns;

                //Actualizar stock a nivel de producto
                if(sizeof($stockQty) > 0){
                    $stockQty = $stockQty[0]['stockQty'];
                }else{
                    $stockQty = 0;
                }                
    
                if($stockQty  > 0){
                    $affected = SdStocks::where('prdId', $prdId)
                                        ->where('centroId', $item->centroId)
                                        ->where('almId', $item->almId)
                                        ->update([
                                            'stockQty'   => $stockQty + $cantidadRecibida,
                                        ]);    
                }else{

                    $affected = SdStocks::create([
                        'empId'      => $this->empId,
                        'centroId'   => $item->centroId,
                        'almId'      => $item->almId,
                        'prdId'      => $prdId,
                        'stockQty'   => $cantidadRecibida,
                        'stockEst'   => 'P', //Pendiente
                    ]);
                }

                $almacen = Almacen::where('sd_centro_alm.almId', $almId)
                ->where('sd_centro_alm.centroId', $centroId)
                ->join('sd_centro', 'sd_centro_alm.centroId', '=', 'sd_centro.centroId')
                ->get();


                $almDes  = $almacen[0]['almDes'];
                $cenDes  = $almacen[0]['cenDes'];

                SdMovStocks::create([
                'empId'                     => $this->empId,
                'stockMovTip'               => 'I',
                'stockMovQty'               => $cantidadRecibida,
                'prdId'                     => $prdId,
                'stockMovHdrCustShortText1' => $centroId,
                'stockMovHdrCustShortText2' => $cenDes,
                'stockMovHdrCustShortText3' => $almId,
                'stockMovHdrCustShortText4' => $almDes,
                'stockMovHdrCustShortText5' => $this->idUser,
                'stockMovHdrCustShortText6' => $this->name
                ]);

                //LPN'S
                foreach($lpnsku as $lpnItem){
                 
                  

                    $affected   = SdIblpns::create([                
                        'empId'                  => $this->empId,
                        'prdId'                  => $prdId,
                        'iblpnQty'               => $lpnItem->cantidad,             
                        'iblpnStatus'            => 'P', //P: Pendiente, A: Almacenado, R: Reservado, T: En tránsito
                        'iblpnType'              => 'I', //I: Ingreso, E: Egreso
                        'iblpnHdrCustShortText1' => $ordId, //Orden de SD
                        'iblpnHdrCustShortText2' => $orddId, //Id de la Orden de SD
                        'iblpnHdrCustShortText3' => $lpnItem->lpnCodigo, //codigo de caja automatia front
                        'iblpnHdrCustShortText4' => $lpnItem->estado, //Estado de la caja
                        'iblpnHdrCustShortText5' => $lpnItem->metodoIngreso, //Metodo digitacion 
                        'iblpnHdrCustShortText6' => $usuarioRecepcion //Usuario que genera la caja
                    ]);

                    $iblpnId              = $affected['id']; 
                    

                    $affected = SdStockIblpn::create([
                        'empId'     => $this->empId,
                        'centroId'  => $centroId,
                        'almId'     => $almId,
                        'iblpnId'   => $iblpnId,
                        'prdId'     => $prdId,
                        'stockIblpnQty' => $lpnItem->cantidad
                    ]);

                
                }

                $orddCantRecep = SdOrdenDet::where('ordId', $ordId)
                ->where('orddNumber', $orddNumber)
                ->where('ordDtlCustShortText1', $prdCod)
                ->value('orddCantRecep');

                $totalOrden = $orddCantRecep + $cantidadRecibida;

                //aumento la cantidad recibida de la orden de produccion de SdordenDet
                $data = SdOrdenDet::where('ordId', $ordId)
                                    ->where('orddNumber', $orddNumber)
                                    ->where('ordDtlCustShortText1', $prdCod)
                                    ->update([
                                        'orddCantRecep' =>$totalOrden
                                       
                                    ]);
                //actualizo el estado de la orden de produccion de Prodcucion
                
                $data = OrdProDet::where('orpId', $orpId)
                                    ->where('orpdPrdCod', $prdCod)
                                    ->update([
                                        'orpdDtlCustShortText1' => DB::raw("CONCAT(orpdDtlCustShortText1, '{$cantidadRecibida}')")
                                    ]);
                                 
               
            }

            //Cambio el estado de la orden de SD a Almacenado en tabla temporal
            $data = SdTIblns::where('stockTblpnId', $stockTblpnId)->update([
                'stockTstatus' => 'A'
            ]);
            //Cambio el estado de la orden de SD a Verificado
            $data = SdOrden::where('ordId', $ordId)->update([
                'ordestatus' => 'V'
            ]);
            //Cambio el estado de la orden de produccion a 3 = Completada
            $data = OrdenProduccion::where('orpId', $orpId)->update([
                'orpEstPrc' => '3'
            ]);

            

        }

    }
}
