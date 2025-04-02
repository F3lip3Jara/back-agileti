<?php

use App\Http\Controllers\Sd\AlmacenController;
use App\Http\Controllers\Sd\CentroController;
use App\Http\Controllers\Sd\ClassTipController;
use App\Http\Controllers\Sd\SdOrdController;
use App\Http\Controllers\Sd\SectorController;
use App\Http\Controllers\Sd\StockController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

 //Centro
 Route::get('trabCentro'       , [CentroController::class,'index']);
 Route::post('insCentro'       , [CentroController::class,'ins']);
 Route::post('delCentro'       , [CentroController::class,'del']);
 Route::post('updCentro'       , [CentroController::class,'update']);


 //Almacen 
 Route::get('trabAlmacen'       , [AlmacenController::class,'index']);
 Route::post('insAlmacen'       , [AlmacenController::class,'ins']);
 Route::post('delAlmacen'       , [AlmacenController::class,'del']);
 Route::post('updAlmacen'       , [AlmacenController::class,'update']);
 Route::get('almacenFil'        , [AlmacenController::class,'indexFil']);
 
 Route::get('trabSdOrden'       , [SdOrdController::class,'index']);
 Route::post('insSdOrden'       , [SdOrdController::class,'ins']);
 Route::get('verSdOrden'        , [SdOrdController::class,'ver']);
 Route::get('pdfOrden'          , [SdOrdController::class, 'pdfOrden']);

 Route::post('insOrdTrasInt'    , [SdOrdController::class,'insOrdTrasInt']);
 Route::get('trabSdClass'       , [ClassTipController::class,'index']);
 Route::post('insSdClass'       , [ClassTipController::class,'ins']);
 Route::post('updSdClass'       , [ClassTipController::class,'update']);

 //Sector
 Route::get('trabSector'        , [SectorController::class,'index']);
 Route::get('sectorFil'         , [SectorController::class,'indexFil']);
 Route::post('insSector'        , [SectorController::class,'ins']);
 Route::post('delSector'        , [SectorController::class,'del']);
 Route::post('updSector'        , [SectorController::class,'update']);

 //Stock
 Route::get('trabSdStock'       , [StockController::class,'index']);

 

?>