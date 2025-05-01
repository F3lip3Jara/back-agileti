<?php

namespace App\Http\Controllers\Sd;

use App\Http\Controllers\Controller;
use App\Jobs\GenerarPdfJob;
use App\Models\Sd\PdfOrden;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PdfOrdenController extends Controller
{
    public function generar(Request $request)
    {
        $ordenIds = $request->input('orden_ids');

        // Validar que orden_ids sea un array y no esté vacío
        if (!is_array($ordenIds) || empty($ordenIds)) {
            Log::error('Datos inválidos recibidos', [
                'orden_ids' => $ordenIds,
                'request_data' => $request->all()
            ]);
            return response()->json([
                'error' => 'Los IDs de órdenes son inválidos o están vacíos'
            ], 400);
        }

        // Validar que todos los IDs sean números
        foreach ($ordenIds as $id) {
            if (!is_numeric($id)) {
                Log::error('ID de orden inválido', [
                    'id' => $id,
                    'orden_ids' => $ordenIds
                ]);
                return response()->json([
                    'error' => 'Los IDs de órdenes deben ser números'
                ], 400);
            }
        }

        Log::info('Creando solicitud de PDF', ['orden_ids' => $ordenIds]);

        $pdfRequest = PdfOrden::create([
            'orden_ids' => json_encode($ordenIds),
            'status' => 'pending',
        ]);

        Log::info('Solicitud de PDF creada', ['request_id' => $pdfRequest->id]);

        GenerarPdfJob::dispatch($pdfRequest->id);

        return response()->json(['request_id' => $pdfRequest->id]);
    }

    public function status(Request $request)
    {
        $jobId = $request->input('jobId');
        $pdfRequest = PdfOrden::where('id', $jobId)->first();

        return response()->json([
            'status' => $pdfRequest->status,
            'file_url' => $pdfRequest->file_path ? url($pdfRequest->file_path) : null,
            'orden_ids' => $pdfRequest->orden_ids
        ]);
    }
}
