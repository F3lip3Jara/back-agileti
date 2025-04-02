<?php

use App\Http\Controllers\Oms\WebhookController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
/*
Route::post('weebhooks_oms_wooecommerce', function(Request $request){
    return response()->json($request->session(), 200);
});*/

Route::post('weebhooks_oms_wooecommerce' , [WebhookController::class,'ins']);
//Route::get('product'                     , [WebhookController::class,'product']);
//Route::get('carro_stock'                 , [WebhookController::class,'carro']);
Route::get('product_web'                 , [WebhookController::class,'product_web']);
Route::get('product_web_fav'             , [WebhookController::class,'product_web']);
Route::get('product_web_des'             , [WebhookController::class,'product_web']);
//Route::get('webhooks_oms'                , [WebhookController::class,'webHooks']);
?>