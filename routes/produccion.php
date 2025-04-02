<?php

use App\Http\Controllers\Produccion\OrdenProdController;
use Illuminate\Support\Facades\Route;


	Route::get('trabOrdenProduccion'        ,[OrdenProdController::class,'index']);
   Route::post('insOrdProduccion'          ,[OrdenProdController::class,'ins']);	
   Route::post('updOrdProduccion'          ,[OrdenProdController::class,'update']);	
   Route::get('OrdPDet'                    ,[OrdenProdController::class,'OrdPDet']);	
   Route::get('empresafilPdf'                 ,[OrdenProdController::class,'empresafilPdf']);	
   /* Route::post('insOT'          ,[OrdenTrabController::class,'ins']);
    Route::get('trabOt'          ,[OrdenTrabController::class,'index']);
    Route::get('verOtIny'        ,[OrdenTrabController::class,'verOtIny']);
    Route::get('verOtTer'        ,[OrdenTrabController::class,'verOtTer']);
    Route::get('verOtImp'        ,[OrdenTrabController::class,'verOtImp']);
    Route::post('insOTT'         ,[OrdenTrabController::class,'insOTT']);
    Route::post('AprOt'          ,[OrdenTrabController::class,'AprOt']);*/
 /*   
    //Termoformado
    Route::get('trabOtTermo'     , 'App\Http\Controllers\OrdenTrabController@indexTermo');
    Route::get('trabOtTermofil'  , 'App\Http\Controllers\OrdenTrabController@indexTermoFil');
    Route::get('termoformado'    , 'App\Http\Controllers\TermoformadoController@indexfil');  
    //Route::get('filotNumRea'     , 'App\Http\Controllers\OrdenTrabController@filopNumRea');
    Route::post('insTerm'        , 'App\Http\Controllers\TermoformadoController@ins');
    Route::post('insTermCierre'  , 'App\Http\Controllers\TermoformadoController@insTermCierre');
    Route::post('insTermCierreC' , 'App\Http\Controllers\TermoformadoController@insTermCierreC');
    Route::post('uploadArTer'    , 'App\Http\Controllers\TermoformadoController@insTermArcv');
    Route::post('delTermArcv'    , 'App\Http\Controllers\TermoformadoController@delArcv');
    Route::get('getTermArchivo'  , 'App\Http\Controllers\TermoformadoController@downloadFileTerm');
    Route::post('insTermDet'     , 'App\Http\Controllers\TermoformadoController@insTermDet');
    Route::post('delTermDes'     , 'App\Http\Controllers\TermoformadoController@delTermDes');
    Route::post('termPallet'     , 'App\Http\Controllers\TermoformadoController@termPallet');
    Route::post('termConf'       , 'App\Http\Controllers\TermoformadoController@termConf');
    Route::post('termRechazo'    , 'App\Http\Controllers\TermoformadoController@termRechazo');
    //Envasado
    Route::post('insEnv'         , 'App\Http\Controllers\EnvasadoController@ins');
    Route::post('upEnv'          , 'App\Http\Controllers\EnvasadoController@up');
    Route::post('insEnvDet'      , 'App\Http\Controllers\EnvasadoController@insEnvDet');
    Route::post('envConf'        , 'App\Http\Controllers\EnvasadoController@envConf');
    Route::post('envRechazo'     , 'App\Http\Controllers\EnvasadoController@envRechazo');
    Route::post('upEnvC'         , 'App\Http\Controllers\EnvasadoController@upEnvC');
    Route::get('valEnv'          , 'App\Http\Controllers\EnvasadoController@valEnv');
    Route::get('trabOtEnv'       , 'App\Http\Controllers\OrdenTrabController@indexEnvasado');  
    Route::get('envDet '         , 'App\Http\Controllers\EnvasadoController@envDet');  
    Route::post('uploadArEnv'    , 'App\Http\Controllers\EnvasadoController@uploadArEnv');
    Route::post('delEnvArcv'     , 'App\Http\Controllers\EnvasadoController@delArcv');

    //Inyección
    Route::get('trabOtInye'      , 'App\Http\Controllers\OrdenTrabController@indexInyeccion');
    Route::post('insIny'         , 'App\Http\Controllers\InyeccionController@ins');
    Route::post('insInyCierre'   , 'App\Http\Controllers\InyeccionController@insInyCierre');
    Route::get('inyeccion'       , 'App\Http\Controllers\InyeccionController@indexfil'); 
    Route::post('uploadArIny'    , 'App\Http\Controllers\InyeccionController@insInyArcv'); 
    Route::post('delInyArcv'     , 'App\Http\Controllers\InyeccionController@delArcv');
    Route::post('insInyCierreC'  , 'App\Http\Controllers\InyeccionController@insTermCierreC');
    Route::post('inyConf'        , 'App\Http\Controllers\InyeccionController@inyConf');
    Route::post('inyRechazo'     , 'App\Http\Controllers\InyeccionController@inyRechazo');

    //Extusion
    Route::get('trabExtrusion'  , 'App\Http\Controllers\ExtrusionController@index');
    Route::post('insExtrusion'  , 'App\Http\Controllers\ExtrusionController@ins');
    Route::post('rechaExtru'    , 'App\Http\Controllers\ExtrusionController@rechaExtru');
    Route::post('insConfirma'   , 'App\Http\Controllers\ExtrusionController@insConfirma');
    Route::post('insConfirmaO'  , 'App\Http\Controllers\ExtrusionController@insConfirmaO');
    Route::post('insConfirmaC'  , 'App\Http\Controllers\ExtrusionController@insConfirmaC');
    Route::post('confExtru'     ,  'App\Http\Controllers\ExtrusionController@confExtru');
    Route::post('confExtruO'    ,  'App\Http\Controllers\ExtrusionController@confExtruO');
    Route::get('filLotSalExt'   , 'App\Http\Controllers\ExtrusionController@filLotSal');
    Route::get('extDet'         , 'App\Http\Controllers\ExtrusionController@extDet');
    Route::get('indexFil'       , 'App\Http\Controllers\ExtrusionController@indexFil');
    Route::get('extruDis'       , 'App\Http\Controllers\ExtrusionController@extruDis');

    //Impresion
    Route::get('trabOtImp'       , 'App\Http\Controllers\OrdenTrabController@indexImpresion');
    Route::post('insImp'         , 'App\Http\Controllers\ImpresionController@ins');;
    Route::post('insImpCierre'   , 'App\Http\Controllers\ImpresionController@insImpCierre');;
    Route::get('impresion'       , 'App\Http\Controllers\ImpresionController@indexfil'); 
    Route::post('insImpCierreC'  , 'App\Http\Controllers\ImpresionController@insImpCierreC');
    Route::post('impConf'        , 'App\Http\Controllers\ImpresionController@impConf');
    Route::post('impRechazo'     , 'App\Http\Controllers\ImpresionController@impRechazo');
    
    //Archivos
    Route::get('archivos'         ,'App\Http\Controllers\ArchivosController@index');*/

    ?>