<?php

use App\Http\Controllers\Parametros\GerenciaController;
use App\Http\Controllers\Seguridad\RolesController;
use App\Http\Controllers\Seguridad\Administracion\AccionController;
use App\Http\Controllers\Seguridad\Administracion\EmpresaController;
use App\Http\Controllers\Seguridad\Administracion\OpcioneController;
use App\Http\Controllers\Seguridad\UserController;
use Illuminate\Support\Facades\Route;


 //Empresa
 Route::get('empresa'        ,  [EmpresaController::class, 'index']);
 Route::post('insEmpresa'    ,  [EmpresaController::class, 'ins']);
 Route::get('trabEmpresa'    ,  [EmpresaController::class, 'index']);
 Route::post('updEmpresa'    ,  [EmpresaController::class, 'up']);
 Route::get('upImg'          ,  [EmpresaController::class, 'upImg']);
 Route::get('empresafil'     ,  [EmpresaController::class, 'index1']);

 //Opciones
Route::get('trabOpciones'   , [OpcioneController::class, 'index']);   
Route::post('insOpciones'   , [OpcioneController::class, 'ins']);
Route::post('updOpciones'   , [OpcioneController::class, 'up']);
Route::post('delOpciones'   , [OpcioneController::class, 'del']);

//Acciones 
Route::get('trabAcciones'   , [AccionController::class, 'index']);
Route::post('insAcciones'   , [AccionController::class, 'ins']);
Route::post('updAcciones'   , [AccionController::class, 'up']);
Route::post('delAcciones'   , [AccionController::class, 'del']);  

//Empresa - Opciones
Route::get('optsnAsig'         , [EmpresaController::class, 'empOptSnAsig']);
Route::get('optAsig'           , [EmpresaController::class, 'empOptAsig']);
Route::post('insEmpOpt'        , [EmpresaController::class, 'insEmpOpt']);  

//Usuarios
Route::get('trabUsuariosAdm'   , [UserController::class,'trabUsuariosAmd']);
Route::get('trabRolesAdm'      , [RolesController::class,'indexAdm']);
Route::get('trabGerenciaAdm'   , [GerenciaController::class,'indexAdm']);
Route::post('insUserAdm'       , [UserController::class, 'ins_Users']);
Route::get('valUsuarioAdm'     , [UserController::class,'valUsuario']);
Route::post('reiniciarAdm'     , [UserController::class,'reiniciar']);
Route::post('deshabilitarAdm'  , [UserController::class,'deshabilitar']);
Route::post('habilitarAdm'     , [UserController::class,'habilitar']);


?>