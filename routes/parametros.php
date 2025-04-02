<?php

use App\Http\Controllers\Parametros\CalendarioJulController;
use App\Http\Controllers\Parametros\CiudadController;
use App\Http\Controllers\Parametros\ColorController;
use App\Http\Controllers\Parametros\ComunaController;
use App\Http\Controllers\Parametros\EtapasController;
use App\Http\Controllers\Parametros\GerenciaController;
use App\Http\Controllers\Parametros\GrupoController;
use App\Http\Controllers\Parametros\MaquinasController;
use App\Http\Controllers\Parametros\MonedaController;
use App\Http\Controllers\Parametros\MovRechazoController;
use App\Http\Controllers\Parametros\PaisController;
use App\Http\Controllers\Parametros\ProveedorController;
use App\Http\Controllers\Parametros\PrvDirController;
use App\Http\Controllers\Parametros\RegionController;
use App\Http\Controllers\Parametros\SubGrupoController;
use App\Http\Controllers\Parametros\TipoPagoController;
use App\Http\Controllers\Parametros\UnidadMedidaController;
use App\Http\Controllers\Parametros\ProductoController;
use App\Http\Controllers\Parametros\TallaController;
use Illuminate\Support\Facades\Route;

      
    //Gerencia
    Route::get('trabGerencia'   , [GerenciaController::class,'index']);
    Route::post('updGerencia'   , [GerenciaController::class,'update']);
    Route::post('insGerencia'   , [GerenciaController::class,'ins']);
    Route::post('delGerencia'   , [GerenciaController::class,'del']);
    //Tipo de pago
    Route::get('trabTipPag'     , [TipoPagoController::class,'index']);
    Route::post('updTipPag'     , [TipoPagoController::class,'update']);
    Route::post('insTipPag'     , [TipoPagoController::class,'ins']);
    Route::post('delTipPag'     , [TipoPagoController::class,'del']);
    //Pais
    Route::get('trabPais'       , [PaisController::class,'index']);
    Route::post('insPais'       , [PaisController::class,'ins']);
    Route::post('delPais'       , [PaisController::class,'del']);
    Route::post('updPais'       , [PaisController::class,'update']);
    Route::get('valCodPai'      , [PaisController::class,'valCodPai']);

    //Region
    Route::get('trabRegion'        , [RegionController::class,'index']);
    Route::post('insRegion'        , [RegionController::class,'ins']);
    Route::post('delRegion'        , [RegionController::class,'del']);
    Route::post('updRegion'        , [RegionController::class,'update']);
    Route::get('valCodReg'         , [RegionController::class,'valCodReg']);
    Route::get('regPai'            , [RegionController::class,'indexFil']);
    Route::post('cargaMasivaReg'   , [RegionController::class,'cargaMasiva']);
    //Ciudad
    Route::get('trabCiudad'     , [CiudadController::class,'index']);
    Route::post('insCiudad'     , [CiudadController::class,'ins']);
    Route::post('delCiudad'     , [CiudadController::class,'del']);
    Route::post('updCiudad'     , [CiudadController::class,'update']);
    Route::get('valCodCiudad'   , [CiudadController::class,'valCodCiudad']);
    Route::get('regCiu'         , [CiudadController::class,'indexFil']);
  //Comuna
    Route::get('trabComuna'     , [ComunaController::class,'index']);
    Route::post('insComuna'     , [ComunaController::class,'ins']);
    Route::post('delComuna'     , [ComunaController::class,'del']);
    Route::post('updComuna'     , [ComunaController::class,'update']);
    Route::get('valCodComuna'   , [ComunaController::class,'valCodComuna']);
    Route::get('ciuCom'         , [ComunaController::class,'indexFil']);

    //Proveedor
    Route::get('trabProveedor'   , [ProveedorController::class,'index']);
    Route::post('insProveedor'   , [ProveedorController::class,'ins']);
    Route::get('valPrvRut'       , [ProveedorController::class,'valPrvRut']);
    Route::get('datPrv'          , [ProveedorController::class,'datPrv']);
    Route::post('updProveedor'   , [ProveedorController::class,'update']);
    Route::get('selCliente'      , [ProveedorController::class,'selCliente']);

    //Proveedor - Sucursal
    Route::post('insPrvDes'      , [PrvDirController::class,'ins']);
    Route::get('trabPrvDir'      , [PrvDirController::class,'index']);
    Route::post('delPrvDir'      , [PrvDirController::class,'del']);
    //Moneda
    Route::get('trabMoneda'      , [MonedaController::class,'index']);
    Route::post('insMoneda'      , [MonedaController::class,'ins']);
    Route::post('delMoneda'      , [MonedaController::class,'del']);
    Route::post('updMoneda'      , [MonedaController::class,'update']);
    Route::get('valMonCod'       , [MonedaController::class,'valMonCod']);
    //Indicadores
    Route::get('indicadores'      , [MonedaController::class,'indicadores']);   
    //Color
    Route::get('trabColor'       , [ColorController::class,'index']);
    Route::post('insColor'       , [ColorController::class,'ins']);
    Route::post('delColor'       , [ColorController::class,'del']);
    Route::post('updColor'       , [ColorController::class,'update']);
    Route::get('valColCod'       , [ColorController::class,'valColCod']);
    Route::get('colorInfo'       , [ColorController::class,'colorInfo']);
    //Unidad
    Route::get('trabUnidad'      , [UnidadMedidaController::class,'index']);
    Route::post('insUnidad'      , [UnidadMedidaController::class,'ins']);
    Route::post('delUnidad'      , [UnidadMedidaController::class,'del']);
    Route::post('updUnidad'      , [UnidadMedidaController::class,'update']);
    Route::get('valUnCod'        , [UnidadMedidaController::class,'valUnCod']);
    //Motivo
    Route::get('trabMotivo'      , [MovRechazoController::class,'index']);
    Route::get('motEta'          , [MovRechazoController::class,'index1']);
    Route::post('insMotivo'      , [MovRechazoController::class,'ins']);
    Route::post('delMotivo'      , [MovRechazoController::class,'del']);
    Route::post('updMotivo'      , [MovRechazoController::class,'update']);
    //Grupo
    Route::get('trabGrupo'       , [GrupoController::class,'index']);
    Route::post('insGrupo'       , [GrupoController::class,'ins']);
    Route::post('delGrupo'       , [GrupoController::class,'del']);
    Route::post('updGrupo'       , [GrupoController::class,'update']);
    Route::get('valGrpCod'       , [GrupoController::class,'valGrpCod']);
    //Sub Grupo
    Route::get('trabSubGrupo'    , [SubGrupoController::class,'index']);
    Route::post('insSubGrupo'    , [SubGrupoController::class,'ins']);
    Route::post('delSubGrupo'    , [SubGrupoController::class,'del']);
    Route::post('updSubGrupo'    , [SubGrupoController::class,'update']);
    Route::get('valCodSubGrp'    , [SubGrupoController::class,'valCodSubGrp']);
    Route::get('subGrp'          , [SubGrupoController::class,'indexFil']);
    
    //Producto
    Route::get('trabProducto'    ,  [ProductoController::class,'index']);
    Route::post('insProducto'    ,  [ProductoController::class,'ins']);
    Route::post('delProducto'    ,  [ProductoController::class,'del']);
    Route::post('updProducto'    ,  [ProductoController::class,'update']);
    Route::get('valPrdCod'       ,  [ProductoController::class,'valCodPrd']);
    Route::get('valPrdEan'       ,  [ProductoController::class,'valEanPrd']);
    Route::get('filPrdDes'       ,  [ProductoController::class,'filPrdDes']);
    Route::get('prdDes'          ,  [ProductoController::class,'prdDes']);
    Route::get('filPrdCod'       ,  [ProductoController::class,'filPrdCod']);
    Route::get('datPrd'          ,  [ProductoController::class,'datPrd']);
    Route::get('datPrdMtP'       ,  [ProductoController::class,'datPrdMtP']);
    Route::get('prod'            ,  [ProductoController::class,'prod']);
    

    //Maquinas
    Route::get('trabMaquinas'    , [MaquinasController::class,'index']);
    Route::post('insMaquinas'    , [MaquinasController::class,'ins']);
    Route::post('delMaquinas'    , [MaquinasController::class,'del']);
    Route::post('updMaquinas'    , [MaquinasController::class,'update']);
    Route::get('filEta'          , [MaquinasController::class,'filEta']);
    //Calendario Juliano
    Route::get('trabCalJul'     , [CalendarioJulController::class,'index']);
    Route::post('insCalJul'     , [CalendarioJulController::class,'ins']);
    Route::post('delCalJul'     , [CalendarioJulController::class,'del']);
    Route::get('busUltAno'      , [CalendarioJulController::class,'busUltAno']);
    Route::get('valCal'         , [CalendarioJulController::class,'valCal']);
    Route::get('diaJul'         , [CalendarioJulController::class,'diaJul']);
    //Producto Equivalencia
  /*  Route::get('trabPrdEqui'    , 'App\Http\Controllers\EquivalenciaPrdController@index');
    Route::post('valEqui'       , 'App\Http\Controllers\EquivalenciaPrdController@valEqui');
    Route::post('insEquival'    , 'App\Http\Controllers\EquivalenciaPrdController@ins');
    Route::get('valEquiPrd'     , 'App\Http\Controllers\EquivalenciaPrdController@valPrd');
    //Bins 
    Route::get('trabBins'       , 'App\Http\Controllers\BinController@index');
    Route::post('upBins'        , 'App\Http\Controllers\BinController@update');
    Route::get('verBins'        , 'App\Http\Controllers\BinController@verHist');*/

      //Etapas
      Route::get('trabEtapas'     , [EtapasController ::class,'index']);
      Route::get('etapasProd'     , [EtapasController ::class,'index1']);
      Route::post('insEtapas'     , [EtapasController ::class,'ins']);
      Route::post('delEtapas'     , [EtapasController ::class,'del']);
      Route::post('updEtapas'     , [EtapasController ::class,'up']);

     //Color
     Route::get('trabTalla'       , [TallaController::class,'index']);
     Route::post('insTalla'       , [TallaController::class,'ins']);
     Route::post('delTalla'       , [TallaController::class,'del']);
     Route::post('updTalla'       , [TallaController::class,'update']);
     Route::get('valTalla'        , [TallaController::class,'valColCod']);

?>