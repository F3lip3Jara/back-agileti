<?php

namespace App\Jobs;

use App\Models\Sd\SdOrden;
use App\Models\Sd\SdOrdenDet;
use App\Models\Sd\SdOrdeTemp;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SdOrdenJobTemp implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var int
     */
    public $empId;

    /**
     * Create a new job instance.
     */
    public function __construct(int $empId)
    {
        $this->empId = $empId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info('Job de actualizacion de ordenes temporales');        
        $data = SdOrdeTemp::where('ordtest', 'N')
        ->where('empId', $this->empId)
        ->get();
        $det  = [];   

        foreach($data as $item){
            $orden = json_decode($item->ordtCustShortText1);
            $det    = $orden->detalles;
            $ordtId = $item->ordtId;
            $ordNumber = $orden->tipo_des.'-'.substr($orden->tipo_des, 0, 1).$orden->orden_compra.$orden->orden_produccion;

            $affected = SdOrden::create([
            'empId'                => $orden->empId,
            'centroId'             => $orden->centro_id,
            'almId'                => $orden->almacen_id,
            'ordNumber'            => $ordNumber,// Número de onda
            'ordQty'               => $orden->prd_total,// Cantidad de orden
            'ordestatus'           => 'L', // Estado de la orden 
            'ordTip'               => $orden->tipo_id, // Tipo Salida / Entrada
            'ordTipDes'            => $orden->tipo_des,//Tipo Salida / Entrada
            'ordClase'             =>'' ,//Clase 
            'ordClaseDes'          => '',//Clase 
            'ordHdrCustShortText1' => '',//Direccion
            'ordHdrCustShortText2' => '',//Ciudad
            'ordHdrCustShortText3' => '',//Región
            'ordHdrCustShortText4' => $orden->id,//Identificación de orden migrado
            'ordHdrCustShortText5' => $orden->fecha,//Fecha de la orden
            'ordHdrCustShortText6' => '',//Teléfono
            'ordHdrCustShortText7' => $orden->proveedor,//Nombre
            'ordHdrCustShortText8' => '',//Email
            'ordHdrCustShortText9' => '',//Courier
            'ordHdrCustShortText10'=> '',//Latitud de la orden
            'ordHdrCustShortText11'=>'',// Lomgitud de la orden
            'ordHdrCustShortText12'=>'',//Clase de documento
            'ordHdrCustShortText13'=>$orden->rut,//Rut
            'ordHdrCustLongText1'  =>''//Comentarios
            ]);

            foreach($det as $detalle){
                SdOrdenDet::create([
                'empId'                 =>$orden->empId,
                'centroId'              =>$orden->centro_id,
                'almId'                 =>$orden->almacen_id,// Agregado cliId
                'ordId'                 =>$affected->id,
                'orddNumber'            =>$ordNumber,
                'orddQtySol'            =>$detalle->orpdCant,
                'orddQtyAsig'           =>0,
                'ordDtlCustShortText1'  =>$detalle->orpdPrdCod, 
                'ordDtlCustShortText2'  =>$detalle->orpdPrdDes,
                'ordDtlCustShortText3'  =>'',
                'ordDtlCustShortText4'  =>'',
                'ordDtlrCustShortText5' =>'',
                'ordDtlCustShortText6'  =>'',
                'ordDtlCustShortText7'  =>'',
                'ordDtlCustShortText8'  =>'',
                'ordDtlCustShortText9'  =>'',
                'ordDtlCustShortText10' =>''
                ]);
            }

            SdOrdeTemp::where('ordtId', $ordtId)->update(['ordtest' => 'S']);
          
        }
    }
}
