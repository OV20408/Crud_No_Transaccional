<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Evaluacion;
use App\Models\Reporte;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EvaluacionApiController extends Controller
{
    /**
     * Asignar universidad a la evaluación asociada con el reporte
     *
     * @param Request $request
     * @param int $reporteId
     * @return \Illuminate\Http\JsonResponse
     */
    public function asignarUniversidad(Request $request, $reporteId)
    {
        try {
            $request->validate([
                'universidad_id' => 'required|exists:universidad,id'
            ]);

            // Buscar la evaluación asociada al reporte
            $evaluacion = Evaluacion::where('id_reporte', $reporteId)->first();

            if (!$evaluacion) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se encontró una evaluación asociada a este reporte'
                ], 404);
            }

            // Actualizar la universidad
            $evaluacion->id_universidad = $request->universidad_id;
            $evaluacion->save();

            Log::info('Universidad asignada a evaluación', [
                'evaluacion_id' => $evaluacion->id,
                'reporte_id' => $reporteId,
                'universidad_id' => $request->universidad_id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Universidad asignada correctamente',
                'data' => [
                    'evaluacion_id' => $evaluacion->id,
                    'universidad_id' => $evaluacion->id_universidad
                ]
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error al asignar universidad', [
                'reporte_id' => $reporteId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al asignar universidad: ' . $e->getMessage()
            ], 500);
        }
    }
}
