<?php

namespace App\Http\Controllers\Oms;
use App\Http\Controllers\Controller;
use App\Jobs\OmsPrdWebhook;
use App\Models\Oms\OrdenWeb;
use App\Models\Oms\WebhookOms;
use App\Models\Sd\SdOrden;
use App\Models\Sd\SdOrdeTemp;
use App\Models\Seguridad\Empresa;
use App\Services\OmsServiceOrden;
use App\Services\OmsServiceProducto;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

use function PHPUnit\Framework\isJson;

class WebhookController extends Controller
{

    public function __construct()
    {

    }


    public function ins(Request $request){
        $data                  = $request->all();
        $header                = $request->header();        
        $x_wc_webhook_event    = $request->header('x-wc-webhook-event');
        $x_wc_webhook_resource = $request->header('x-wc-webhook-resource');
        $x_wc_webhook_topic    = $request->header('x-wc-webhook-topic');
        
        
        $affected = WebhookOms::create([
            'json' => json_encode($data) ,
            'header'=>json_encode($header),
                'x_wc_webhook_event'   =>$x_wc_webhook_event,
                'x_wc_webhook_resource'=>$x_wc_webhook_resource,
                'x_wc_webhook_topic'   =>$x_wc_webhook_topic,
                'session'              =>'',
                'web_estado'           =>'N'
        ]);
        $job = new OmsPrdWebhook();
        dispatch($job);            
        return response()->json('ok', 200);
    }

    public function carro(Request $request){
      

        return response()->json('ok', 200);

    }

   /*

    public function product_web( Request $request){

        $CLIENT_KEY    = 'ck_c97b99b6f38f8740c87b4649f24764bb758faf2b';
        $CLIENT_SECRET = 'cs_7827de097fe2b842e93bf12e8c07fc172b0047d8';

         $client = new Client([
            'base_uri' => 'https://app.ecommerce.agileti.cl/wp-json/custom-api/v1/',
            'auth' => [$CLIENT_KEY, $CLIENT_SECRET], // Sustituye con tus credenciales
            'timeout' => 10, // Limita el tiempo de espera para evitar bloqueos
            'verify' => false, // Mantén esta opción activada para validar el certificado
        ]);

        $featured     = $request['featured'];
        $category     = $request['category'];
      
        // Construir argumentos de consulta
        $args = [
          
        ];

        // Agregar filtros condicionales
        if ($featured) {
            $args['featured'] = $featured;
        }

        if ($category) {
            $args['category'] = $category;
        }

        return $args;
        
        $response = $client->get('products-info?', $args);
        $data     = json_decode($response->getBody(), true);
       
        return $data;

        $product  = [];

        foreach($data as $item){
            $parent_id              = $item['id'];
            $parent_name            = $item['name'];
            $parent_images          = $item['images'][0]['src'];
            $variantes              = [];
            $permalink              = $item['permalink']; 
            $catalog_visibility     = $item['catalog_visibility'];
           
            if($catalog_visibility =='visible'){
                if($item['type']== 'variable'){
        
                    $url = "https://app.ecommerce.agileti.cl/wp-json/wc/v3/products/". $parent_id . "/";
                    $client = new Client([
                        'base_uri' => $url,
                        'auth' => [$CLIENT_KEY, $CLIENT_SECRET], // Sustituye con tus credenciales
                        'timeout' => 10, // Limita el tiempo de espera para evitar bloqueos
                        'verify' => false, // Mantén esta opción activada para validar el certificado
                    ]);
                    $response = $client->get('variations');
                    $variant  = json_decode($response->getBody(), true);
                    
                      foreach($variant as $item){
                       
                            $price         = $item['price'];
                            $regular_price = $item['regular_price'];
                            $sale_price    = $item['sale_price'];
                            $id            = $item['id'];
                            $name          = $item['name'];
        
                            array_push($variantes , array(
                                    'price'        => $price,
                                    'id'           => $id,
                                    'regular_price'=> $regular_price,
                                    'sale_price'   => $sale_price,
                                    'name'         => $name,
                                    
                            ));
                      } 
                   }
                   $producto  = array(
                        'parent_id'=> $parent_id,
                        'name'     => $parent_name,
                        'image'    => $parent_images,
                        'permalink'=> $permalink,
                        'variantes'=> $variantes,
                    );  
                   
        
                   array_push($product , $producto);
            }

          
        }

        $resources = $product;
        return response()->json($resources, 200);

        // Devuelve los datos al frontend
          // Verificar si la solicitud fue exitosa
        /*  if ($response->) {
            return response()->json( $data); // Devuelve los datos al frontend
        } else {
            return response()->json(['error' => 'Failed to fetch data'], $response->status());
        }}*/
        
   


    public function product_web(Request $request) {
        $CLIENT_KEY    = env('WOOECOMMERCE_USER');
        $CLIENT_SECRET = env('WOOECOMMERCE_PASS');
    
        // Construir la URL base
        $baseUrl = 'https://app.ecommerce.agileti.cl/wp-json/custom-api/v1/products-info';
        
        // Obtener parámetros del request
        $queryParams = [];
        
        if ($request->has('featured')) {
            $queryParams['featured'] = $request->featured;
        }
        
        if ($request->has('category')) {
            $queryParams['category'] = $request->category;
        }
        
        if ($request->has('on_sale')) {
            $queryParams['on_sale'] = $request->on_sale;
        }
        
        // Usar Http facade en lugar de GuzzleHttp\Client
        $response = Http::withBasicAuth($CLIENT_KEY, $CLIENT_SECRET)
            ->withoutVerifying()
            ->get($baseUrl, $queryParams);
    
        // Verificar si la solicitud fue exitosa
        if ($response->successful()) {
            $data = $response->json();
            // ... resto del procesamiento de datos existente ... 
            return response()->json($data, 200);
        } else {
            return response()->json(['error' => 'Failed to fetch data'], $response->status());
        }
    }
    
    public function webHooks(Request $request){

        dispatch(new OmsPrdWebhook());            
            
    }



    
}
