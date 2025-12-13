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
 * NOTA: Todas las tablas usan la columna `ci_voluntario_accion` para referenciar al voluntario.
 * La tabla `usuario` usa `ci` como identificador principal.
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
        try {
            // Validar que el CI no esté vacío
            if (empty($ci)) {
                return response()->json([
                    'success' => false,
                    'message' => 'El CI es requerido',
                    'data' => null
                ], 400);
            }

            // ========================================================
            // INFORMACIÓN DEL VOLUNTARIO (tabla: usuario)
            // Columnas: id_usuario, nombres, apellidos, ci, fecha_nacimiento,
            //           genero, telefono, email, direccion_domicilio, estado,
            //           nivel_entrenamiento, entidad_pertenencia, tipo_sangre,
            //           foto_ci, licencia_conducir, foto_licencia, created_at, updated_at
            // ========================================================
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

            // ========================================================
            // 1. EVALUACIONES (tabla: evaluacion)
            // Columnas: id, id_reporte, id_test, id_universidad, fecha, ci_voluntario_accion
            // ========================================================
            $evaluaciones = DB::table('evaluacion')
                ->join('test', 'evaluacion.id_test', '=', 'test.id')
                ->leftJoin('reporte', 'evaluacion.id_reporte', '=', 'reporte.id')
                ->leftJoin('universidad', 'evaluacion.id_universidad', '=', 'universidad.id')
                ->where('evaluacion.ci_voluntario_accion', $ci)
                ->whereNull('evaluacion.deleted_at')
                ->select(
                    'evaluacion.id as id_evaluacion',
                    'evaluacion.id_test',
                    'evaluacion.id_reporte',
                    'evaluacion.id_universidad',
                    'evaluacion.fecha',
                    'evaluacion.ci_voluntario_accion',
                    'test.nombre as test_nombre',
                    'test.categoria as test_categoria',
                    'test.descripcion as test_descripcion',
                    'reporte.estado_general as reporte_estado_general',
                    'reporte.resumen_fisico as reporte_resumen_fisico',
                    'reporte.resumen_emocional as reporte_resumen_emocional'
                )
                ->orderBy('evaluacion.fecha', 'desc')
                ->get();

            // ========================================================
            // 2. RESPUESTAS (tabla: respuesta)
            // Columnas: id, id_evaluacion, texto_pregunta, respuesta_texto, created_at, ci_voluntario_accion
            // ========================================================
            $respuestas = DB::table('respuesta')
                ->join('evaluacion', 'respuesta.id_evaluacion', '=', 'evaluacion.id')
                ->join('test', 'evaluacion.id_test', '=', 'test.id')
                ->where('respuesta.ci_voluntario_accion', $ci)
                ->whereNull('respuesta.deleted_at')
                ->select(
                    'respuesta.id as id_respuesta',
                    'respuesta.id_evaluacion',
                    'respuesta.texto_pregunta',
                    'respuesta.respuesta_texto',
                    'respuesta.ci_voluntario_accion',
                    'respuesta.created_at',
                    'evaluacion.fecha as evaluacion_fecha',
                    'test.nombre as test_nombre',
                    'test.categoria as test_categoria'
                )
                ->orderBy('respuesta.created_at', 'desc')
                ->get();

            // ========================================================
            // 3. REPORTES (tabla: reporte)
            // Columnas: id, estado_general, fecha_generado, observaciones, recomendaciones,
            //           resumen_emocional, resumen_fisico, id_historial, respuestas_fisico,
            //           respuestas_emocional, ci_voluntario_accion
            // ========================================================
            $reportes = DB::table('reporte')
                ->leftJoin('historial_clinico', 'reporte.id_historial', '=', 'historial_clinico.id')
                ->where('reporte.ci_voluntario_accion', $ci)
                ->whereNull('reporte.deleted_at')
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
                    'reporte.ci_voluntario_accion',
                    'historial_clinico.fecha_inicio as historial_fecha_inicio',
                    'historial_clinico.fecha_actualizacion as historial_fecha_actualizacion'
                )
                ->orderBy('reporte.fecha_generado', 'desc')
                ->get();

            // ========================================================
            // 4. PROGRESO EN CAPACITACIONES (tabla: progreso_voluntario)
            // Columnas: id, id_usuario, id_etapa, estado, fecha_inicio, fecha_finalizacion, ci_voluntario_accion
            // ========================================================
            $progresoCapacitaciones = DB::table('progreso_voluntario')
                ->join('etapa', 'progreso_voluntario.id_etapa', '=', 'etapa.id')
                ->join('curso', 'etapa.id_curso', '=', 'curso.id')
                ->join('capacitacion', 'curso.id_capacitacion', '=', 'capacitacion.id')
                ->where('progreso_voluntario.ci_voluntario_accion', $ci)
                ->whereNull('progreso_voluntario.deleted_at')
                ->select(
                    'progreso_voluntario.id as id_progreso',
                    'progreso_voluntario.id_usuario',
                    'progreso_voluntario.id_etapa',
                    'progreso_voluntario.estado',
                    'progreso_voluntario.fecha_inicio',
                    'progreso_voluntario.fecha_finalizacion',
                    'progreso_voluntario.ci_voluntario_accion',
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

            // ========================================================
            // 5. CONSULTAS (tabla: consultas)
            // Columnas: id, voluntario_id, necesidad_id, mensaje, estado, created_at, updated_at, respuesta_admin, ci_voluntario_accion
            // ========================================================
            $consultas = DB::table('consultas')
                ->leftJoin('necesidad', 'consultas.necesidad_id', '=', 'necesidad.id')
                ->where('consultas.ci_voluntario_accion', $ci)
                ->whereNull('consultas.deleted_at')
                ->select(
                    'consultas.id as id_consulta',
                    'consultas.voluntario_id',
                    'consultas.necesidad_id',
                    'consultas.mensaje',
                    'consultas.estado',
                    'consultas.respuesta_admin',
                    'consultas.ci_voluntario_accion',
                    'consultas.created_at',
                    'consultas.updated_at',
                    'necesidad.tipo as necesidad_tipo',
                    'necesidad.descripcion as necesidad_descripcion'
                )
                ->orderBy('consultas.created_at', 'desc')
                ->get();

            // ========================================================
            // 6. MENSAJES DE CHAT (tabla: chat_mensajes)
            // Columnas: id, voluntario_id, de, texto, leido_en, created_at, updated_at, ci_voluntario_accion
            // ========================================================
            $chatMensajes = DB::table('chat_mensajes')
                ->where('ci_voluntario_accion', $ci)
                ->whereNull('deleted_at')
                ->select(
                    'id as id_mensaje',
                    'voluntario_id',
                    'de',
                    'texto',
                    'leido_en',
                    'ci_voluntario_accion',
                    'created_at',
                    'updated_at'
                )
                ->orderBy('created_at', 'desc')
                ->get();

            // ========================================================
            // 7. SOLICITUDES DE AYUDA (tabla: solicitudes_ayuda)
            // Columnas: id, voluntario_id, tipo, nivel_emergencia, descripcion, latitud, longitud,
            //           estado, ci_voluntarios_acudir, fecha_respondida, created_at, updated_at, ci_voluntario_accion
            // NOTA: NO tiene ci_voluntario_solicita ni ci_voluntario_responde
            // ========================================================
            $solicitudesAyuda = DB::table('solicitudes_ayuda')
                ->where('ci_voluntario_accion', $ci)
                ->whereNull('deleted_at')
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
                    'ci_voluntario_accion',
                    'created_at',
                    'updated_at'
                )
                ->orderBy('created_at', 'desc')
                ->get();

            // ========================================================
            // 8. RECOMENDACIONES DE CURSOS (tabla: curso_recomendaciones)
            // Columnas: id, id_voluntario, id_curso, id_reporte, mensaje_ia, razon, estado, created_at, updated_at, ci_voluntario_accion
            // ========================================================
            $recomendacionesCursos = DB::table('curso_recomendaciones')
                ->leftJoin('curso', 'curso_recomendaciones.id_curso', '=', 'curso.id')
                ->leftJoin('capacitacion', 'curso.id_capacitacion', '=', 'capacitacion.id')
                ->leftJoin('reporte', 'curso_recomendaciones.id_reporte', '=', 'reporte.id')
                ->where('curso_recomendaciones.ci_voluntario_accion', $ci)
                ->whereNull('curso_recomendaciones.deleted_at')
                ->select(
                    'curso_recomendaciones.id as id_recomendacion',
                    'curso_recomendaciones.id_voluntario',
                    'curso_recomendaciones.id_curso',
                    'curso_recomendaciones.id_reporte',
                    'curso_recomendaciones.mensaje_ia',
                    'curso_recomendaciones.razon',
                    'curso_recomendaciones.estado',
                    'curso_recomendaciones.ci_voluntario_accion',
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

            // ========================================================
            // 9. APTITUD DE NECESIDADES (tabla: aptitud_necesidades)
            // Columnas: id, id_voluntario, id_necesidad, id_reporte, nivel_aptitud, razon_ia,
            //           necesidades_recomendadas, estado, created_at, updated_at, ci_voluntario_accion
            // ========================================================
            $aptitudNecesidades = DB::table('aptitud_necesidades')
                ->leftJoin('necesidad', 'aptitud_necesidades.id_necesidad', '=', 'necesidad.id')
                ->leftJoin('reporte', 'aptitud_necesidades.id_reporte', '=', 'reporte.id')
                ->where('aptitud_necesidades.ci_voluntario_accion', $ci)
                ->whereNull('aptitud_necesidades.deleted_at')
                ->select(
                    'aptitud_necesidades.id as id_aptitud',
                    'aptitud_necesidades.id_voluntario',
                    'aptitud_necesidades.id_necesidad',
                    'aptitud_necesidades.id_reporte',
                    'aptitud_necesidades.nivel_aptitud',
                    'aptitud_necesidades.razon_ia',
                    'aptitud_necesidades.necesidades_recomendadas',
                    'aptitud_necesidades.estado',
                    'aptitud_necesidades.ci_voluntario_accion',
                    'aptitud_necesidades.created_at',
                    'aptitud_necesidades.updated_at',
                    'necesidad.tipo as necesidad_tipo',
                    'necesidad.descripcion as necesidad_descripcion',
                    'reporte.estado_general as reporte_estado_general',
                    'reporte.fecha_generado as reporte_fecha_generado'
                )
                ->orderBy('aptitud_necesidades.created_at', 'desc')
                ->get();

            // ========================================================
            // 10. HISTORIAL CLÍNICO (tabla: historial_clinico)
            // Columnas: id, id_usuario, fecha_inicio, fecha_actualizacion, ci_voluntario_accion
            // ========================================================
            $historialClinico = DB::table('historial_clinico')
                ->join('usuario', 'historial_clinico.id_usuario', '=', 'usuario.id_usuario')
                ->where('historial_clinico.ci_voluntario_accion', $ci)
                ->whereNull('historial_clinico.deleted_at')
                ->select(
                    'historial_clinico.id as id_historial',
                    'historial_clinico.id_usuario',
                    'historial_clinico.fecha_inicio',
                    'historial_clinico.fecha_actualizacion',
                    'historial_clinico.ci_voluntario_accion',
                    'usuario.ci as ci_usuario',
                    'usuario.nombres as usuario_nombres',
                    'usuario.apellidos as usuario_apellidos'
                )
                ->orderBy('historial_clinico.fecha_actualizacion', 'desc')
                ->get();

            // ========================================================
            // 11. ASIGNACIÓN DE NECESIDADES (tabla: reporte_necesidad)
            // Filtrado a través de reporte.ci_voluntario_accion
            // ========================================================
            $necesidadesAsignadas = DB::table('reporte_necesidad')
                ->join('necesidad', 'reporte_necesidad.id_necesidad', '=', 'necesidad.id')
                ->join('reporte', 'reporte_necesidad.id_reporte', '=', 'reporte.id')
                ->where('reporte.ci_voluntario_accion', $ci)
                ->whereNull('reporte.deleted_at')
                ->select(
                    'reporte_necesidad.id_reporte',
                    'reporte_necesidad.id_necesidad',
                    'reporte_necesidad.created_at',
                    'reporte_necesidad.updated_at',
                    'necesidad.tipo as necesidad_tipo',
                    'necesidad.descripcion as necesidad_descripcion',
                    'reporte.estado_general as reporte_estado_general',
                    'reporte.fecha_generado as reporte_fecha_generado',
                    'reporte.ci_voluntario_accion'
                )
                ->orderBy('reporte.fecha_generado', 'desc')
                ->get();

            // ========================================================
            // CONSTRUIR RESPUESTA JSON
            // ========================================================
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

        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error en trazabilidad: ' . $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }
}
