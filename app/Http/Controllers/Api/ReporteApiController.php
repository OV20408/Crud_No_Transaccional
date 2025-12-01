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
                // Obtener necesidades del reporte
                $necesidades = DB::table('reporte_necesidad')
                    ->join('necesidad', 'reporte_necesidad.id_necesidad', '=', 'necesidad.id')
                    ->where('reporte_necesidad.id_reporte', $reporte->id)
                    ->select('necesidad.id', 'necesidad.tipo', 'necesidad.descripcion')
                    ->get();

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
                    'necesidades' => $necesidades,
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

        // Obtener necesidades del reporte
        $necesidades = DB::table('reporte_necesidad')
            ->join('necesidad', 'reporte_necesidad.id_necesidad', '=', 'necesidad.id')
            ->where('reporte_necesidad.id_reporte', $reporte->id)
            ->select('necesidad.id', 'necesidad.tipo', 'necesidad.descripcion')
            ->get();

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
                'necesidades' => $necesidades,
            ],
        ]);
    }

    /**
     * Obtener necesidades asignadas a un voluntario (a través de sus reportes)
     * GET /api/voluntarios/{id}/necesidades
     */
    public function getNecesidadesByVoluntario($voluntarioId)
    {
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

        // Obtener todas las necesidades de todos los reportes del voluntario
        $necesidades = DB::table('reporte')
            ->join('reporte_necesidad', 'reporte.id', '=', 'reporte_necesidad.id_reporte')
            ->join('necesidad', 'reporte_necesidad.id_necesidad', '=', 'necesidad.id')
            ->where('reporte.id_historial', $historial->id)
            ->select(
                'necesidad.id',
                'necesidad.tipo',
                'necesidad.descripcion',
                'reporte.fecha_generado as fechaAsignacion',
                'reporte.id as reporteId'
            )
            ->orderBy('reporte.fecha_generado', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $necesidades,
        ]);
    }

    /**
     * Obtener capacitaciones asignadas a un voluntario (a través de progreso_voluntario)
     * GET /api/voluntarios/{id}/capacitaciones
     */
    public function getCapacitacionesByVoluntario($voluntarioId)
    {
        // Obtener capacitaciones a través de los cursos asignados al voluntario
        $capacitaciones = DB::table('progreso_voluntario')
            ->join('etapa', 'progreso_voluntario.id_etapa', '=', 'etapa.id')
            ->join('curso', 'etapa.id_curso', '=', 'curso.id')
            ->join('capacitacion', 'curso.id_capacitacion', '=', 'capacitacion.id')
            ->where('progreso_voluntario.id_usuario', $voluntarioId)
            ->select(
                'capacitacion.id',
                'capacitacion.nombre',
                'capacitacion.descripcion',
                'curso.nombre as cursoNombre',
                DB::raw('MIN(progreso_voluntario.fecha_inicio) as fechaInicio'),
                DB::raw('MAX(progreso_voluntario.fecha_finalizacion) as fechaFinalizacion'),
                DB::raw("CASE WHEN COUNT(CASE WHEN progreso_voluntario.estado != 'completado' THEN 1 END) = 0 THEN 'completado' ELSE 'en_progreso' END as estado")
            )
            ->groupBy('capacitacion.id', 'capacitacion.nombre', 'capacitacion.descripcion', 'curso.nombre')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $capacitaciones,
        ]);
    }
}
