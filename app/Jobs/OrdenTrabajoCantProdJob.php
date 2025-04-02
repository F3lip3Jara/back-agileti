<?php

namespace App\Jobs;

use App\Models\OrdenTrabajo;
use App\Models\OrdTrabDet;
use App\Models\viewOtCantProdInye;
use App\Models\viewOtCantProdImp;
use App\Models\viewOtCantProdTer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;


class OrdenTrabajoCantProdJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */

     protected $id;  
     protected $tipo;
     

    public function __construct($id , $tipo)
    {  
        $this->id     = $id;
        $this->tipo   = $tipo;
       
    }

    /**
     * Execute the job.
     *
     * @return void
     */


    public function handle()
    {
      Log::info('Job de actualizacion de cantidades producidas');

        switch ($this->tipo){
        case 5: 
            //$etapax = 'Termoformado';
            $affect = viewOtCantProdTer::select('*')->where('idTer' , $this->id)->get();
            break;
       // case 6: 
           // $etapax = 'Evansado';
        //     break;
       case 7: 
            //$etapax = 'Inyección';
            $affect = viewOtCantProdInye::select('*')->where('idIny' , $this->id)->get();
            break;
        case 8: 
            //$etapax = 'Impresion';
            $affect = viewOtCantProdImp::select('*')->where('idImp' , $this->id)->get();
            break;
        default:
            Log::info('Error en la ejecución del job de actualizacion de cantidades producidas');
        }

        $tot_prod = 0;
        $idOrdtd  = 0;
        $idOrdt   = 0;
        
        foreach($affect as $item){
            $tot_prod = $item['tot_prd_bul'];
            $idOrdtd  = $item['idOrdtd'];
            $idOrdt   = $item['idOrdt'];
        }

        $ortdProd = 0;
        $orden = OrdTrabDet::select('ortdProd')
                                    ->where('idOrdt',$idOrdt)
                                    ->where ('idOrdtd',$idOrdtd) 
                                    ->get();

        foreach($orden as $item){
            $ortdProd = $item['ortdProd'];
        }

        $total = 0;
        $total = $tot_prod + $ortdProd;
        
        $affect = OrdTrabDet::where('idOrdt', $idOrdt )
                             ->where ('idOrdtd', $idOrdtd) 
                             ->update([
                                    'ortdProd' => $total                       
                                    ]);

                            

        $mensaje = 'Actualizando la etapa:'.$this->tipo.' id:'. $this->id .' total_ini:'. $ortdProd. ' total_producida :'. $tot_prod . ' total_cal'. $total;
         Log::info($mensaje);
    }
}
