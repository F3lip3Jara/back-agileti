<?php

namespace App\Jobs;

use App\Models\Parametros\Region;
use App\Models\Parametros\RegionCargaControl;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class CargadeRegiones implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */

     
     private $paiId;
     private $iso2;
     private $regCargaId;

    public function __construct( $paiId , $iso2 , $regCargaId )
    {
      
        $this->paiId = $paiId;
        $this->iso2 = $iso2;
        $this->regCargaId = $regCargaId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
           

            $json = file_get_contents(__DIR__ . '/data/countries/'.$this->iso2.'.json');
            $data = json_decode($json);
            
            $totalRegiones = 0;          
            $totalRegiones = count($data->states);
              
         

            // Actualizar total de regiones
            $control = RegionCargaControl::find($this->regCargaId);
            RegionCargaControl::where('regCargaId', $control->regCargaId)->update([
                'regCargaTotal' => $totalRegiones
            ]);
            
            $progreso = 0;
        
            $states = $data->states;
            foreach($states as $itemReg) {
                try {
                            $cod = $itemReg->state_code;
                            $name = $itemReg->name;
                            $cities = $itemReg->cities;

                            $affected = Region::create([
                                'paiId' => $this->paiId,
                                'empId' => 1,
                                'regCod' => $cod,
                                'regDes' => $name
                            ]);

                            $regId = $affected->id;
                            
                            $job = new Ciudades($cities, $this->paiId, $regId);
                            dispatch($job);

                            $progreso++;
                            $control->update(['regCargaProgreso' => $progreso]);

                        } catch (\Exception $e) {
                            Log::error('Error procesando región: ' . $e->getMessage());
                            $control->update([
                                'regCargaError' => 'Error en región ' . $name . ': ' . $e->getMessage()
                            ]);
                        }
                }                   
            

            // Marcar como completado
              RegionCargaControl::where('regCargaId', $control->regCargaId)->update([
                'regCargaEst' => 'C'
            ]);
            
            

        } catch (\Exception $e) {
            Log::error('Error en carga de regiones: ' . $e->getMessage());
            if (isset($control)) {
                $control->update([
                    'regCargaEst' => 'F',
                    'regCargaError' => $e->getMessage()
                ]);
            }
        }
    }
}
