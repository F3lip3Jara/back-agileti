<?php

namespace App\Jobs;

use App\Models\Sd\SdIblpns;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SdCierreTraslado implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public $empId;
    public $iblpns;

    public function __construct($empId , $iblpns)
    {
        $this->empId  = $empId;
        $this->iblpns = $iblpns;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        foreach($this->iblpns as $item){
            $iblpnOriginalBarcode = $item['iblpnOriginalBarcode'];
            SdIblpns::where('iblpnOriginalBarcode', $iblpnOriginalBarcode)->update(
                [
                    'iblpnStatus' => 'A'
                ]
            );
            
        }
    }
}
