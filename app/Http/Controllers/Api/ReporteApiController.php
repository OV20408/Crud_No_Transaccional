<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Reporte;
use Illuminate\Support\Facades\DB;

class ReporteApiController extends Controller
{
    /**
     * Obtener todos los reportes de un voluntario
     * GET /api/voluntarios/{id}/reportes
     */
    public function getByVoluntario($voluntarioId)
    {
        // Primero obtener el historial_clinico del voluntario
        $historial = DB::table('historial_clinico')
            ->where('id_usuario', $voluntarioId)
            ->first();

        if (!$historial) {
            return response()->json([
                'success' => true,
                'data' => [],
                'message' => 'El voluntario no tiene historial clínico'
            ]);
        }

        // Obtener todos los reportes del historial
        $reportes = Reporte::where('id_historial', $historial->id)
            ->orderBy('fecha_generado', 'desc')
            ->get()
            ->map(function ($reporte) {
                return [
                    'id' => $reporte->id,
                    'fechaGenerado' => $reporte->fecha_generado ? $reporte->fecha_generado->format('Y-m-d H:i:s') : null,
                    'estadoGeneral' => $reporte->estado_general,
                    'resumenFisico' => $reporte->resumen_fisico,
                    'resumenEmocional' => $reporte->resumen_emocional,
                    'respuestasFisico' => $reporte->respuestas_fisico,
                    'respuestasEmocional' => $reporte->respuestas_emocional,
                    'observaciones' => $reporte->observaciones,
                    'recomendaciones' => $reporte->recomendaciones,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $reportes,
        ]);
    }

    /**
     * Obtener el último reporte de un voluntario
     * GET /api/voluntarios/{id}/reportes/ultimo
     */
    public function getUltimoByVoluntario($voluntarioId)
    {
        $historial = DB::table('historial_clinico')
            ->where('id_usuario', $voluntarioId)
            ->first();

        if (!$historial) {
            return response()->json([
                'success' => true,
                'data' => null,
                'message' => 'El voluntario no tiene historial clínico'
            ]);
        }

        $reporte = Reporte::where('id_historial', $historial->id)
            ->orderBy('fecha_generado', 'desc')
            ->first();

        if (!$reporte) {
            return response()->json([
                'success' => true,
                'data' => null,
                'message' => 'No hay reportes disponibles'
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $reporte->id,
                'fechaGenerado' => $reporte->fecha_generado ? $reporte->fecha_generado->format('Y-m-d H:i:s') : null,
                'estadoGeneral' => $reporte->estado_general,
                'resumenFisico' => $reporte->resumen_fisico,
                'resumenEmocional' => $reporte->resumen_emocional,
                'respuestasFisico' => $reporte->respuestas_fisico,
                'respuestasEmocional' => $reporte->respuestas_emocional,
                'observaciones' => $reporte->observaciones,
                'recomendaciones' => $reporte->recomendaciones,
            ],
        ]);
    }
}
