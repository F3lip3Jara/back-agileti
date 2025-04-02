<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\OrdenVenta;
use App\Models\OrdenVentaDet;
use App\Models\TranHub;
use Carbon\Carbon;
use Error;
use Exception;
use Illuminate\Http\Request;
use Transbank\Webpay\WebpayPlus;
use Transbank\Webpay\WebpayPlus\Transaction;

class OrdenVentaController extends Controller
{
    public function index(){

    }

    public function ins(Request $request){
      $data = $request->all();
      $name = $data['name'];
      
        $orvFech        = $data[0]['orvFech'];
        $orvObs         = $data[0]['orvObs'];
        $idTipPag       = $data[0]['idTipPag'];
        $idPrv          = $data[0]['idPrv'];
        $ordenes        = $data[0]['ordenes'];
        $orvPrecioTot   = $data[0]['orvPrecioTot'];
        $orvPrecioIva   = $data[0]['orvPrecioIva'];
        $orvPrecioPag   = $data[0]['orvPrecioPag'];
        $carbonDate = Carbon::create($orvFech['year'], $orvFech['month'], $orvFech['day']);
      
     $affected = OrdenVenta::create([
       'empId'          => 1,
       'idPrv'          => $idPrv,
       'orvNumRea'      => '',
       'orvFech'        => $carbonDate,
       'orvUsrG'        => $name,
       'orvObs'         => $orvObs,
       'orvEst'         => 'P',
       'orvEstTrans'    => 'P',
       'orvNumTrj'      => '',
       'orvPrecioTot'   => $orvPrecioTot,
       'orvPrecioIva'   => $orvPrecioIva,
       'orvPrecioPag'   => $orvPrecioPag,       
       'idTipPag'       => $idTipPag,
    ]);

    if (isset($affected)) {

        foreach($ordenes as $item){
            $xaffected = OrdenVentaDet::create([
                'idOrv'     => $affected->id,
                'empId'     => 1,
                'idMon'     =>$item['Mon'] ,
                'orpvPrdCod'=>$item['prdCod'],
                'orpvPrdDes'=>$item['prdDes'],
                'orpvPrdCost'=>$item['prdCost'],
                'orpvPrdNet'=>$item['prdNet'],
                'orpvCant'  =>$item['orpvCant'],
                'orpvPrecio'=>$item['orpvPrecio'],
                'orpvDesc'  =>$item['orpvDesc']
             ]);
        }
        if (isset($xaffected)) {
            $resources = array(
                array(
                    "error" => "0", 'mensaje' => "Orden de Venta ingresada de manera correcta",
                    'type' => 'success'
                )
            );
            return response()->json($resources, 200);
            
        }else {
                return response()->json('error', 204);
            }      
        } else {
            return response()->json('error', 204);
        }      
   
    }

    public function indexPago(Request $request){
        $data = $request->all();
        $header = $request->header('access-token');
        
        if ($header == '') {
            return response()->json('error', 203);
        } else {
            if($header === '1949296726654f991305947'){                

               $affected = OrdenVenta::select('idOrv', 'orvFech', 'prvNom','prvDir','prvTel','prvRut','prvMail','ord_venta.idPrv' ,'orvPrecioTot', 'orvPrecioIva', 'orvPrecioPag')
               ->join('proveedor', 'proveedor.idPrv' , 'ord_venta.idPrv' )
               ->where('orvNumRea', $data['orden'])->get();

               if (sizeof($affected) > 0) {

                    $id = $affected[0]['idOrv'];

                    $affected1 = OrdenVentaDet::select('monCod','orpvPrdCod', 'orpvPrdDes',  'orpvCant' ,  'orpvPrdNet', 'orpvDesc', 'orpvPrecio' )
                    ->join('moneda', 'moneda.idMon' , 'ord_venta_det.idMon' )
                    ->where('idOrv', $id)->get();
                    $count = count($affected1);
                    $orden = array(
                        'orden'    => $affected,
                        'ordenDet' => $affected1,
                        'total'    => $count
                    );
                    $resources = array("error" => '0', 'mensaje' => "Orden Coorecta", 'type' => 'success' , 'orden'=>$orden);
                    
                    return response()->json($resources, 200);
            } else {
                $resources = 
                    array("error" => '1', 'mensaje' => "Orden Incorrecta", 'type' => 'warning')
                ;
                return response()->json($resources, 200);
            }
            }
        }
        
         
    }

    public function transbank(Request $request){

        $data = $request->all();
        $header = $request->header('access-token');
        
        if ($header == '') {
            return response()->json('error', 203);
        } else {
            if($header === '1949296726654f991305947'){    
                $affected = OrdenVenta::select('orvPrecioPag' , 'orvNumRea')
                ->where('idOrv' , $data['idOrv'])
                ->where('idPrv', $data['idPrv'])
                ->get();}

                if (sizeof($affected) > 0) {
                    // SDK Versión 2.x
                    // El SDK apunta por defecto al ambiente de pruebas, no es necesario configurar lo siguiente
                    //  WebpayPlus::configureForIntegration('597055555532', '579B532A7440BB0C9079DED94D31EA1615BACEB56610332264630D42D0A36B1C');
                    $amount      =  $affected[0]['orvPrecioPag'];
                    $session_id  ='123456';
                    $orvNumRea   =  $affected[0]['orvNumRea'];
                    $return_url  = 'http://127.0.0.1:8000/transbankRe?orvNumRea='.$orvNumRea.'&idOrv='.$data['idOrv'];   
                   

                    $transaction = new Transaction();
                    $response    = $transaction->create($orvNumRea, $session_id, $amount, $return_url);
                    $url         = $response->getUrl();
                    $token       = $response->getToken();
                    $transbank   = $url.'?token_ws='.$token;
                    $resources = array("error" => '0', 'url' => $transbank);
                    $xaffected = TranHub::create([
                        'idOrv'   =>  $data['idOrv'],
                        'json'    =>  "",
                        'token_ws'=>  $token,
                        'empId'   =>  1,
                        'transtip'=> 'create'
                    ]); 


                    
                    return response()->json($resources, 200);
                }else{
                    return response()->json('error', 203);
                }

                
            }
    }

    public function transbankRe(Request $request){
       
        $token     = $request['token_ws'];
        $orvNumRea = $request['orvNumRea'];
        $idOrv     = $request['idOrv'];            

        try{

            if($token == ''){          
                $token     = $request['TBK_TOKEN'];
           
                $response  = (new Transaction)->status($token);
                OrdenVentaController::statusT($token , $response, $idOrv);
                $amount    = $response->getAmount();

                $xaffected = TranHub::create([
                    'idOrv'   =>  $idOrv,
                    'json'    =>  "",
                    'token_ws'=>  $token,
                    'transtip'=> 'anulada web',
                    'transtatus'=> '',
                    'empId'   =>  1
                ]);

                if($response->getVci() == 'TSY' && $response->getStatus() == "AUTHORIZED"){  
    
                    return redirect('http://localhost:4200/#/cliente/exitoso?orvNumRea='.$orvNumRea);
                }else{                    
                    return redirect('http://localhost:4200/#/cliente/error?orvNumRea='.$orvNumRea);
                }

                
            }else{
              
                $response = (new Transaction)->commit($token);
    
                $xaffected = TranHub::create([
                    'idOrv'   =>  $idOrv,
                    'json'    =>  '',
                    'token_ws'=>  $token,
                    'transtip'=> 'commit',
                    'transtatus'=>  $response->getStatus(),
                    'empId'   =>  1
                ]); 
    
                
    
                if($response->getVci() == 'TSY' && $response->getStatus() == "AUTHORIZED"){  
                  
                    $json = [
                        "vci"               => $response->getVci(),
                        "amount"            => $response->getAmount(),
                        "status"            => $response->getStatus(),
                        "buyOrder"          => $response->getBuyOrder(),
                        "sessionId"         => $response->getSessionId(),
                        "cardDetail"        => $response->getCardDetail(),
                        "accountingDate"    => $response->getAccountingDate(),
                        "transactionDate"   => $response->getTransactionDate(),
                        "authorizationCode" => $response->getAuthorizationCode(),
                        "paymentTypeCode"   => $response->getPaymentTypeCode(),
                        "responseCode"      => $response->getResponseCode(),
                        "installmentsAmount"=> $response->getInstallmentsAmount(),
                        "installmentsNumber"=> $response->getInstallmentsNumber(),
                        "balance"           => $response->getBalance(),
                    ];
                    
                    $xaffected = TranHub::create([
                        'idOrv'   =>  $idOrv,
                        'json'    =>  json_encode($json),
                        'token_ws'=>  $token,
                        'transtip'=> 'commit2',
                        'transtatus'=>  $response->getStatus(),
                        'empId'   =>  1
                    ]); 
    
                    return redirect('http://localhost:4200/#/cliente/exitoso?orvNumRea='.$orvNumRea);
                 }else{
                   
                    $response = (new Transaction)->status($token);
                    $xstatus   = OrdenVentaController::statusT($token , $response , $idOrv);
    
                    if($response->getVci() == 'TSY' && $response->getStatus() == "AUTHORIZED"){  
    
                        return redirect('http://localhost:4200/#/cliente/exitoso?orvNumRea='.$orvNumRea);
                    }else{                    
                        return redirect('http://localhost:4200/#/cliente/error?orvNumRea='.$orvNumRea);
                    }
                }

            }
           
        }catch(Exception $ex){
            $response = (new Transaction)->status($token);
            OrdenVentaController::statusT($token , $response,$idOrv);

            if($response->getVci() == 'TSY' && $response->getStatus() == "AUTHORIZED"){  
                return redirect('http://localhost:4200/#/cliente/exitoso?orvNumRea='.$orvNumRea);
            }else{
                return redirect('http://localhost:4200/#/cliente/error?orvNumRea='.$orvNumRea);
            }
        }
    }

    public function transbankRep(Request $request){
        $token     = $request['token_ws'];
        $orvNumRea = $request['orvNumRea'];
        $idOrv     = $request['idOrv'];
        $response = (new Transaction)->status($token);
        $xstatus   = OrdenVentaController::statusT($token , $response , $idOrv);        
        if($response->getStatus() == "INITIALIZED"){
            try{
                $affected = OrdenVenta::select('orvPrecioPag')
                ->where('idOrv' , $idOrv)        
                ->where('orvNumRea', $orvNumRea)
                ->get();

                if(sizeof($affected)){
                    $amount      =  $affected[0]['orvPrecioPag'];
                    OrdenVentaController::refoundT($token , $amount ,$idOrv);                    
                  return redirect('http://localhost:4200/#/cliente/reintento?orvNumRea='.$orvNumRea);
                }

            }catch(Exception $ex){
                $response = (new Transaction)->status($token);
                OrdenVentaController::statusT($token, $response,$idOrv);
                return redirect('http://localhost:4200/#/cliente/reintento?orvNumRea='.$orvNumRea);                
            }
        }else{

            if($response->getVci() == 'TSY' && $response->getStatus() == "AUTHORIZED"){  
                OrdenVentaController::statusT($token, $response,$idOrv);
                return redirect('http://localhost:4200/#/cliente/exitoso?orvNumRea='.$orvNumRea);
            }else{
                OrdenVentaController::statusT($token, $response,$idOrv);
                return redirect('http://localhost:4200/#/cliente/error?orvNumRea='.$orvNumRea);
            }
               
        }

    }

     function statusT($token , $response , $idOrv){
        
        $json = [
            "vci"               => $response->getVci(),
            "amount"            => $response->getAmount(),
            "status"            => $response->getStatus(),
            "buyOrder"          => $response->getBuyOrder(),
            "sessionId"         => $response->getSessionId(),
            "cardDetail"        => $response->getCardDetail(),
            "accountingDate"    => $response->getAccountingDate(),
            "transactionDate"   => $response->getTransactionDate(),
            "authorizationCode" => $response->getAuthorizationCode(),
            "paymentTypeCode"   => $response->getPaymentTypeCode(),
            "responseCode"      => $response->getResponseCode(),
            "installmentsAmount"=> $response->getInstallmentsAmount(),
            "installmentsNumber"=> $response->getInstallmentsNumber(),
            "balance"           => $response->getBalance(),
        ];

        $xaffected = TranHub::create([
            'idOrv'   =>  $idOrv,
            'json'    =>  json_encode($json),
            'token_ws'=>  $token,
            'transtip'=> 'status',
            'transtatus'=>  $response->getStatus(),
            'empId'   =>  1
        ]);  
        
        return true;
    }   

    public function refoundT($token , $amount, $idOrv){
        return "auqi";
    }
    
    function statusTransbank(Request $request){
        
        $token  = $request['token'];    
        $response = (new Transaction)->status($token);
        $json = [
            "vci"               => $response->getVci(),
            "amount"            => $response->getAmount(),
            "status"            => $response->getStatus(),
            "buyOrder"          => $response->getBuyOrder(),
            "sessionId"         => $response->getSessionId(),
            "cardDetail"        => $response->getCardDetail(),
            "accountingDate"    => $response->getAccountingDate(),
            "transactionDate"   => $response->getTransactionDate(),
            "authorizationCode" => $response->getAuthorizationCode(),
            "paymentTypeCode"   => $response->getPaymentTypeCode(),
            "responseCode"      => $response->getResponseCode(),
            "installmentsAmount"=> $response->getInstallmentsAmount(),
            "installmentsNumber"=> $response->getInstallmentsNumber(),
            "balance"           => $response->getBalance(),
        ];

        return response()->json($json, 200);
    }
}
