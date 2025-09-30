<?php

namespace App\Jobs;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Storage;
class ValidaEtiqueta implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */

    public $pedidos;
    public $tipo;
    public function __construct($pedidos , $tipo)
    {
        $this->pedidos = $pedidos; 
        $this->tipo = $tipo;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {   $etiquetas = [];    
        $boletas = [];
        if($this->tipo == 'E'){
            $this->validaEtiqueta($this->pedidos);
        }else{
            if($this->tipo == 'B'){
                $this->boleta($this->pedidos);
            }else{
           // $this->cancelarPedido($this->pedidos);
            }
        }
    
    }

    public function validaEtiqueta($pedido)
    { 
        $contador  = 0;
        foreach ($this->pedidos as $pedido) {
            $contador++;
    
            $url = 'https://colgram-api.knownonline.com/workflow/GetEtiquetaDespacho?numero_pedido_origen=' . $pedido;
    
            try {
                $client = new Client([
                    'verify' => false,
                    'headers'  => [
                        'auth-key' => '124',
                        'Content-Type' => 'application/json'
                    ]
                ]);
    
                $req = $client->get($url);
                $info = json_decode($req->getBody());
    
                if (!empty($info->etiquetas)) {
                    $code = $info->etiquetas[0]->codigo_Barra_etiqueta;

                    if(!empty($code)){
                        $eti = [
                            'pedido' => $pedido,
                            'mensaje' => 'Etiqueta encontrada',
                            'type' => 'success'
                        ];
                    }else{  
                        $eti = [
                            'pedido' => $pedido,
                            'mensaje' => 'Etiqueta no encontrada',
                            'type' => 'success'
                        ];
                    }
                } else {
                    $eti = [
                        'pedido' => $pedido,
                        'mensaje' => 'Etiqueta no encontrada',
                        'type' => 'success'
                    ];
                }
    
                $etiquetas[] = $eti;
    
            } catch (BadResponseException $ex) {
                $eti = [
                    'pedido' => $pedido,
                    'mensaje' => 'Error al obtener la etiqueta',
                    'type' => 'danger'
                ];
                $etiquetas[] = $eti;
            }
    
            // 👉 Agregar sleep cada 100 pedidos
            if ($contador % 100 === 0) {
                sleep(5); // duerme 5 segundos (ajusta según lo que necesites)
                $contador = 0;
            }

            // Guardar archivo JSON
            $json = json_encode($etiquetas, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            Storage::put('etiquetas/resultadosv3.json', $json);
        }
    }

    public function boleta($pedido)
    {
        $contador  = 0;
        foreach ($this->pedidos as $pedido) {
            $contador++;
    
            // URL base con parámetros en query string
            $url = 'https://colgram-api.knownonline.com/workflow/get-pedido';
            
            // Parámetros para la URL (query string)
            $queryParams = [
                'pedido_id' => $pedido,
                'tipo' => 'boleta',
                'version' => 'v1'
            ];
            
            // Construir URL con parámetros
            $urlWithParams = $url . '?' . http_build_query($queryParams);
            
            // Datos para el body de la petición POST
            $postData = [
                'pedido_id' => $pedido,
                'timestamp' => now()->toISOString(),
                'source' => 'agileti-system'
            ];
    
            try {
                $client = new Client([
                    'verify' => false,
                    'headers'  => [
                        'Authorization' => ' ApiKey 673368cb826ed91cbf03255012b639d48d46cc9e7a9abd007f37f8f2941359cf',
                        'x-api-key-name' => 'backend-prod',
                        'Content-Type' => 'application/json'
                    ]
                ]);
    
                // Hacer petición POST con parámetros en URL y datos en body
                $req = $client->post($urlWithParams, [
                    'json' => $postData
                ]);
                
                $info                  = json_decode($req->getBody());
                $numero_Pedido_Interno = $info->numero_Pedido_Interno;
                $boleta                = $info->numero_Boleta;

                $bol = [
                    'pedido' => $pedido,
                    'numero_Pedido_Interno' => $numero_Pedido_Interno,
                    'boleta' => $boleta,
                    'type' => 'success',
                    'body' => $req->getBody()
                ];
                $boletas[] = $bol;
                
             
        } catch (BadResponseException $ex) {
            $bol = [
                'pedido' => $pedido,
                'mensaje' => 'Error al obtener la boleta',
                'type' => 'danger',
                'error' => $ex->getMessage()
            ];
            $boletas[] = $bol;
        }

         // Guardar archivo JSON
         $json = json_encode($boletas, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
         Storage::put('etiquetas/boletas.json', $json);

       }
    }

    public function cancelarPedido($pedido)
    {
        $contador  = 0;


        foreach ($this->pedidos as $pedido) {
            $contador++;
    
            // URL base con parámetros en query string
            $url = 'https://colgram-api.knownonline.com/workflow/retry-pos';
            
            // Parámetros para la URL (query string)
          /* $queryParams = [
                'pedido_id' => $pedido,
                'tipo' => 'boleta',
                'version' => 'v1'
            ];*/
            
            // Construir URL con parámetros
         //   $urlWithParams = $url . '?' . http_build_query($queryParams);
           $urlWithParams = $url;
            // Datos para el body de la petición POST
            $postData = [
               "orderId" => $pedido
            ];
            try {
                $client = new Client([
                    'verify' => false,
                    'headers'  => [
                        'Authorization' => ' ApiKey 673368cb826ed91cbf03255012b639d48d46cc9e7a9abd007f37f8f2941359cf',
                        'x-api-key-name' => 'backend-prod',
                        'Content-Type' => 'application/json'
                    ]
                ]);
    
                // Hacer petición POST con parámetros en URL y datos en body
                $req = $client->post($urlWithParams, [
                    'json' => $postData
                ]);
                
                $info                  = json_decode($req->getBody());
               

                $pedidoCancelado = [
                    'pedido' => $pedido,                   
                    'type' => 'success',
                    'body' => $info
                ];
                $pedidosCancelados[] = $pedidoCancelado;
                
             
        } catch (BadResponseException $ex) {
            $pedidoCancelado = [
                'pedido' => $pedido,
                'mensaje' => 'Error al POS retry el pedido',
                'type' => 'danger',
                'error' => $ex->getMessage()
            ];
            $pedidosCancelados[] = $pedidoCancelado;
        }

         // Guardar archivo JSON
         $json = json_encode($pedidosCancelados, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
         Storage::put('etiquetas/pedidosCancelados.json', $json);

       }
    }
}
