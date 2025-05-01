<?php

namespace App\Http\Controllers\Parametros;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Http\Request;

class FeriadoController extends Controller
{
    public function verificarFeriado()
    {
        try {
            $client = new Client([
                'verify' => false,
                'timeout' => 30,
                'connect_timeout' => 30
            ]);

            $response = $client->request('GET', 'https://api.boostr.cl/holidays.json', [
                'headers' => [
                    'accept' => 'application/json'
                ]
            ]);

            $feriados = json_decode($response->getBody(), true);
            $hoy = Carbon::now('America/Santiago')->format('Y-m-d');

            // Verificar si hoy es feriado
            $esFeriado = false;
            foreach ($feriados as $feriado) {
                if ($feriado['date'] === $hoy) {
                    $esFeriado = true;
                    break;
                }
            }

            if ($esFeriado) {
                // Si es feriado, obtener el último día registrado
                $ultimoDia = Carbon::now('America/Santiago')->subDay();
                while ($ultimoDia->isWeekend()) {
                    $ultimoDia->subDay();
                }

                return response()->json([
                    'esFeriado' => true,
                    'fecha' => $hoy,
                    'ultimoDiaRegistrado' => $ultimoDia->format('Y-m-d')
                ]);
            }

            return response()->json([
                'esFeriado' => false,
                'fecha' => $hoy
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'mensaje' => 'Error al consultar API de feriados: ' . $e->getMessage()
            ], 500);
        }
    }
} 