<?php

namespace App\Jobs;

use App\Models\Notificaciones;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\Request;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class NotificacionesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    
    /**
     * Create a new job instance.
     *
     * @return void
     */

     
    protected $usuario;
    protected $etapa;
    protected $tipo;
    protected $loteSalida;
    

    public function __construct($lote_salida, $etapa , $tipo , $name)
    
    {  

        $this->usuario    = $name;
        $this->etapa      = $etapa;
        $this->loteSalida = $lote_salida;
        $this->tipo       = $tipo;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
    
      Log::info("Job 1.1 de notificaciones ejecutada");
       
      switch ($this->etapa){
        case 3:
            $etapax = 'Mezcla';
            break;
        case 4:
            $etapax = 'Extrusion';
            break;
        case 5: 
            $etapax = 'Termoformado';
            break;
        case 6: 
            $etapax = 'Evansado';
             break;
        case 7: 
            $etapax = 'Inyección';
            break;
        case 8: 
            $etapax = 'Impresión';
            break;
        default:
            Log::info('Error en la ejecución del job de notificaciones');
        }

        if($this->tipo == 'A'){
            Notificaciones::create([
                'empId' =>1,
                'notUso'=>$this->usuario,
                'notEst'=>"P",
                'notObs'=>$etapax." autorizada",
                'notLotSal'=>$this->loteSalida
            ]);

        }else{
            Notificaciones::create([
                'empId' =>1,
                'notUso'=>$this->usuario,
                'notEst'=>"P",
                'notObs'=>$etapax." rechazada",
                'notLotSal'=>$this->loteSalida
            ]);
        }
    }
}
