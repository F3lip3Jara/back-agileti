<?php


use App\Http\Controllers\Parametros\ComunaController;
use App\Http\Controllers\Parametros\ProductoController;
use App\Http\Controllers\Sd\SdOrdController;
use App\Http\Controllers\Seguridad\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::post('logPda', [UserController::class, 'authenticateUserPda'] );
Route::post('log'   , [UserController::class, 'authenticateUser'] );
Route::middleware(['App\Http\Middleware\postMiddleware'])->group(function () {
  require __DIR__ . '/seguridad.php';
  require __DIR__ . '/parametros.php';
  require __DIR__ . '/produccion.php';
  require __DIR__ . '/sd.php';
  require __DIR__ . '/ventas.php';
  require __DIR__ . '/pda.php';
  //require __DIR__ . '/ventas.php';
  

});

Route::middleware(['App\Http\Middleware\sysAdmin'])->group(function () {
  require __DIR__ . '/administracion.php';
});


//PAGO DE CLIENTE
Route::middleware(['App\Http\Middleware\webPayMiddleware'])->group(function () {
 /* Route::get('ordenventa'     , [OrdenVentaController::class,'indexPago']);
  Route::post('transbank'     , [OrdenVentaController::class,'transbank']);
  Route::post('transbankRe'   , [OrdenVentaController::class,'transbankRep']);
  Route::get('transbankRe'    , [OrdenVentaController::class,'transbankRe']);
  Route::get('statusTransbank', [OrdenVentaController::class,'statusTransbank']);*/
 
});


require __DIR__ . '/weebhooksOms.php';

Route::get('comuna'       , [ComunaController::class,'index']);



?>