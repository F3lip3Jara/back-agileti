<?php

namespace App\Jobs;

use App\Models\Parametros\Producto;
use App\Models\Sd\Almacen;
use App\Models\Sd\SdIblpns;
use App\Models\Sd\SdMovStocks;
use App\Models\Sd\SdOrden;
use App\Models\Sd\SdStockIblpn;
use App\Models\Sd\SdStocks;
use App\Models\Sd\SdTIblns;
use App\Models\Sd\SdTraslado;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

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
    
    
            foreach($data as $item){ 
                $dataJson       = json_decode($item->stockTblpnJson);
                $stockTblpnId   = $item->stockTblpnId;               

                foreach ($dataJson as $item) {                   
                    $prdCod         = $item->ordDtlCustShortText1;            
                    $prdId          = Producto::where('prdCod', $prdCod)->get();
                    $prdId          = $prdId[0]['prdId'];                       
                    $enteredQty     = $item->enteredQty;
                    $sectorId       = $item->sectorFil->sectorId;
                    $sectorCod      = $item->sectorFil->secCod;
                    $almId          = $item->almId;
                    $centroId       = $item->centroId;
                    $ordId          = $item->ordId;
                    $orddId         = $item->orddId;
                    $orddNumber     = $item->orddNumber;

                
                   $affected   = SdIblpns::create([                
                        'empId'                  => $this->empId,
                        'prdId'                  => $prdId,
                        'iblpnQty'               => $enteredQty,             
                        'iblpnStatus'            => 'T', //P: Pendiente, A: Almacenado, R: Reservado, T: En tránsito
                        'iblpnType'              => 'I', //I: Ingreso, E: Egreso
                        'iblpnHdrCustShortText1' => $ordId, //Orden de SD
                        'iblpnHdrCustShortText2' => $orddId, //Id de la Orden de SD
                        'iblpnHdrCustShortText3' => $sectorId, //Sector destino 
                        'iblpnHdrCustShortText4' => $sectorCod, //Orden de Origen
                        'iblpnHdrCustShortText5' => $item->orddNumber, //Number Orden
                        'iblpnHdrCustShortText6' => $item->orddQtySol,// Cantidad Orignal
                    ]);

                    $iblpnId              = $affected['id']; 
                    $iblpnOriginalBarcode = $affected['iblpnOriginalBarcode'];

                    $stockQty = SdStocks::where('prdId', $prdId)->where('centroId', $item->centroId)->where('almId', $item->almId)->get();
                  
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
                                                'stockQty'   => $stockQty + $enteredQty,
                                            ]);    
                    }else{

                        $affected = SdStocks::create([
                            'empId'      => $this->empId,
                            'centroId'   => $item->centroId,
                            'almId'      => $item->almId,
                            'prdId'      => $prdId,
                            'stockQty'   => $enteredQty,
                            'stockEst'   => 'T', //Transito
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
                        'stockMovQty'               => $enteredQty,
                        'prdId'                     => $prdId,
                        'stockMovHdrCustShortText1' => $centroId,
                        'stockMovHdrCustShortText2' => $cenDes,
                        'stockMovHdrCustShortText3' => $almId,
                        'stockMovHdrCustShortText4' => $almDes,
                        'stockMovHdrCustShortText5' => $this->idUser,
                        'stockMovHdrCustShortText6' => $this->name
                    ]);

                    $affected = SdStockIblpn::create([
                        'empId'     => $this->empId,
                        'centroId'  => $centroId,
                        'almId'     => $almId,
                        'iblpnId'   => $iblpnId,
                        'prdId'     => $prdId,
                        'stockIblpnQty' => $enteredQty,
                    ]);

                

                    SdTraslado::create([
                        'empId'     => $this->empId,     
                        'centroId'  => $centroId,                     
                        'almId'     => $almId,     
                        'iblpnId'   => $iblpnId,    
                        'trasTip'   => 'I',           
                        'trassecCod' => $orddNumber,
                        'trassecDes' => $orddNumber,             
                        'trasSecDesDes'=>$sectorCod,
                        'trasSecCodDes'=>$sectorCod,
                        'trasUserid' => $this->idUser, 
                        'trasUserName'=>$this->name,
                        'trasHdrCustShortText1' => $ordId,  //Id Orden
                        'trasHdrCustShortText2' => $orddId, //Id Orden Dt
                        'trasHdrCustShortText3' => $iblpnOriginalBarcode, //Iblpn 
                        'trasHdrCustShortText4' => ''//    
                    ]);
    
                }
    
                SdTIblns::where('stockTblpnId', $stockTblpnId)->update(['stockTstatus' => 'S']);
                SdOrden::where( 'ordId' , $ordId)->update(['ordestatus' => 'V']);


                
            }
    }
}
