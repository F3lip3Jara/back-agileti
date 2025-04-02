<?php

namespace App\Jobs;

use App\Models\Parametros\Region;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CargadeRegiones implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */

     
     private $paiId;
     private $iso2;

    public function __construct( $paiId , $iso2)
    {
      
        $this->paiId = $paiId;
        $this->iso2 = $iso2;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        
        $json = file_get_contents(__DIR__ . '/data/c_states_cities.json');
        $data = json_decode($json);
        

        $job = new LogSistema( 36 , 63 , 'root' , 1 , 'INCIAR CARGA MASIVA DE REGIONES' , 'info');
        dispatch($job);  

        foreach($data as $item){
            if($item->iso2 === $this->iso2){
                $states = $item->states;
                foreach($states as $itemReg){
                        $cod    = $itemReg->state_code;
                        $name   = $itemReg->name;
                        $cities = $itemReg->cities;

                        $affected = Region::create([
                                'paiId'  => $this->paiId,
                                'empId'  => 1,
                                'regCod' => $cod,
                                'regDes' => $name
                        ]);

                        $regId = $affected->id;

                     
                            
                        $job = new Ciudades($cities ,$this->paiId ,$regId);
                        dispatch($job); 

                 //   array_push($state , $region);
                }
              
                
            }
        }
    }
}
