<?php

use App\Http\Controllers\LogController;
use App\Http\Controllers\Seguridad\RolesController;
use App\Http\Controllers\Seguridad\MenuController;
use App\Http\Controllers\Seguridad\ModuloController;
use App\Http\Controllers\Seguridad\NotificacionesController;
use App\Http\Controllers\Seguridad\RolesModuloController;
use App\Http\Controllers\Seguridad\UserController;
use App\Http\Controllers\Seguridad\subOpcionesController;
use Illuminate\Support\Facades\Route;

    //USUARIO
    Route::get('trabUsuarios'   , [UserController::class,'trabUsuarios']);
    Route::get('valUsuario'     , [UserController::class,'valUsuario']);
    Route::post('upUsuario'     , [UserController::class,'up']);

    Route::get ('getUsuario'    , [UserController::class,'getUser']);
    Route::post('setUserSession', [UserController::class,'setUserSession']);   
    Route::get('getUsuarios'    , [UserController::class,'getUsuarios']);
    Route::post('upUsuario2'    , [UserController::class,'upUsuario2']);
    Route::post('insUser'       , [UserController::class,'ins_Users']);
    Route::post('reiniciar'     , [UserController::class,'reiniciar']);
    Route::post('deshabilitar'  , [UserController::class,'deshabilitar']);
    Route::post('habilitar'     , [UserController::class,'habilitar']);
    Route::post('cambiarPassword', [UserController::class,'cambiarPassword']);

    //Menu
    Route::get('usuarioMenu'    , [MenuController::class, 'index'] );    
    Route::get('trabModule'     , [ModuloController::class, 'index']);  
    Route::post('insModulo'     , [RolesModuloController::class, 'ins']);  
    Route::post('upModulo'      , [RolesModuloController::class, 'up']);   
    Route::post('delModulo'     , [RolesModuloController::class, 'del']);       
    Route::get('snAsig'         , [RolesModuloController::class, 'moduleSnAsig']);
    Route::get('asig'           , [RolesModuloController::class, 'moduleAsig']);
    Route::get('menuAsig'       , [RolesModuloController::class, 'menuAsig']);
    
    //Menu subopciones
    Route::get('trabsubopc'    , [subOpcionesController::class, 'index'] );
    Route::get('snAsigOpt'     , [subOpcionesController::class, 'sinAsig']); 
    Route::get('asigOpt'       , [subOpcionesController::class, 'asig']); 
    Route::post('insSubOpc'    , [subOpcionesController::class,'ins']);
    Route::post('delSubOpc'    , [subOpcionesController::class, 'del']);
    
    //Route::get('getUser'      , 'App\Http\Controllers\UserController@setUserSession');

    //Roles
    Route::get('trabRoles'      , [RolesController::class,'index']);
    Route::post('updRoles'      , [RolesController::class,'update']);
    Route::post('insRoles'      , [RolesController::class,'ins']);
    Route::post('delRoles'      , [RolesController::class,'del']);
    Route::get('snAsigRol'      , [RolesController::class, 'rolSnAsig']);
    Route::get('asigRol'        , [RolesController::class, 'rolAsig']);

    //Trabajar etapas log
  /*  Route::get('trabEtapasDet'  , 'App\Http\Controllers\EtapasDesController@index');
    Route::post('insEtapasDet'  , 'App\Http\Controllers\EtapasDesController@ins');
    Route::post('delEtapasDet'  , 'App\Http\Controllers\EtapasDesController@del');
    Route::post('updEtapasDet'  , 'App\Http\Controllers\EtapasDesController@update');*/


    //Log sistema
    Route::get('trabLogSys'     , [LogController::class, 'index']);
    Route::post('insLogSys'     , [LogController::class, 'ins']);

       //Notificaciones
  /*     Route::get('trabNot'          , [NotificacionesController::class,'index']);
       Route::get('notificaciones'   , [NotificacionesController::class,'notificaciones']);
       Route::get('notcontador'      , [NotificacionesController::class,'notcontador']);
       Route::post('lecturaNot'      , [NotificacionesController::class,'lecturaNot']);
       Route::post('lecturaNotAll'   , [NotificacionesController::class,'lecturaNotAll']);
    */
      

?>