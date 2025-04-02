<?php

namespace App\Jobs;

use App\Models\Oms\WebhookOms;
use App\Services\OmsServiceProducto;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

use function PHPUnit\Framework\isJson;

class OmsProductWebHook implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    private $empId; 

    public function __construct($empId)
    {
        $this->empId = $empId;
      
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $data               = WebhookOms::select('*')
                                        ->where('web_estado', 'N')
                                        ->where('x_wc_webhook_resource', 'product')
                                        ->get();

        $CLIENT_KEY         = env('WOOECOMMERCE_USER');
        $CLIENT_SECRET      = env('WOOECOMMERCE_PASS');

        $omsServiceProducto = new OmsServiceProducto();
        //$omsServiceProducto->manejarProducto($obj, $this->empId);
        foreach($data as $obj) {
            try {
                // Lógica específica para productos
                if (isJson($obj->json)) {
                    $json = json_decode($obj->json);
                 
        
                if($json->type=='variable'){
                    $id          = $json->id;
                    $name        = $json->name;
                    $slug        = $json->slug;
                    $descripcion = $json->description;

                    // Construir la URL base
                    $baseUrl = 'https://app.ecommerce.agileti.cl/wp-json/wc/v3/products/'.$id.'/variations/';   
                    
                    // Usar Http facade en lugar de GuzzleHttp\Client
                    $response = Http::withBasicAuth($CLIENT_KEY, $CLIENT_SECRET)
                        ->withoutVerifying()
                        ->get($baseUrl);
                        
                    // Verificar si la solicitud fue exitosa
                    if ($response->successful()) {
                        $data = $response->json();
                        foreach($data as $item){
                            $omsServiceProducto->manejarProducto($item, $this->empId , $name, $slug , $descripcion , 'V');

                        } 
                        $affected = WebhookOms::where('omshId', $obj->omshId)->update([
                            'web_estado' => 'S'
                        ]);
                        Log::info("Producto procesado ".$obj->omshId );
                        continue;
                        
                    } else {
                        Log::error("Error procesnado Producto".$obj->omshId );
                        continue;
                    }

                    
                }else{
                    $omsServiceProducto->manejarProducto($json, $this->empId , $name , $slug , $descripcion , 'S');

                    $affected = WebhookOms::where('omshId', $obj->omshId)->update([
                        'web_estado' => 'S'
                    ]);

                    Log::info("Producto procesado ".$obj->omshId );
                    continue;
                }
                } else {
                        // Manejo de error o caso en que no sea ni array ni JSON
                        Log::error(' "No es ni array ni JSON válido: ');
                        continue;
                        
                }        
            
            } catch (\Exception $e) {
                Log::error('Error procesando producto: ' . $e->getMessage());
                continue;
            }
        }
    }
}