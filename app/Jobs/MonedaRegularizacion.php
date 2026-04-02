<?php

namespace App\Jobs;

use App\Models\Parametros\Moneda;
use App\Models\Parametros\MonedaConversion;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class MonedaRegularizacion implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */

    public $key;
    public function __construct(private $keyc)
    {
        $this->key = $keyc;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {

        $hoy = Carbon::now('America/Santiago');
        // Si no hay datos, consultamos el histórico
        $anioActual = $hoy->year;
        $anioAnterior = $anioActual - 1;

        $data = Moneda::where('monInt', 'S')->get();

        foreach ($data as $item) {
            try {
                $client = new \GuzzleHttp\Client([
                    'verify' => false,
                    'timeout' => 30,
                    'connect_timeout' => 30
                ]);

                // Consultar datos del año actual
                $urlActual = 'http://api.cmfchile.cl/api-sbifv3/recursos_api/' . $item['monIntVal'] . '/' . $anioActual . '?apikey=' . $this->key . '&formato=json';
                $responseActual = $client->request('GET', $urlActual);
                $datosActual = json_decode($responseActual->getBody(), true);

                // Consultar datos del año anterior
                /* $urlAnterior = 'http://api.cmfchile.cl/api-sbifv3/recursos_api/'.$item['monIntVal'].'/'.$anioAnterior.'?apikey=80e3f542faaf21efc24dd8111aca2eeb7dd28b28&formato=json';
                 $responseAnterior = $client->request('GET', $urlAnterior);
                 $datosAnterior = json_decode($responseAnterior->getBody(), true);*/

                // Procesar y guardar los datos
                $arr = $item['monIntArray'];
                if (isset($datosActual[$arr])) {
                    foreach ($datosActual[$arr] as $valor) {
                        MonedaConversion::updateOrCreate(
                            [
                                'monId' => $item['monId'],
                                'moncFecha' => Carbon::createFromFormat('Y-m-d', $valor['Fecha'])->format('Y-m-d')
                            ],
                            [
                                'moncValor' => str_replace(',', '.', str_replace('.', '', $valor['Valor']))
                            ]
                        );
                    }
                }

                /* if (isset($datosAnterior[$arr])) {
                     foreach($datosAnterior[$arr] as $valor) {
                         MonedaConversion::updateOrCreate(
                             [
                                 'monId' => $item['monId'],
                                 'moncFecha' => Carbon::createFromFormat('Y-m-d', $valor['Fecha'])->format('Y-m-d')
                             ],
                             [
                                 'moncValor' => str_replace(',', '.', str_replace('.', '', $valor['Valor']))
                             ]
                         );
                     }
                 }*/
            } catch (\GuzzleHttp\Exception\ConnectException $e) {
            } catch (\Exception $e) {
            }
        }
    }
}
