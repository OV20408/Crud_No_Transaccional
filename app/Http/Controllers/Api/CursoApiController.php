<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Curso;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CursoApiController extends Controller
{
    /**
     * ✅ Obtener etapas de un curso con el progreso del voluntario
     * GET /api/cursos/{cursoId}/progreso/{voluntarioId}
     */
    public function obtenerProgresoVoluntario($cursoId, $voluntarioId)
    {
        try {
            // Verificar que el curso existe
            $curso = DB::table('curso')
                ->join('capacitacion', 'capacitacion.id', '=', 'curso.id_capacitacion')
                ->where('curso.id', $cursoId)
                ->select('curso.*', 'capacitacion.nombre as capacitacion_nombre')
                ->first();

            if (!$curso) {
                return response()->json([
                    'success' => false,
                    'message' => 'Curso no encontrado',
                ], 404);
            }

            // Obtener etapas del curso con el progreso del voluntario
            $etapas = DB::table('etapa')
                ->leftJoin('progreso_voluntario', function($join) use ($voluntarioId) {
                    $join->on('progreso_voluntario.id_etapa', '=', 'etapa.id')
                         ->where('progreso_voluntario.id_usuario', '=', $voluntarioId);
                })
                ->where('etapa.id_curso', $cursoId)
                ->select(
                    'etapa.id',
                    'etapa.nombre',
                    'etapa.descripcion',
                    'etapa.orden',
                    'progreso_voluntario.estado',
                    'progreso_voluntario.fecha_inicio',
                    'progreso_voluntario.fecha_finalizacion'
                )
                ->orderBy('etapa.orden', 'asc')
                ->get();

            // Formatear respuesta
            $etapasFormateadas = $etapas->map(function ($etapa) {
                return [
                    'id'          => $etapa->id,
                    'nombre'      => $etapa->nombre,
                    'descripcion' => $etapa->descripcion,
                    'orden'       => $etapa->orden,
                    'estado'      => $etapa->estado ?? 'no_iniciado',
                    'fecha_inicio' => $etapa->fecha_inicio,
                    'fecha_finalizacion' => $etapa->fecha_finalizacion,
                ];
            });

            return response()->json([
                'success' => true,
                'curso' => [
                    'id'           => $curso->id,
                    'nombre'       => $curso->nombre,
                    'descripcion'  => $curso->descripcion,
                    'capacitacion' => $curso->capacitacion_nombre,
                ],
                'etapas' => $etapasFormateadas,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener progreso: ' . $e->getMessage(),
            ], 500);
        }
    }
}