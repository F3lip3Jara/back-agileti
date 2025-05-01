<?php

namespace App\Jobs;

use App\Models\Sd\Almacen;
use App\Models\Sd\PdfOrden;
use App\Models\Sd\SdOrden;
use App\Models\Sd\SdOrdenDet;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;
use Milon\Barcode\DNS1D;

class GenerarPdfJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public $requestId) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $request = PdfOrden::find($this->requestId);
        PdfOrden::where('id', $this->requestId)->update(['status' => 'processing']);
        
        try {
            $ordenIds = json_decode($request->orden_ids, true);
            
            // Validar que $ordenIds sea un array y no esté vacío
            if (!is_array($ordenIds) || empty($ordenIds)) {
                throw new \Exception('Los IDs de órdenes no son válidos');
            }

            $ordenes = SdOrden::whereIn('ordId', $ordenIds)->get();
            
            if ($ordenes->isEmpty()) {
                throw new \Exception('No se encontraron órdenes con los IDs proporcionados');
            }

            $data = [];
            $barcode = new DNS1D();
            $barcode->setStorPath(storage_path('app/public/barcodes'));
            
            foreach ($ordenes as $orden) {
                $ordenesdet = SdOrdenDet::where('ordId', $orden->ordId)->get();
                $almacen = Almacen::where('almId', $orden->almId)->first();
                
                $productos = $ordenesdet->map(function($prod) use ($barcode) {
                    return [
                        'sku' => $prod->ordDtlCustShortText1,
                        'nombre' => $prod->ordDtlCustShortText2,
                        'cantidad' => $prod->orddQtySol,
                        'barcode' => $barcode->getBarcodePNG($prod->ordDtlCustShortText1, 'C128', 2, 60)
                    ];
                });

                $data[] = [
                    'orden' => [
                        'codigo' => $orden->ordNumber,
                        'fecha_entrega' => $orden->ordHdrCustShortText5,
                        'almacen_destino' => $almacen->almDes,
                        'cliente' => [
                            'nombre' => $orden->ordHdrCustShortText3
                        ]
                    ],
                    'barcodeOt' => $barcode->getBarcodePNG($orden->ordNumber, 'C128', 2, 60),
                    'productos' => $productos
                ];
            }

            Log::info('Generando PDF para órdenes', ['ordenes_count' => count($data)]);
            
            $pdf = Pdf::loadView('pdf.ordenes', ['ordenes' => $data]);
            $fileName = 'ordenes_' . time() . '.pdf';
            $filePath = 'pdfs/' . $fileName;

            Log::info('Guardando PDF', ['file_path' => $filePath]);
            
            Storage::disk('public')->put('' . $filePath, $pdf->output());

            $request->update([
                'status' => 'completed',
                'file_path' => 'storage/' . $filePath,
            ]);
            
            Log::info('PDF generado exitosamente', ['request_id' => $this->requestId]);
        } catch (\Exception $e) {
            Log::error('Error al generar PDF', [
                'request_id' => $this->requestId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            PdfOrden::where('id', $this->requestId)->update([
                'status' => 'failed'
            ]);
        }
    }
}
