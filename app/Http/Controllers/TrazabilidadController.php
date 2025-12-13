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
 * 
 * VERSIÓN DETALLADA: Incluye la máxima cantidad de datos disponibles.
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

        // Obtener información del voluntario
        $voluntario = DB::table('usuario')
            ->where('ci', $ci)
            ->select(
                'id_usuario',
                'nombres',
                'apellidos',
                'ci',
                'fecha_nacimiento',
                'genero',
                'telefono',
                'email',
                'direccion_domicilio',
                'estado',
                'nivel_entrenamiento',
                'entidad_pertenencia',
                'tipo_sangre',
                'created_at',
                'updated_at'
            )
            ->first();

        // 1. EVALUACIONES - Tests/evaluaciones realizadas por el voluntario
        $evaluaciones = DB::table('evaluacion')
            ->join('test', 'evaluacion.id_test', '=', 'test.id')
            ->leftJoin('reporte', 'evaluacion.id_reporte', '=', 'reporte.id')
            ->leftJoin('universidad', 'evaluacion.id_universidad', '=', 'universidad.id')
            ->where('evaluacion.ci_voluntario', $ci)
            ->select(
                'evaluacion.id as id_evaluacion',
                'evaluacion.id_test',
                'evaluacion.id_reporte',
                'evaluacion.id_universidad',
                'evaluacion.fecha',
                'evaluacion.ci_voluntario',
                'test.nombre as test_nombre',
                'test.categoria as test_categoria',
                'test.descripcion as test_descripcion',
                'reporte.estado_general as reporte_estado_general',
                'reporte.resumen_fisico as reporte_resumen_fisico',
                'reporte.resumen_emocional as reporte_resumen_emocional'
            )
            ->orderBy('evaluacion.fecha', 'desc')
            ->get();

        // 2. RESPUESTAS - Respuestas a preguntas de evaluaciones
        $respuestas = DB::table('respuesta')
            ->join('evaluacion', 'respuesta.id_evaluacion', '=', 'evaluacion.id')
            ->join('test', 'evaluacion.id_test', '=', 'test.id')
            ->where('respuesta.ci_voluntario', $ci)
            ->select(
                'respuesta.id as id_respuesta',
                'respuesta.id_evaluacion',
                'respuesta.texto_pregunta',
                'respuesta.respuesta_texto',
                'respuesta.ci_voluntario',
                'respuesta.created_at',
                'evaluacion.fecha as evaluacion_fecha',
                'test.nombre as test_nombre',
                'test.categoria as test_categoria'
            )
            ->orderBy('respuesta.created_at', 'desc')
            ->get();

        // 3. REPORTES - Reportes generados
        $reportes = DB::table('reporte')
            ->leftJoin('historial_clinico', 'reporte.id_historial', '=', 'historial_clinico.id')
            ->where('reporte.ci_voluntario', $ci)
            ->select(
                'reporte.id as id_reporte',
                'reporte.estado_general',
                'reporte.fecha_generado',
                'reporte.observaciones',
                'reporte.recomendaciones',
                'reporte.resumen_emocional',
                'reporte.resumen_fisico',
                'reporte.respuestas_fisico',
                'reporte.respuestas_emocional',
                'reporte.id_historial',
                'reporte.ci_voluntario',
                'historial_clinico.fecha_inicio as historial_fecha_inicio',
                'historial_clinico.fecha_actualizacion as historial_fecha_actualizacion'
            )
            ->orderBy('reporte.fecha_generado', 'desc')
            ->get();

        // 4. PROGRESO EN CAPACITACIONES - Avances en cursos/etapas
        $progresoCapacitaciones = DB::table('progreso_voluntario')
            ->join('etapa', 'progreso_voluntario.id_etapa', '=', 'etapa.id')
            ->join('curso', 'etapa.id_curso', '=', 'curso.id')
            ->join('capacitacion', 'curso.id_capacitacion', '=', 'capacitacion.id')
            ->where('progreso_voluntario.ci_voluntario', $ci)
            ->select(
                'progreso_voluntario.id as id_progreso',
                'progreso_voluntario.id_usuario',
                'progreso_voluntario.id_etapa',
                'progreso_voluntario.estado',
                'progreso_voluntario.fecha_inicio',
                'progreso_voluntario.fecha_finalizacion',
                'progreso_voluntario.ci_voluntario',
                'etapa.id as etapa_id',
                'etapa.nombre as etapa_nombre',
                'etapa.orden as etapa_orden',
                'etapa.descripcion as etapa_descripcion',
                'curso.id as curso_id',
                'curso.nombre as curso_nombre',
                'curso.descripcion as curso_descripcion',
                'capacitacion.id as capacitacion_id',
                'capacitacion.nombre as capacitacion_nombre',
                'capacitacion.descripcion as capacitacion_descripcion'
            )
            ->orderBy('progreso_voluntario.fecha_inicio', 'desc')
            ->get();

        // 5. CONSULTAS - Consultas realizadas
        $consultas = DB::table('consultas')
            ->leftJoin('necesidad', 'consultas.necesidad_id', '=', 'necesidad.id')
            ->where('consultas.ci_voluntario', $ci)
            ->select(
                'consultas.id as id_consulta',
                'consultas.voluntario_id',
                'consultas.necesidad_id',
                'consultas.mensaje',
                'consultas.estado',
                'consultas.respuesta_admin',
                'consultas.ci_voluntario',
                'consultas.created_at',
                'consultas.updated_at',
                'necesidad.tipo as necesidad_tipo',
                'necesidad.descripcion as necesidad_descripcion'
            )
            ->orderBy('consultas.created_at', 'desc')
            ->get();

        // 6. MENSAJES DE CHAT - Mensajes enviados
        $chatMensajes = DB::table('chat_mensajes')
            ->where('ci_voluntario', $ci)
            ->select(
                'id as id_mensaje',
                'voluntario_id',
                'de',
                'texto',
                'leido_en',
                'ci_voluntario',
                'created_at',
                'updated_at'
            )
            ->orderBy('created_at', 'desc')
            ->get();

        // 7. SOLICITUDES DE AYUDA - Emergencias/solicitudes  
        $solicitudesAyuda = DB::table('solicitudes_ayuda')
            ->where('ci_voluntario_solicita', $ci)
            ->select(
                'id as id_solicitud',
                'voluntario_id',
                'tipo',
                'nivel_emergencia',
                'descripcion',
                'latitud',
                'longitud',
                'estado',
                'ci_voluntarios_acudir',
                'fecha_respondida',
                'ci_voluntario_solicita',
                'ci_voluntario_responde',
                'created_at',
                'updated_at'
            )
            ->orderBy('created_at', 'desc')
            ->get();

        // 8. RECOMENDACIONES DE CURSOS - Cursos recomendados/asignados
        $recomendacionesCursos = DB::table('curso_recomendaciones')
            ->leftJoin('curso', 'curso_recomendaciones.id_curso', '=', 'curso.id')
            ->leftJoin('capacitacion', 'curso.id_capacitacion', '=', 'capacitacion.id')
            ->leftJoin('reporte', 'curso_recomendaciones.id_reporte', '=', 'reporte.id')
            ->where('curso_recomendaciones.ci_voluntario', $ci)
            ->select(
                'curso_recomendaciones.id as id_recomendacion',
                'curso_recomendaciones.id_voluntario',
                'curso_recomendaciones.id_curso',
                'curso_recomendaciones.id_reporte',
                'curso_recomendaciones.mensaje_ia',
                'curso_recomendaciones.razon',
                'curso_recomendaciones.estado',
                'curso_recomendaciones.ci_voluntario',
                'curso_recomendaciones.created_at',
                'curso_recomendaciones.updated_at',
                'curso.nombre as curso_nombre',
                'curso.descripcion as curso_descripcion',
                'capacitacion.nombre as capacitacion_nombre',
                'capacitacion.descripcion as capacitacion_descripcion',
                'reporte.estado_general as reporte_estado_general',
                'reporte.fecha_generado as reporte_fecha_generado'
            )
            ->orderBy('curso_recomendaciones.created_at', 'desc')
            ->get();

        // 9. APTITUD DE NECESIDADES - Evaluaciones de aptitud
        $aptitudNecesidades = DB::table('aptitud_necesidades')
            ->leftJoin('necesidad', 'aptitud_necesidades.id_necesidad', '=', 'necesidad.id')
            ->leftJoin('reporte', 'aptitud_necesidades.id_reporte', '=', 'reporte.id')
            ->where('aptitud_necesidades.ci_voluntario', $ci)
            ->select(
                'aptitud_necesidades.id as id_aptitud',
                'aptitud_necesidades.id_voluntario',
                'aptitud_necesidades.id_necesidad',
                'aptitud_necesidades.id_reporte',
                'aptitud_necesidades.nivel_aptitud',
                'aptitud_necesidades.razon_ia',
                'aptitud_necesidades.necesidades_recomendadas',
                'aptitud_necesidades.estado',
                'aptitud_necesidades.ci_voluntario',
                'aptitud_necesidades.created_at',
                'aptitud_necesidades.updated_at',
                'necesidad.tipo as necesidad_tipo',
                'necesidad.descripcion as necesidad_descripcion',
                'reporte.estado_general as reporte_estado_general',
                'reporte.fecha_generado as reporte_fecha_generado'
            )
            ->orderBy('aptitud_necesidades.created_at', 'desc')
            ->get();

        // 10. HISTORIAL CLÍNICO - Cambios en historial
        $historialClinico = DB::table('historial_clinico')
            ->join('usuario', 'historial_clinico.id_usuario', '=', 'usuario.id_usuario')
            ->where('usuario.ci', $ci)
            ->select(
                'historial_clinico.id as id_historial',
                'historial_clinico.id_usuario',
                'historial_clinico.fecha_inicio',
                'historial_clinico.fecha_actualizacion',
                'usuario.ci as ci_usuario',
                'usuario.nombres as usuario_nombres',
                'usuario.apellidos as usuario_apellidos'
            )
            ->orderBy('historial_clinico.fecha_actualizacion', 'desc')
            ->get();

        // 11. ASIGNACIÓN DE NECESIDADES - Necesidades asignadas a reportes
        $necesidadesAsignadas = DB::table('reporte_necesidad')
            ->join('necesidad', 'reporte_necesidad.id_necesidad', '=', 'necesidad.id')
            ->join('reporte', 'reporte_necesidad.id_reporte', '=', 'reporte.id')
            ->where('reporte.ci_voluntario', $ci)
            ->select(
                'reporte_necesidad.id_reporte',
                'reporte_necesidad.id_necesidad',
                'reporte_necesidad.created_at',
                'reporte_necesidad.updated_at',
                'necesidad.tipo as necesidad_tipo',
                'necesidad.descripcion as necesidad_descripcion',
                'reporte.estado_general as reporte_estado_general',
                'reporte.fecha_generado as reporte_fecha_generado',
                'reporte.ci_voluntario'
            )
            ->orderBy('reporte.fecha_generado', 'desc')
            ->get();

        // Construir respuesta JSON completa
        $trazabilidad = [
            'ci_consultado' => $ci,
            'fecha_consulta' => now()->timezone('America/La_Paz')->toDateTimeString(),
            'sistema' => 'GEVOPI - Sistema de Gestión de Voluntarios de Protección Integral',
            'voluntario' => $voluntario,
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
                    'descripcion' => 'Tests y evaluaciones físicas/emocionales completadas por el voluntario',
                    'total' => count($evaluaciones),
                    'registros' => $evaluaciones
                ],
                'respuestas' => [
                    'descripcion' => 'Respuestas individuales a preguntas de tests y evaluaciones',
                    'total' => count($respuestas),
                    'registros' => $respuestas
                ],
                'reportes' => [
                    'descripcion' => 'Reportes generados automáticamente por el sistema basados en evaluaciones',
                    'total' => count($reportes),
                    'registros' => $reportes
                ],
                'progreso_capacitaciones' => [
                    'descripcion' => 'Avance del voluntario en etapas, cursos y capacitaciones del sistema',
                    'total' => count($progresoCapacitaciones),
                    'registros' => $progresoCapacitaciones
                ],
                'consultas' => [
                    'descripcion' => 'Consultas realizadas por el voluntario al sistema de ayuda',
                    'total' => count($consultas),
                    'registros' => $consultas
                ],
                'chat_mensajes' => [
                    'descripcion' => 'Mensajes enviados por el voluntario en el sistema de chat',
                    'total' => count($chatMensajes),
                    'registros' => $chatMensajes
                ],
                'solicitudes_ayuda' => [
                    'descripcion' => 'Solicitudes de emergencia o ayuda realizadas por el voluntario',
                    'total' => count($solicitudesAyuda),
                    'registros' => $solicitudesAyuda
                ],
                'recomendaciones_cursos' => [
                    'descripcion' => 'Cursos recomendados al voluntario por el sistema de IA según evaluaciones',
                    'total' => count($recomendacionesCursos),
                    'registros' => $recomendacionesCursos
                ],
                'aptitud_necesidades' => [
                    'descripcion' => 'Evaluaciones de aptitud del voluntario para atender necesidades específicas',
                    'total' => count($aptitudNecesidades),
                    'registros' => $aptitudNecesidades
                ],
                'historial_clinico' => [
                    'descripcion' => 'Modificaciones realizadas al historial clínico del voluntario',
                    'total' => count($historialClinico),
                    'registros' => $historialClinico
                ],
                'necesidades_asignadas' => [
                    'descripcion' => 'Necesidades de apoyo asignadas al voluntario en base a sus reportes',
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
