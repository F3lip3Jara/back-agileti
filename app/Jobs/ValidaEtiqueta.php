<?php

namespace App\Jobs;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;
use Exception;

class ValidaEtiqueta implements ShouldQueue, ShouldBeUnique
{
    use Queueable;

    /**
     * Número de veces que se puede intentar el job.
     */
    public $tries = 1;

    /**
     * Tiempo máximo de ejecución del job en segundos.
     */
    public $timeout = 1200;

    /**
     * Tiempo en segundos que el lock único se mantiene.
     * Después de este tiempo, el job puede ser ejecutado nuevamente.
     */
    public $uniqueFor = 3600; // 1 hora

    /**
     * Create a new job instance.
     */

    public $pedidos;
    public $tipo;
    public $store;
    public $jobId; // ID único para este job

    public function __construct($pedidos , $tipo, $store)
    {
        $this->pedidos = $pedidos; 
        $this->tipo = $tipo;
        $this->store = $store;
        $this->jobId = Str::uuid()->toString(); // Generar ID único para este job
    }

    /**
     * Identificador único del job para evitar duplicados.
     */
    public function uniqueId(): string
    {
        return $this->jobId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {   $etiquetas = [];    
        $boletas = [];
        if($this->tipo == 'C'){
          // $this->actualizacionStockColloky($this->pedidos, $this->store);
     ///   $this->regularizarSkus($this->pedidos);
         //  $this->actualizacionStockOpaline($this->pedidos, $this->store);
    //  $this->cancelarPedido($this->pedidos);
       // $this->crearGiftCard();
   //    $this->retryPOS($this->pedidos);
  // $this->cancelarPedidosQA($this->pedidos);


 //$this->retrySAP($this->pedidos);

$this->validaEtiqueta($this->pedidos);
//$this->actualizacionStockColloky($this->pedidos, $this->store);

        }else{
            if($this->tipo == 'B'){
           //  $this->validaEtiqueta($this->pedidos);
            }else if($this->tipo == 'P'){
          //      $this->preparacion($this->pedidos);
            }
        }
    
    }

    /**
     * Manejar un fallo del job.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error("ValidaEtiqueta job falló después de {$this->tries} intentos", [
            'job_id' => $this->jobId,
            'pedidos' => $this->pedidos,
            'tipo' => $this->tipo,
            'store' => $this->store,
            'exception' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString()
        ]);
    }

    public function validaEtiqueta($pedido)
    { 
        $contador  = 0;
        $fecha = now()->format('Y-m-d H:i:s');
        $fecha = str_replace(' ', '_', $fecha);
        $fecha = str_replace('-', '_', $fecha);
        $fecha = str_replace(':', '_', $fecha);
        
        // Usar un nombre de archivo único por job para evitar race conditions
        $nombreArchivo = "etiquetas/resultado_etiquetas/resultadosv3_".$fecha."_".$this->jobId.".json";
        $etiquetas = [];

        foreach ($this->pedidos as $pedido) {
            $contador++;
            $hora = now()->toISOString();
           
    
           $url = 'https://colgram-api.knownonline.com/workflow/GetEtiquetaDespacho?numero_pedido_origen=' . $pedido;
           // $url='https://oms-servicios-api.azure-api.net/oms-api-produccion/GetEtiquetaDespacho?numero_pedido_origen=' . $pedido;
            try {
                $client = new Client([
                    'verify' => false,
                    'timeout' => 30, // Timeout de 30 segundos por petición
                    'headers'  => [
                        'auth-key' => '4f58f5892ee3497b88806fc005f0c07d',
                        'Content-Type' => 'application/json'
                    ]
                ]);
    
                $req = $client->get($url);
                $info = json_decode($req->getBody());
    
                if (!empty($info->etiquetas)) {
                    $code = $info->etiquetas[0]->codigo_Barra_etiqueta ?? $info->etiquetas[0]->codigo_Barra_Etiqueta;

                    if(!empty($code)){
                        $eti = [
                            'pedido' => $pedido,
                            'mensaje' => 'Etiqueta encontrada',
                            'type' => 'success',
                            "hora" => $hora,
                            "termino"=>now()->toISOString(),
                            "duracion"=>now()->diffInSeconds($hora),
                            'codigo_barra' => $code
                        ];
                    }else{  
                        $eti = [
                            'pedido' => $pedido,
                            'mensaje' => 'Etiqueta no encontrada y/o sin codigo de barra',
                            'type' => 'danger',
                            'codigo_barra' => $code,
                            "hora" => $hora,
                            "termino"=>now()->toISOString(),
                            "duracion"=>now()->diffInSeconds($hora)
                        ];
                    }
                } else {
                    $eti = [
                        'pedido' => $pedido,
                        'mensaje' => 'Etiqueta no encontrada',
                        'type' => 'danger',
                        "hora" => $hora,
                        "termino"=>now()->toISOString(),
                        "duracion"=>now()->diffInSeconds($hora),
                       
                    ];
                }
    
                $etiquetas[] = $eti;
    
            } catch (GuzzleException $ex) {
                // Capturar todas las excepciones de Guzzle (BadResponseException, ConnectException, etc.)
                $eti = [
                    'pedido' => $pedido,
                    'mensaje' => 'Error al obtener la etiqueta: ' . $ex->getMessage(),
                    'type' => 'danger',
                    "hora" => $hora,
                    "termino"=>now()->toISOString(),
                    "duracion"=>now()->diffInSeconds($hora),
                    'error_code' => $ex->getCode()
                ];
                $etiquetas[] = $eti;
            } catch (Exception $ex) {
                // Capturar cualquier otra excepción
                $eti = [
                    'pedido' => $pedido,
                    'mensaje' => 'Error inesperado: ' . $ex->getMessage(),
                    'type' => 'danger',
                    "hora" => $hora,
                    "termino"=>now()->toISOString(),
                    "duracion"=>now()->diffInSeconds($hora),
                    'error_code' => $ex->getCode()
                ];
                $etiquetas[] = $eti;
            }
    
            // 👉 Agregar sleep cada 100 pedidos
            if ($contador % 100 === 0) {
               // sleep(5); // duerme 5 segundos (ajusta según lo que necesites)
                $contador = 0;
            }

            // Guardar archivo JSON después de cada pedido (con manejo de errores)
            try {
                $json = json_encode($etiquetas, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
                Storage::put($nombreArchivo, $json);
            } catch (Exception $ex) {
                // Si falla el guardado, registrar el error pero continuar
                Log::error("Error al guardar archivo de etiquetas: " . $ex->getMessage());
                // No lanzar la excepción para que el job continúe procesando
            }
        }

        // Generar archivo CSV al finalizar el procesamiento
        $this->generarCSV($etiquetas, $fecha);
    }

    /**
     * Genera un archivo CSV con los resultados de las etiquetas
     */
    private function generarCSV(array $etiquetas, string $fecha): void
    {
        try {
            $nombreArchivoCSV = "etiquetas/resultado_etiquetas/resultadosv3_" . $fecha . "_" . $this->jobId . ".csv";
            
            // Definir las cabeceras del CSV
            $cabeceras = ['pedido', 'mensaje', 'type', 'hora', 'termino', 'duracion', 'codigo_barra', 'error_code'];
            
            // Crear el contenido del CSV
            $csvContent = implode(',', $cabeceras) . "\n";
            
            foreach ($etiquetas as $etiqueta) {
                $fila = [
                    $this->escaparCSV($etiqueta['pedido'] ?? ''),
                    $this->escaparCSV($etiqueta['mensaje'] ?? ''),
                    $this->escaparCSV($etiqueta['type'] ?? ''),
                    $this->escaparCSV($etiqueta['hora'] ?? ''),
                    $this->escaparCSV($etiqueta['termino'] ?? ''),
                    $this->escaparCSV($etiqueta['duracion'] ?? ''),
                    $this->escaparCSV($etiqueta['codigo_barra'] ?? ''),
                    $this->escaparCSV($etiqueta['error_code'] ?? '')
                ];
                $csvContent .= implode(';', $fila) . "\n";
            }
            
            Storage::put($nombreArchivoCSV, $csvContent);
            Log::info("Archivo CSV generado: " . $nombreArchivoCSV);
            
        } catch (Exception $ex) {
            Log::error("Error al generar archivo CSV: " . $ex->getMessage());
        }
    }

    /**
     * Escapa un valor para CSV (maneja comas, comillas y saltos de línea)
     */
    private function escaparCSV($valor): string
    {
        $valor = (string) $valor;
        
        // Si contiene comas, comillas o saltos de línea, envolver en comillas
        if (strpos($valor, ',') !== false || strpos($valor, '"') !== false || strpos($valor, "\n") !== false) {
            // Escapar comillas dobles duplicándolas
            $valor = str_replace('"', '""', $valor);
            return '"' . $valor . '"';
        }
        
        return $valor;
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
    
            $url = 'https://colgram-api.knownonline.com/workflow/post-order-state';
            $urlWithParams = $url;
            $hora = now()->toISOString();
            $postData = [
                "OrderSourceNumber" => $pedido,
                "OrderDate" => $hora,
                "StateChangeDate" => $hora,              
                "State" => "Cancelado"
               
             ];

             try {
                $client = new Client([
                    'verify' => false,
                    'headers'  => [
                        'Authorization' => ' ApiKey 673368cb826ed91cbf03255012b639d48d46cc9e7a9abd007f37f8f2941359cf',
                        'x-api-key-name' => 'backend-prod',
                        'Content-Type' => 'application/json',
                        'api-key'=>'123'
                    ]
                ]);
    
                // Hacer petición POST con parámetros en URL y datos en body
                $req = $client->post($urlWithParams, [
                    'json' => $postData
                ]);
                
                $info= json_decode($req->getBody());
               

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
         Storage::put('etiquetas/preparacion.json', $json);
                
         
       }
    }

    public function bodegas($pedido)
    {
        $contador  = 0;

        foreach ($this->pedidos as $item) {
            $tienda =  $item->Tienda;
            $nombre =  $item->Codigo;

            $contador++;
    
            // URL base con parámetros en query string
            $url = 'https://colgramcl.vtexcommercestable.com.br/api/logistics/pvt/configuration/warehouses';
            
          
            // Datos para el body de la petición POST
            $body = [
                    "id" => str_replace(' ', '', $nombre),
                    "name" => $tienda,
                    "warehouseDocks" => [],
                    "priority" => 0,
                    "isActive" => true
            ];
            try {
              
                $client = new Client([
                    'verify' => false,
                    'headers'  => [
                        'X-VTEX-API-AppKey' => 'vtexappkey-colgramcl-IKHRQV',
                        'X-VTEX-API-AppToken' => 'HYYVJFVLHDEUFDGGLRNBIZWQDUTZQESZOWFGNHWSUOXWAVWIQWPKZZYDIPTGEMPYMYHGKSHYAVQPSVOBWOBMRVOCICDPZSJSMHTEHUGVKNSGQZFCETZMGOYLCZRUWTXJ',
                        'Content-Type' => 'application/json',
                        'vtexappkey-colgramcl-IKHRQV' => 'HYYVJFVLHDEUFDGGLRNBIZWQDUTZQESZOWFGNHWSUOXWAVWIQWPKZZYDIPTGEMPYMYHGKSHYAVQPSVOBWOBMRVOCICDPZSJSMHTEHUGVKNSGQZFCETZMGOYLCZRUWTXJ'
                    ]
                ]);
    
                // Hago la peticion con el body
                $req = $client->post($url, [    
                    'json' => $body
                ]);

                $info                  = json_decode($req->getBody());
               

                $bodega = [
                    'bodega' => $tienda,                   
                    'type' => 'success',
                    'body' => $info
                ];
                $bodegas[] = $bodega;
                
        } catch (BadResponseException $ex) {
            $bodega = [
                'bodega' => $tienda,
                'mensaje' => 'Error al POS retry el pedido',
                'type' => 'danger',
                'error' => $ex->getMessage()
            ];
            $bodegas[] = $bodega;
        } 

        // Guardar archivo JSON
        $json = json_encode($bodegas, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        Storage::put('etiquetas/bodegas.json', $json);

      }
    }

    public function preparacion($pedidos)
    {
        $contador  = 0;
        $hora = now()->toISOString();
          
        foreach($pedidos as $pedido){
            $url = 'https://colgram-api.knownonline.com/workflow/post-order-state';
            $urlWithParams = $url;

            $postData = [
                "OrderSourceNumber" => $pedido->internalOrderId,
                "OrderDate" => $hora,
                "StateChangeDate" => $hora,
                "CourierServicioCodigoExterno" => $pedido->courier,
                "State" => "EnPreparacion",
                "OrderPacking" => [
                    "Numero_Bultos" => 1,
                    "Numero_Envio" => $pedido->deliveryNumber,
                    "Url_Tracking" => $pedido->trackingUrl
                ],
                "Documents" => [],
                "Items" => []
             ];

             try {
                $client = new Client([
                    'verify' => false,
                    'headers'  => [
                        'Authorization' => ' ApiKey 673368cb826ed91cbf03255012b639d48d46cc9e7a9abd007f37f8f2941359cf',
                        'x-api-key-name' => 'backend-prod',
                        'Content-Type' => 'application/json',
                        'api-key'=>'123'
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
         Storage::put('etiquetas/preparacion.json', $json);

        }
    }


    public function actualizacionStockColloky($pedido , $store)
    {
        $contador  = 0;      

       
        foreach ($this->pedidos as $pedido) {
            $contador++;
    
            // URL base con parámetros en query string
            $url = 'https://colgramcl.vtexcommercestable.com.br/api/logistics/pvt/inventory/skus/'.$pedido.'/warehouses/'.$store;
          //  $url ='colgram-oms-backend-dev.knownonline.com/workflow/retry-pos';
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
                "quantity" => 0
            ];

            
            try {
                $client = new Client([
                    'verify' => false,
                    'headers'  => [
                        'X-VTEX-API-AppKey' => 'vtexappkey-colgramcl-IKHRQV',
                        'X-VTEX-API-AppToken' => 'HYYVJFVLHDEUFDGGLRNBIZWQDUTZQESZOWFGNHWSUOXWAVWIQWPKZZYDIPTGEMPYMYHGKSHYAVQPSVOBWOBMRVOCICDPZSJSMHTEHUGVKNSGQZFCETZMGOYLCZRUWTXJ',
                        'Content-Type' => 'application/json',
                        'vtexappkey-colgramcl-IKHRQV' => 'HYYVJFVLHDEUFDGGLRNBIZWQDUTZQESZOWFGNHWSUOXWAVWIQWPKZZYDIPTGEMPYMYHGKSHYAVQPSVOBWOBMRVOCICDPZSJSMHTEHUGVKNSGQZFCETZMGOYLCZRUWTXJ'
                    ]
                ]);
    
                // Hacer petición POST con parámetros en URL y datos en body
              

                $req = $client->put($url, [
                  
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

    public function actualizacionStockOpaline($pedido , $store)
    {
        $contador  = 0;      

       
        foreach ($this->pedidos as $pedido) {
            $contador++;
    
            // URL base con parámetros en query string
            $url = 'https://opalinecl.myvtex.com/api/logistics/pvt/inventory/skus/'.$pedido.'/warehouses/'.$store;
          
            $postData = [
                "quantity" => 0
            ];

            
            try {
                $client = new Client([
                    'verify' => false,
                    'headers'  => [
                        'X-VTEX-API-AppKey' => 'vtexappkey-opalinecl-zhsguu',
                        'X-VTEX-API-AppToken' => 'PCYNIHBQQHAIJHXIHRYDRVSAVESKIQEMTEEEBIJTTJSTTWBHFKMIFUUMUWRFBPPURBPKHFVMQDIVDHUEYCOGKYALNTDULSMSSLGDEUREAMKHZVAHNATKLZZFITYNUWHW',
                        'Content-Type' => 'application/json',
                        'vtexappkey-opalinecl-zhsguu' =>  'PCYNIHBQQHAIJHXIHRYDRVSAVESKIQEMTEEEBIJTTJSTTWBHFKMIFUUMUWRFBPPURBPKHFVMQDIVDHUEYCOGKYALNTDULSMSSLGDEUREAMKHZVAHNATKLZZFITYNUWHW'
                    ]
                ]);
    
                // Hacer petición POST con parámetros en URL y datos en body
              

                $req = $client->put($url, [
                  
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

    public function crearGiftCard(){

        foreach ($this->pedidos as $item) {
            $id = $item->id;
            $price=$item->price;
            $code = $item->code;
            $rut = $item->rut;
            $hoy = now()->toISOString();
          //  $url = 'https://colgramcl.vtexcommercestable.com.br/api/giftcards/'.$id.'/transactions';
            $url ="";
            $postData = [
                "operation"=>"Credit",
                "value"=> $price,
                "description"=> $rut,
                "redemptionToken"=> $code,
                "redemptionCode"=> $code,
                "requestId"=> $hoy
            ];

            try {
                $client = new Client([
                    'verify' => false,
                    'headers'  => [
                        'X-VTEX-API-AppKey' => 'vtexappkey-colgramcl-IKHRQV',
                        'X-VTEX-API-AppToken' => 'HYYVJFVLHDEUFDGGLRNBIZWQDUTZQESZOWFGNHWSUOXWAVWIQWPKZZYDIPTGEMPYMYHGKSHYAVQPSVOBWOBMRVOCICDPZSJSMHTEHUGVKNSGQZFCETZMGOYLCZRUWTXJ',
                        'Content-Type' => 'application/json',
                        'vtexappkey-colgramcl-IKHRQV' => 'HYYVJFVLHDEUFDGGLRNBIZWQDUTZQESZOWFGNHWSUOXWAVWIQWPKZZYDIPTGEMPYMYHGKSHYAVQPSVOBWOBMRVOCICDPZSJSMHTEHUGVKNSGQZFCETZMGOYLCZRUWTXJ'
                    ]
                ]);
    
                // Hacer petición POST con parámetros en URL y datos en body
              

                $req = $client->post($url, [                  
                    'json' => $postData
                ]);

                $info                  = json_decode($req->getBody());
               

                $pedidoCancelado = [
                    'pedido' => $code,                   
                    'type' => 'success',
                    'body' => $info
                ];
                $pedidosCancelados[] = $pedidoCancelado;
                
             
        } catch (BadResponseException $ex) {
            $pedidoCancelado = [
                'pedido' => $code,
                'mensaje' => 'Error al POS retry el pedido',
                'type' => 'danger',
                'error' => $ex->getMessage()
            ];
            $pedidosCancelados[] = $pedidoCancelado;
        }

         // Guardar archivo JSON
         $json = json_encode($pedidosCancelados, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
         Storage::put('etiquetas/giftCards.json', $json);


        }

            
            
       

    }

    public function retryPOS($pedido){

     
        $contador  = 0;

        foreach ($this->pedidos as $pedido) {
            $contador++;
    
            $url = 'https://colgram-api.knownonline.com/workflow/retry-pos';
            $urlWithParams = $url;
            $hora = now()->toISOString();
            $postData = [
                "orderId" => $pedido
               
             ];

             try {
                $client = new Client([
                    'verify' => false,
                    'headers'  => [
                        'Authorization' => ' ApiKey 673368cb826ed91cbf03255012b639d48d46cc9e7a9abd007f37f8f2941359cf',
                        'x-api-key-name' => 'backend-prod',
                        'Content-Type' => 'application/json',
                        'api-key'=>'123'
                    ]
                ]);
    
                // Hacer petición POST con parámetros en URL y datos en body
                $req = $client->post($urlWithParams, [
                    'json' => $postData
                ]);
                
                $info= json_decode($req->getBody());
               

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
         Storage::put('etiquetas/preparacion.json', $json);
                
         
       }

    }

    public function cancelarPedidosQA($pedidos){
        $contador  = 0;

        foreach ($this->pedidos as $pedido) {
            $contador++;
    
            $url = 'https://colgram-oms-backend-dev.knownonline.com/workflow/post-order-state';
            $urlWithParams = $url;
            $hora = now()->toISOString();
            $postData = [
                "OrderSourceNumber" => $pedido,
                "OrderDate" => $hora,
                "StateChangeDate" => $hora,              
                "State" => "Cancelado"
               
             ];

             try {
                $client = new Client([
                    'verify' => false,
                    'headers'  => [
                     //   'Authorization' => ' ApiKey 673368cb826ed91cbf03255012b639d48d46cc9e7a9abd007f37f8f2941359cf',
                    //    'x-api-key-name' => 'backend-prod',
                        'Content-Type' => 'application/json',
                        'api-key'=>'123'
                    ]
                ]);
    
                // Hacer petición POST con parámetros en URL y datos en body
                $req = $client->post($urlWithParams, [
                    'json' => $postData
                ]);
                
                $info= json_decode($req->getBody());
               

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
         Storage::put('etiquetas/pedidosCanceladosQA.json', $json);
                
         
       }


    }



    
    public function retrySAP($pedido){

     
        $contador  = 0;

        foreach ($this->pedidos as $pedido) {
            $contador++;
    
            $url = 'colgram-api.knownonline.com/workflow/retry-sap';
            $urlWithParams = $url;
            $hora = now()->toISOString();
            $postData = [
                "orderId" => $pedido
               
             ];

             try {
                $client = new Client([
                    'verify' => false,
                    'headers'  => [
                        'Authorization' => ' ApiKey 673368cb826ed91cbf03255012b639d48d46cc9e7a9abd007f37f8f2941359cf',
                        'x-api-key-name' => 'backend-prod',
                        'Content-Type' => 'application/json',
                        'api-key'=>'123'
                    ]
                ]);
    
                // Hacer petición POST con parámetros en URL y datos en body
                $req = $client->post($urlWithParams, [
                    'json' => $postData
                ]);
                
                $info= json_decode($req->getBody());
               

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
         Storage::put('etiquetas/pedidosRetrySAP.json', $json);
                
         
       }

    }


    public function regularizarSkus($pedidos)
    {
        $pedidosRegularizados = [];

        $client = new Client([
            'verify' => false,
            'headers'  => [
                'X-VTEX-API-AppKey' => 'vtexappkey-colgramcl-IKHRQV',
                'X-VTEX-API-AppToken' => 'HYYVJFVLHDEUFDGGLRNBIZWQDUTZQESZOWFGNHWSUOXWAVWIQWPKZZYDIPTGEMPYMYHGKSHYAVQPSVOBWOBMRVOCICDPZSJSMHTEHUGVKNSGQZFCETZMGOYLCZRUWTXJ',
                'Content-Type' => 'application/json'
            ]
        ]);

        foreach ($pedidos as $pedido) {
           try {
                // GET producto por ID
               $urlGet = "https://colgramcl.vtexcommercestable.com.br/api/catalog/pvt/product/" . $pedido;
                $req = $client->get($urlGet);
                $producto = json_decode($req->getBody());

                // Modifico el campo ReleaseDate
                //dia anterior
                $diaAnterior = '2025-11-15T00:00:00';
                //dos días anteriores
               // $dosDiasAnteriores = now()->subDays(2)->toISOString();
                $producto->ReleaseDate = $diaAnterior;

                // PUT para actualizar el producto
               $urlPut = "https://colgramcl.vtexcommercestable.com.br/api/catalog/pvt/product/" . $pedido;
                $req = $client->put($urlPut, [
                    'json' => $producto
                ]);

                $response = json_decode($req->getBody());

                $pedidoRegularizado = [
                    'productId' => $pedido,
                    'name' => $response->Name ?? $producto->Name ?? '',
                    'type' => 'success',
                    'body' => $response
                ];
                $pedidosRegularizados[] = $pedidoRegularizado;

            } catch (BadResponseException $ex) {
                $pedidoRegularizado = [
                    'productId' => $pedido,
                    'type' => 'danger',
                    'error' => $ex->getMessage()
                ];
                $pedidosRegularizados[] = $pedidoRegularizado;
            } catch (Exception $ex) {
                $pedidoRegularizado = [
                    'productId' => $pedido,
                    'type' => 'danger',
                    'error' => $ex->getMessage()
                ];
                $pedidosRegularizados[] = $pedidoRegularizado;
            }
        }

        // Guardar archivo JSON
        $json = json_encode($pedidosRegularizados, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        Storage::put('etiquetas/pedidosRegularizados.json', $json);
    }
}

