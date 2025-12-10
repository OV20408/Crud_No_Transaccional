<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * TrazabilidadController
 * 
 * Controlador para el API Gateway de trazabilidad.
 * Devuelve todas las acciones realizadas por un voluntario en el sistema GEVOPI
 * basándose en el CI del voluntario.
 */
class TrazabilidadController extends Controller
{
    /**
     * Obtener todas las acciones realizadas por un voluntario según su CI
     * 
     * @param string $ci - Cédula de Identidad del voluntario
     * @return \Illuminate\Http\JsonResponse
     */
    public function porVoluntario($ci)
    {
        // Validar que el CI no esté vacío
        if (empty($ci)) {
            return response()->json([
                'success' => false,
                'message' => 'El CI es requerido',
                'data' => null
            ], 400);
        }

        // 1. EVALUACIONES - Tests/evaluaciones realizadas por el voluntario
        $evaluaciones = DB::table('evaluacion')
            ->join('test', 'evaluacion.id_test', '=', 'test.id')
            ->leftJoin('reporte', 'evaluacion.id_reporte', '=', 'reporte.id')
            ->where('evaluacion.ci_voluntario_accion', $ci)
            ->select(
                'evaluacion.id',
                'evaluacion.fecha',
                'evaluacion.ci_voluntario_accion',
                'test.nombre as test_nombre',
                'test.categoria as test_categoria',
                'reporte.estado_general',
                'evaluacion.created_at'
            )
            ->orderBy('evaluacion.fecha', 'desc')
            ->get();

        // 2. RESPUESTAS - Respuestas a preguntas de evaluaciones
        $respuestas = DB::table('respuesta')
            ->join('pregunta', 'respuesta.id_pregunta', '=', 'pregunta.id')
            ->join('evaluacion', 'respuesta.id_evaluacion', '=', 'evaluacion.id')
            ->where('respuesta.ci_voluntario_accion', $ci)
            ->select(
                'respuesta.id',
                'respuesta.respuesta_texto',
                'respuesta.ci_voluntario_accion',
                'pregunta.texto as pregunta_texto',
                'pregunta.tipo as pregunta_tipo',
                'evaluacion.fecha as fecha_evaluacion',
                'respuesta.created_at'
            )
            ->orderBy('respuesta.created_at', 'desc')
            ->get();

        // 3. REPORTES - Reportes generados
        $reportes = DB::table('reporte')
            ->leftJoin('historial_clinico', 'reporte.id_historial', '=', 'historial_clinico.id')
            ->where('reporte.ci_voluntario_accion', $ci)
            ->select(
                'reporte.id',
                'reporte.estado_general',
                'reporte.fecha_generado',
                'reporte.ci_voluntario_accion',
                'reporte.resumen_fisico',
                'reporte.resumen_emocional',
                'reporte.observaciones',
                'reporte.recomendaciones',
                'reporte.created_at'
            )
            ->orderBy('reporte.fecha_generado', 'desc')
            ->get();

        // 4. PROGRESO EN CAPACITACIONES - Avances en cursos/etapas
        $progresoCapacitaciones = DB::table('progreso_voluntario')
            ->join('etapa', 'progreso_voluntario.id_etapa', '=', 'etapa.id')
            ->join('curso', 'etapa.id_curso', '=', 'curso.id')
            ->join('capacitacion', 'curso.id_capacitacion', '=', 'capacitacion.id')
            ->where('progreso_voluntario.ci_voluntario_accion', $ci)
            ->select(
                'progreso_voluntario.id',
                'progreso_voluntario.estado',
                'progreso_voluntario.fecha_inicio',
                'progreso_voluntario.fecha_finalizacion',
                'progreso_voluntario.ci_voluntario_accion',
                'etapa.nombre as etapa_nombre',
                'etapa.orden as etapa_orden',
                'curso.nombre as curso_nombre',
                'capacitacion.nombre as capacitacion_nombre',
                'progreso_voluntario.created_at'
            )
            ->orderBy('progreso_voluntario.created_at', 'desc')
            ->get();

        // 5. CONSULTAS - Consultas realizadas
        $consultas = DB::table('consultas')
            ->leftJoin('necesidad', 'consultas.necesidad_id', '=', 'necesidad.id')
            ->where('consultas.ci_voluntario_accion', $ci)
            ->select(
                'consultas.id',
                'consultas.mensaje',
                'consultas.estado',
                'consultas.respuesta_admin',
                'consultas.ci_voluntario_accion',
                'necesidad.descripcion as necesidad_descripcion',
                'necesidad.tipo as necesidad_tipo',
                'consultas.created_at'
            )
            ->orderBy('consultas.created_at', 'desc')
            ->get();

        // 6. MENSAJES DE CHAT - Mensajes enviados
        $chatMensajes = DB::table('chat_mensajes')
            ->where('ci_voluntario_accion', $ci)
            ->select(
                'id',
                'de',
                'texto',
                'leido_en',
                'ci_voluntario_accion',
                'created_at'
            )
            ->orderBy('created_at', 'desc')
            ->get();

        // 7. SOLICITUDES DE AYUDA - Emergencias/solicitudes
        $solicitudesAyuda = DB::table('solicitudes_ayuda')
            ->where('ci_voluntario_accion', $ci)
            ->select(
                'id',
                'tipo',
                'tipo_emergencia',
                'nivel_emergencia',
                'descripcion',
                'latitud',
                'longitud',
                'estado',
                'ci_voluntarios_acudir',
                'fecha_respondida',
                'ci_voluntario_accion',
                'created_at'
            )
            ->orderBy('created_at', 'desc')
            ->get();

        // 8. RECOMENDACIONES DE CURSOS - Cursos recomendados/asignados
        $recomendacionesCursos = DB::table('curso_recomendaciones')
            ->leftJoin('curso', 'curso_recomendaciones.id_curso', '=', 'curso.id')
            ->leftJoin('capacitacion', 'curso.id_capacitacion', '=', 'capacitacion.id')
            ->where('curso_recomendaciones.ci_voluntario_accion', $ci)
            ->select(
                'curso_recomendaciones.id',
                'curso_recomendaciones.mensaje_ia',
                'curso_recomendaciones.razon',
                'curso_recomendaciones.estado',
                'curso_recomendaciones.ci_voluntario_accion',
                'curso.nombre as curso_nombre',
                'capacitacion.nombre as capacitacion_nombre',
                'curso_recomendaciones.created_at'
            )
            ->orderBy('curso_recomendaciones.created_at', 'desc')
            ->get();

        // 9. APTITUD DE NECESIDADES - Evaluaciones de aptitud
        $aptitudNecesidades = DB::table('aptitud_necesidades')
            ->leftJoin('necesidad', 'aptitud_necesidades.id_necesidad', '=', 'necesidad.id')
            ->where('aptitud_necesidades.ci_voluntario_accion', $ci)
            ->select(
                'aptitud_necesidades.id',
                'aptitud_necesidades.nivel_aptitud',
                'aptitud_necesidades.razon_ia',
                'aptitud_necesidades.necesidades_recomendadas',
                'aptitud_necesidades.estado',
                'aptitud_necesidades.ci_voluntario_accion',
                'necesidad.descripcion as necesidad_descripcion',
                'necesidad.tipo as necesidad_tipo',
                'aptitud_necesidades.created_at'
            )
            ->orderBy('aptitud_necesidades.created_at', 'desc')
            ->get();

        // 10. HISTORIAL CLÍNICO - Cambios en historial
        $historialClinico = DB::table('historial_clinico')
            ->where('ci_voluntario_accion', $ci)
            ->select(
                'id',
                'fecha_inicio',
                'fecha_actualizacion',
                'ci_voluntario_accion',
                'created_at'
            )
            ->orderBy('created_at', 'desc')
            ->get();

        // 11. ASIGNACIÓN DE NECESIDADES - Necesidades asignadas a reportes
        $necesidadesAsignadas = DB::table('reporte_necesidad')
            ->join('necesidad', 'reporte_necesidad.id_necesidad', '=', 'necesidad.id')
            ->join('reporte', 'reporte_necesidad.id_reporte', '=', 'reporte.id')
            ->where('reporte_necesidad.ci_voluntario_accion', $ci)
            ->select(
                'reporte_necesidad.id_reporte',
                'reporte_necesidad.id_necesidad',
                'reporte_necesidad.ci_voluntario_accion',
                'necesidad.descripcion as necesidad_descripcion',
                'necesidad.tipo as necesidad_tipo',
                'reporte.fecha_generado'
            )
            ->orderBy('reporte.fecha_generado', 'desc')
            ->get();

        // Construir respuesta JSON completa
        $trazabilidad = [
            'ci_consultado' => $ci,
            'fecha_consulta' => now()->timezone('America/La_Paz')->toDateTimeString(),
            'sistema' => 'GEVOPI - Sistema de Gestión de Voluntarios de Protección Integral',
            'total_acciones' => 
                count($evaluaciones) + 
                count($respuestas) + 
                count($reportes) + 
                count($progresoCapacitaciones) + 
                count($consultas) + 
                count($chatMensajes) + 
                count($solicitudesAyuda) + 
                count($recomendacionesCursos) + 
                count($aptitudNecesidades) + 
                count($historialClinico) +
                count($necesidadesAsignadas),
            'acciones' => [
                'evaluaciones' => [
                    'descripcion' => 'Tests y evaluaciones físicas/emocionales completadas',
                    'total' => count($evaluaciones),
                    'registros' => $evaluaciones
                ],
                'respuestas' => [
                    'descripcion' => 'Respuestas a preguntas de evaluaciones',
                    'total' => count($respuestas),
                    'registros' => $respuestas
                ],
                'reportes' => [
                    'descripcion' => 'Reportes de evaluación generados',
                    'total' => count($reportes),
                    'registros' => $reportes
                ],
                'progreso_capacitaciones' => [
                    'descripcion' => 'Avance en etapas y cursos de capacitación',
                    'total' => count($progresoCapacitaciones),
                    'registros' => $progresoCapacitaciones
                ],
                'consultas' => [
                    'descripcion' => 'Consultas realizadas al sistema',
                    'total' => count($consultas),
                    'registros' => $consultas
                ],
                'chat_mensajes' => [
                    'descripcion' => 'Mensajes enviados en el chat',
                    'total' => count($chatMensajes),
                    'registros' => $chatMensajes
                ],
                'solicitudes_ayuda' => [
                    'descripcion' => 'Solicitudes de ayuda/emergencia creadas',
                    'total' => count($solicitudesAyuda),
                    'registros' => $solicitudesAyuda
                ],
                'recomendaciones_cursos' => [
                    'descripcion' => 'Cursos recomendados por IA y asignados',
                    'total' => count($recomendacionesCursos),
                    'registros' => $recomendacionesCursos
                ],
                'aptitud_necesidades' => [
                    'descripcion' => 'Evaluaciones de aptitud para necesidades',
                    'total' => count($aptitudNecesidades),
                    'registros' => $aptitudNecesidades
                ],
                'historial_clinico' => [
                    'descripcion' => 'Modificaciones al historial clínico',
                    'total' => count($historialClinico),
                    'registros' => $historialClinico
                ],
                'necesidades_asignadas' => [
                    'descripcion' => 'Necesidades asignadas a reportes',
                    'total' => count($necesidadesAsignadas),
                    'registros' => $necesidadesAsignadas
                ]
            ]
        ];

        return response()->json([
            'success' => true,
            'message' => 'Trazabilidad obtenida exitosamente',
            'data' => $trazabilidad
        ], 200);
    }
}
