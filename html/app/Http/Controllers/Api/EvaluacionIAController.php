<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\IAService;
use App\Models\Reporte;
use App\Models\Evaluacion;
use App\Models\HistorialClinico;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class EvaluacionIAController extends Controller
{
    protected IAService $iaService;

    public function __construct(IAService $iaService)
    {
        $this->iaService = $iaService;
    }

    /**
     * Procesar evaluación completa y generar reporte con IA
     */
    public function procesarEvaluacion(Request $request)
    {
        // Validar que vengan todos los campos requeridos
        $validator = Validator::make($request->all(), [
            'id_usuario' => 'required|integer|exists:usuario,id_usuario',
            'respuestas_fisico' => 'required|array|min:1',
            'respuestas_fisico.*' => 'required|integer|min:1|max:5',
            'respuestas_psico' => 'required|array|min:1',
            'respuestas_psico.*' => 'required|integer|min:1|max:5',
            'estado_cuerpo' => 'nullable|array',
            'estado_cuerpo.*' => 'nullable|string|in:muybien,bien,normal,mal,muymal',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validación fallida',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Convertir estado del cuerpo a formato para IA
            $cuerpoData = [];
            if ($request->has('estado_cuerpo')) {
                foreach ($request->estado_cuerpo as $parte => $estado) {
                    if ($estado) {
                        $valorNumerico = match($estado) {
                            'muybien' => 5,
                            'bien' => 4,
                            'normal' => 3,
                            'mal' => 2,
                            'muymal' => 1,
                            default => 3
                        };
                        $cuerpoData[$parte] = $valorNumerico;
                    }
                }
            }

            // Llamar a la IA
            $resultadoIA = $this->iaService->generarEvaluacionCompleta(
                $request->respuestas_fisico,
                $request->respuestas_psico,
                $cuerpoData
            );

            if (!$resultadoIA['success']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al procesar con IA',
                    'details' => $resultadoIA
                ], 503);
            }

            // Obtener o crear historial clínico
            $historial = HistorialClinico::firstOrCreate(
                ['id_usuario' => $request->id_usuario],
                [
                    'email' => '',
                    'fecha_inicio' => now(),
                    'fecha_actualizacion' => now()
                ]
            );

            // Crear reporte con resultados de la IA
            $reporte = Reporte::create([
                'id_historial' => $historial->id,
                'fecha_generado' => now(),
                'resumen_fisico' => $resultadoIA['fisico']['data']['resumen'] ?? 'Evaluación física procesada',
                'resumen_emocional' => $resultadoIA['emocional']['data']['resumen'] ?? 'Evaluación emocional procesada',
                'estado_general' => $this->calcularEstadoGeneral($resultadoIA),
                'observaciones' => $resultadoIA['fisico']['data']['observaciones'] ?? null,
                'recomendaciones' => $resultadoIA['emocional']['data']['recomendaciones'] ?? null,
            ]);

            // Actualizar historial
            $historial->update(['fecha_actualizacion' => now()]);

            return response()->json([
                'success' => true,
                'message' => 'Evaluación procesada exitosamente',
                'data' => [
                    'reporte_id' => $reporte->id,
                    'resumen_fisico' => $reporte->resumen_fisico,
                    'resumen_emocional' => $reporte->resumen_emocional,
                    'estado_general' => $reporte->estado_general,
                    'observaciones' => $reporte->observaciones,
                    'recomendaciones' => $reporte->recomendaciones,
                    'fecha' => $reporte->fecha_generado->format('d/m/Y H:i')
                ]
            ], 201);

        } catch (\Exception $e) {
            Log::error('Error procesando evaluación', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error interno al procesar la evaluación: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Calcular estado general basado en resultados de IA
     */
    private function calcularEstadoGeneral(array $resultado): string
    {
        $puntajeFisico = $resultado['fisico']['data']['puntaje'] ?? 3;
        $puntajeEmocional = $resultado['emocional']['data']['puntaje'] ?? 3;
        
        $promedio = ($puntajeFisico + $puntajeEmocional) / 2;

        if ($promedio >= 4) return 'Excelente';
        if ($promedio >= 3) return 'Bueno';
        if ($promedio >= 2) return 'Regular';
        return 'Requiere atención';
    }

    /**
     * Obtener historial de evaluaciones de un voluntario
     */
    public function historialVoluntario(int $idUsuario)
    {
        $historial = HistorialClinico::where('id_usuario', $idUsuario)->first();

        if (!$historial) {
            return response()->json([
                'success' => true,
                'data' => [
                    'reportes' => [],
                    'mensaje' => 'No hay historial para este voluntario'
                ]
            ]);
        }

        $reportes = Reporte::where('id_historial', $historial->id)
            ->orderBy('fecha_generado', 'desc')
            ->get()
            ->map(function ($reporte) {
                return [
                    'id' => $reporte->id,
                    'fecha' => $reporte->fecha_generado ? $reporte->fecha_generado->format('d/m/Y H:i') : null,
                    'estado_general' => $reporte->estado_general,
                    'resumen_fisico' => $reporte->resumen_fisico,
                    'resumen_emocional' => $reporte->resumen_emocional,
                    'observaciones' => $reporte->observaciones,
                    'recomendaciones' => $reporte->recomendaciones,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => [
                'historial_id' => $historial->id,
                'fecha_inicio' => $historial->fecha_inicio ? $historial->fecha_inicio->format('d/m/Y') : null,
                'ultima_actualizacion' => $historial->fecha_actualizacion ? $historial->fecha_actualizacion->format('d/m/Y H:i') : null,
                'reportes' => $reportes
            ]
        ]);
    }
}
