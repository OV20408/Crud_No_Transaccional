<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\IAService;
use App\Models\Reporte;
use App\Models\Evaluacion;
use App\Models\HistorialClinico;
use App\Models\CursoRecomendacion;
use App\Models\AptitudNecesidad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

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
            'evaluacion_fisica' => 'required|string|min:10',
            'evaluacion_emocional' => 'required|string|min:10',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validación fallida',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Llamar a la IA con el formato correcto (string)
            $resultadoIA = $this->iaService->generarEvaluacionCompleta(
                $request->evaluacion_fisica,
                $request->evaluacion_emocional
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
                'resumen_fisico' => $resultadoIA['fisico']['respuesta'] ?? 'Evaluación física procesada',
                'resumen_emocional' => $resultadoIA['emocional']['respuesta'] ?? 'Evaluación emocional procesada',
                'estado_general' => 'Procesado por IA',
                'observaciones' => null,
                'recomendaciones' => null,
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
            ->map(function ($reporte) use ($idUsuario) {
                // Obtener recomendaciones de cursos para este reporte
                $cursosRecomendados = DB::table('curso_recomendaciones')
                    ->leftJoin('curso', 'curso_recomendaciones.id_curso', '=', 'curso.id')
                    ->leftJoin('capacitacion', 'curso.id_capacitacion', '=', 'capacitacion.id')
                    ->where('curso_recomendaciones.id_reporte', $reporte->id)
                    ->select(
                        'curso_recomendaciones.id',
                        'curso_recomendaciones.mensaje_ia',
                        'curso_recomendaciones.razon',
                        'curso_recomendaciones.estado',
                        'curso.nombre as curso_nombre',
                        'curso.descripcion as curso_descripcion',
                        'capacitacion.nombre as capacitacion_nombre'
                    )
                    ->get();

                // Obtener evaluación de aptitud para necesidades de este reporte
                $aptitudNecesidades = AptitudNecesidad::where('id_reporte', $reporte->id)
                    ->where('id_voluntario', $idUsuario)
                    ->first();

                $necesidadesRecomendadas = [];
                if ($aptitudNecesidades && $aptitudNecesidades->necesidades_recomendadas) {
                    $necesidadesIds = json_decode($aptitudNecesidades->necesidades_recomendadas, true) ?? [];
                    if (!empty($necesidadesIds)) {
                        $necesidadesRecomendadas = DB::table('necesidad')
                            ->whereIn('id', $necesidadesIds)
                            ->select('id', 'tipo', 'descripcion')
                            ->get();
                    }
                }

                return [
                    'id' => $reporte->id,
                    'fecha' => $reporte->fecha_generado ? $reporte->fecha_generado->format('d/m/Y H:i') : null,
                    'estado_general' => $reporte->estado_general,
                    'resumen_fisico' => $reporte->resumen_fisico,
                    'resumen_emocional' => $reporte->resumen_emocional,
                    'observaciones' => $reporte->observaciones,
                    'recomendaciones' => $reporte->recomendaciones,
                    // Nuevos campos para recomendaciones de IA
                    'cursos_recomendados' => $cursosRecomendados->map(function ($curso) {
                        return [
                            'id' => $curso->id,
                            'nombre' => $curso->curso_nombre ?? $curso->mensaje_ia,
                            'descripcion' => $curso->curso_descripcion,
                            'capacitacion' => $curso->capacitacion_nombre,
                            'razon' => $curso->razon,
                            'estado' => $curso->estado,
                        ];
                    }),
                    'aptitud_necesidades' => $aptitudNecesidades ? [
                        'nivel_aptitud' => $aptitudNecesidades->nivel_aptitud,
                        'razon' => $aptitudNecesidades->razon_ia,
                        'necesidades_recomendadas' => $necesidadesRecomendadas,
                    ] : null,
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
