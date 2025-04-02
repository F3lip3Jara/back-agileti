<?php

namespace App\Jobs;

use App\Models\Notificaciones;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ActualizaEstNotificacionJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */

     protected $affect;

    public function __construct(array $affect)
    {
       
        $this->affect = $affect;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
       
        Log::info('Job de actualizacion de notificaciones');

        foreach($this->affect as $item){
            Notificaciones::where('idNot' , $item->idNot)->update(['notEst' => 'N']);
        }
       
    }
}
