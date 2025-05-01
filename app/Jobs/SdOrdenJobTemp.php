<?php

namespace App\Jobs;

use App\Models\Produccion\OrdenProduccion;
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
            $det    = $orden->detalle;
            $ordtId = $item->ordtId;
            $ordNumber = $orden->tipo_cod.$orden->centro_id.$orden->almacen_id.$ordtId;

            $affected = SdOrden::create([
            'empId'                => $orden->empId,
            'centroId'             => $orden->centro_id,
            'almId'                => $orden->almacen_id,
            'ordNumber'            => $ordNumber,// Número de onda
            'ordQty'               => $orden->prd_total_lineas,// Cantidad de orden
            'ordestatus'           => 'P', // Estado de la orden  pediente
            'ordTip'               => $orden->tipo_id, // Tipo Salida / Entrada
            'ordTipDes'            => $orden->tipo_cod,//Tipo Salida / Entrada
            'ordClase'             =>$orden->tipo_cod,//Clase 
            'ordClaseDes'          => $orden->tipo_des,//Clase 
            'ordHdrCustShortText1' => str_replace(',', '.', $orden->id),//Documeno relacionado
            'ordHdrCustShortText2' => $orden->proveedor_id,//Proveedor  / Cliente
            'ordHdrCustShortText3' => $orden->proveedor,//Nombre Proveedor / Cliente
            'ordHdrCustShortText4' => $orden->fecha,//Fecha de la orden
            'ordHdrCustShortText5' => $orden->fech_promesa,//Fecha promesa entrega
            'ordHdrCustShortText6' => $orden->prd_total,//Cantidad de lineas
            'ordHdrCustShortText7' => '',//Nombre
            'ordHdrCustShortText8' => '',//Email
            'ordHdrCustShortText9' => '',//Courier
            'ordHdrCustShortText10'=> '',//Latitud de la orden
            'ordHdrCustShortText11'=>'',// Lomgitud de la orden
            'ordHdrCustShortText12'=>'',//Clase de documento
            'ordHdrCustShortText13'=>'',//Libre
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
                'ordDtlCustShortText3'  =>$orden->almacen_destino,
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
            OrdenProduccion::where('orpId', $orden->id) ->update(['orpEst' => 2]);
        }
    }
}
