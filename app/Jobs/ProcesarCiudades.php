<?php

namespace App\Jobs;

use App\Models\Parametros\Ciudad;
use App\Models\Parametros\Comuna;
use App\Models\Parametros\RegionCargaControl;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcesarCiudades implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $regCargaId;

    public function __construct($regCargaId)
    {
        $this->regCargaId = $regCargaId;
    }

    public function handle(): void
    {
        try {
            $control = RegionCargaControl::find($this->regCargaId);
            if (!$control) {
                throw new \Exception('No se encontró el registro de control');
            }

            $data = json_decode($control->regCargaError, true);
            if (!$data || !isset($data['regId']) || !isset($data['cities'])) {
                throw new \Exception('Datos de ciudades no válidos');
            }

            $regId = $data['regId'];
            $cities = $data['cities'];

            foreach ($cities as $item) {
                try {
                    $cod = $item->id;
                    $name = $item->name;
                    $communes = $item->communes;

                    $affected = Ciudad::create([
                        'regId' => $regId,
                        'ciuCod' => $cod,
                        'ciuDes' => $name
                    ]);

                    $ciuId = $affected->id;

                    foreach ($communes as $itemCom) {
                        $codCom = $itemCom->id;
                        $nameCom = $itemCom->name;

                        Comuna::create([
                            'ciuId' => $ciuId,
                            'comCod' => $codCom,
                            'comDes' => $nameCom
                        ]);
                    }
                } catch (\Exception $e) {
                    Log::error('Error procesando ciudad: ' . $e->getMessage());
                }
            }

            // Limpiar el campo de error ya que lo usamos para almacenar los datos
            $control->update(['regCargaError' => null]);

        } catch (\Exception $e) {
            Log::error('Error en procesamiento de ciudades: ' . $e->getMessage());
            if (isset($control)) {
                $control->update([
                    'regCargaEst' => 'F',
                    'regCargaError' => $e->getMessage()
                ]);
            }
        }
    }
} 