<?php

use App\Http\Controllers\Oms\VentaWebController;
use Illuminate\Support\Facades\Route;

 Route::get('trabVentaWeb'      , [VentaWebController::class,'index']);
 Route::get('venta_det'         , [VentaWebController::class,'lineas_pedidos']);
 //Route::post('updTipPag'     , [TipoPagoController::class,'update']);
 //Route::post('insTipPag'     , [TipoPagoController::class,'ins']);
 //Route::post('delTipPag'     , [TipoPagoController::class,'del']);

?>