<?php

namespace App\Jobs;

use App\Models\Parametros\Ciudad;
use App\Models\Parametros\Comuna;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class Ciudades implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    
    private $json;
    private $paiId;
    private $regId;

    public function __construct( $json , $paiId , $regId)
    {
      $this->json = $json;
      $this->paiId = $paiId;
      $this->regId = $regId;
      
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        foreach($this->json as $item){
           
                $ciuCod = $item->id;
                $ciuDes= $item->name;

                $affected1 = Ciudad::create([
                    'paiId'   => $this->paiId,
                    'empId'   => 1,
                    'regId'   => $this->regId,
                    'ciuCod'  => $ciuCod,
                    'ciuDes'  => $ciuDes
                ]);

                $ciuId = $affected1->id;

                $affected2 = Comuna::create([
                    'paiId'  => $this->paiId,
                    'empId'  => 1,
                    'regId'  => $this->regId,
                    'ciuId'  => $ciuId,
                    'comCod' => $ciuCod,
                    'comDes' => $ciuDes
                ]);
            
        }
    }
}
