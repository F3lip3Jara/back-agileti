<?php

use App\Http\Controllers\Oms\WebhookController;
use App\Http\Controllers\Parametros\ComunaController;
use App\Http\Controllers\Parametros\MonedaController;
use App\Http\Controllers\Seguridad\TotpController;
use App\Http\Controllers\Seguridad\UserController;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
  return view('welcome');
});

Route::post('logPda', [UserController::class, 'authenticateUserPda']);
Route::post('log', [UserController::class, 'authenticateUser']);

// Rutas TOTP
Route::prefix('totp')->group(function () {
  Route::post('/setup', [TotpController::class, 'setup']);
  Route::post('/verify', [TotpController::class, 'verify']);
  Route::post('/status', [TotpController::class, 'status']);
  Route::post('/disable', [TotpController::class, 'disable']);
});



Route::middleware(['App\Http\Middleware\postMiddleware'])->group(function () {
  require __DIR__ . '/seguridad.php';
  require __DIR__ . '/parametros.php';
  require __DIR__ . '/produccion.php';
  require __DIR__ . '/sd.php';
  require __DIR__ . '/ventas.php';
  require __DIR__ . '/pda.php';
  require __DIR__ . '/gym.php';
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

Route::get('comuna', [ComunaController::class, 'index']);
Route::post('estadoOrden', [WebhookController::class, 'estadoOrden']);
Route::get('valEtiqeta', [ComunaController::class, 'valEtiqeta']);

Route::get('dolar', [MonedaController::class, 'dolar']);


Route::get('uf', [MonedaController::class, 'uf']);


Route::get('pruebaTokenMV', function () {

  try {

    $client = new Client([
      'verify' => false,
      'timeout' => 30,
      'connect_timeout' => 30
    ]);

    $response = $client->request('POST', 'https://app.multivende.com/oauth/access-token', [
      'headers' => [
        'accept' => 'application/json'
      ],
      'body' => json_encode([
        "client_id" => env("MVID"),
        "client_secret" => env("MVSECRET"),
        "grant_type" => "authorization_code",
        "code" => env("MVCODE")
      ])
    ]);

    return json_decode($response->getBody()->getContents());
  } catch (Exception $e) {
    return $e;
  }
});
