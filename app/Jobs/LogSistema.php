<?php

namespace App\Jobs;
use App\Models\Seguridad\LogSys;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class LogSistema implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    
    private $etaId;
    private $etaDesId;
    private $name;
    private $empId;
    private $etaDesDes;
    private $etaTip;

    public function __construct( $etaId ,$etaDesId, $name , $empId, $etaDesDes, $etaTip )
    {
       
        $this->etaId      = $etaId;
        $this->etaDesId   = $etaDesId;
        $this->name       = $name;
        $this->empId      = $empId;
        $this->etaDesDes  = $etaDesDes;
        $this->etaTip     = $etaTip;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info('Job de actualizacion de LOG'. $this->etaDesDes);
        $lgDes1      = $this->name.', '.$this->etaDesDes;      
     

        $affected    = LogSys::create([
            'empId'      => $this->empId,
            'etaId'      => $this->etaId,   
            'etaDesId'   => $this->etaDesId,            
            'lgName'     => $this->name,
            'lgDes'      => $this->etaDesDes,
            'lgDes1'     => $lgDes1,
            'lgTip'      => 1,
            'lgId'       => 1,
            'lgDes2'     => $this->etaTip

        ]);
    
    }
}
