<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Evaluacion;
use App\Models\Reporte;
use App\Models\Test;
use App\Models\HistorialClinico;
use App\Models\Curso;
use App\Models\CursoRecomendacion;
use App\Mail\EvaluacionInvitacionMail;
use App\Services\IAService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Carbon\Carbon;

class EvaluacionVoluntarioController extends Controller
{
    /**
     * Enviar email de invitación al voluntario
     */
    public function enviarInvitacion(Request $request, $id)
    {
        try {
            $voluntario = User::where('id_usuario', $id)->firstOrFail();
            
            // Verificar que tenga email
            if (empty($voluntario->email)) {
                return response()->json([
                    'success' => false,
                    'message' => 'El voluntario no tiene un email registrado.'
                ], 400);
            }
            
            // Generar token único
            $token = Str::random(64);
            
            // Guardar token en la base de datos
            DB::table('evaluacion_tokens')->insert([
                'id_voluntario' => $voluntario->id_usuario,
                'token' => $token,
                'usado' => false,
                'fecha_expiracion' => Carbon::now()->addDays(7),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);
            
            // Enviar email
            Mail::to($voluntario->email)->send(new EvaluacionInvitacionMail($voluntario, $token));
            
            return response()->json([
                'success' => true,
                'message' => 'Formulario enviado correctamente a ' . $voluntario->email
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al enviar el formulario: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Mostrar la página de evaluación para el voluntario (vista restringida)
     */
    public function mostrarEvaluacion($token)
    {
        // Verificar token
        $tokenData = DB::table('evaluacion_tokens')
            ->where('token', $token)
            ->where('usado', false)
            ->where('fecha_expiracion', '>', Carbon::now())
            ->first();
            
        if (!$tokenData) {
            return view('evaluacion_voluntario.token_invalido');
        }
        
        $voluntario = User::where('id_usuario', $tokenData->id_voluntario)->first();
        
        if (!$voluntario) {
            return view('evaluacion_voluntario.token_invalido');
        }
        
        // Obtener tests disponibles
        $tests = Test::all();
        
        return view('evaluacion_voluntario.evaluacion', [
            'voluntario' => $voluntario,
            'token' => $token,
            'tests' => $tests
        ]);
    }
    
    /**
     * Procesar la evaluación del voluntario
     */
    public function procesarEvaluacion(Request $request, $token)
    {
        // Verificar token
        $tokenData = DB::table('evaluacion_tokens')
            ->where('token', $token)
            ->where('usado', false)
            ->where('fecha_expiracion', '>', Carbon::now())
            ->first();
            
        if (!$tokenData) {
            return response()->json([
                'success' => false,
                'message' => 'Token inválido o expirado'
            ], 400);
        }
        
        try {
            $voluntario = User::where('id_usuario', $tokenData->id_voluntario)->first();
            
            // DEBUG: Log para verificar datos del voluntario
            Log::info('DEBUG Voluntario obtenido', [
                'id_voluntario_token' => $tokenData->id_voluntario,
                'voluntario_encontrado' => $voluntario ? 'SI' : 'NO',
                'voluntario_id' => $voluntario->id_usuario ?? 'NULL',
                'voluntario_ci' => $voluntario->ci ?? 'NULL',
                'voluntario_completo' => $voluntario ? $voluntario->toArray() : 'NULL'
            ]);
            
            // Obtener o crear historial clínico del voluntario
            $historial = HistorialClinico::firstOrCreate(
                ['id_usuario' => $voluntario->id_usuario],
                [
                    'fecha_inicio' => Carbon::now(),
                    'fecha_actualizacion' => Carbon::now()
                ]
            );
            
            // Obtener las respuestas del voluntario
            $evaluacionFisica = $request->input('resumen_fisico', 'Sin evaluación física');
            $evaluacionEmocional = $request->input('resumen_emocional', 'Sin evaluación emocional');
            
            // Enviar a la IA para procesar
            $iaService = app(IAService::class);
            $resultadoIA = $iaService->generarEvaluacionCompleta($evaluacionFisica, $evaluacionEmocional);
            
            // Determinar los resúmenes (usar respuesta IA si está disponible, si no guardar las respuestas del usuario)
            $resumenFisico = $evaluacionFisica; // Respuestas del voluntario como fallback
            $resumenEmocional = $evaluacionEmocional;
            $estadoGeneral = 'Completado';
            
            if ($resultadoIA['success']) {
                // Si la IA respondió correctamente, usar sus respuestas
                $resumenFisico = $resultadoIA['fisico']['respuesta'] ?? $evaluacionFisica;
                $resumenEmocional = $resultadoIA['emocional']['respuesta'] ?? $evaluacionEmocional;
                $estadoGeneral = 'Procesado por IA';
                Log::info('Evaluación procesada por IA', [
                    'voluntario_id' => $voluntario->id_usuario,
                    'fisico_ok' => $resultadoIA['fisico']['success'] ?? false,
                    'emocional_ok' => $resultadoIA['emocional']['success'] ?? false
                ]);
            } else {
                Log::warning('IA no disponible, guardando respuestas del voluntario', [
                    'voluntario_id' => $voluntario->id_usuario,
                    'error' => $resultadoIA
                ]);
            }
            
            // Crear reporte con los resultados
            // respuestas_fisico/emocional = lo que respondió el usuario
            // resumen_fisico/emocional = análisis de la IA
            $reporte = Reporte::create([
                'id_historial' => $historial->id,
                'respuestas_fisico' => $evaluacionFisica,
                'respuestas_emocional' => $evaluacionEmocional,
                'resumen_fisico' => $resumenFisico,
                'resumen_emocional' => $resumenEmocional,
                'estado_general' => $estadoGeneral,
                'observaciones' => $request->input('observaciones', ''),
                'fecha_generado' => Carbon::now(),
                'ci_voluntario_accion' => $voluntario->ci // Trazabilidad API Gateway
            ]);
            
            // Actualizar historial
            $historial->update(['fecha_actualizacion' => Carbon::now()]);
            
            // Crear evaluación (relacionada con el test de evaluación física/psicológica)
            // Buscar o crear el test si no existe
            $test = Test::first();
            if (!$test) {
                $test = Test::create([
                    'nombre' => 'Evaluación Física y Emocional',
                    'categoria' => 'mixto',
                    'descripcion' => 'Evaluación integral de condición física y emocional del voluntario'
                ]);
            }
            
            Evaluacion::create([
                'id_reporte' => $reporte->id,
                'id_test' => $test->id,
                'id_universidad' => null,
                'fecha' => Carbon::now(),
                'ci_voluntario_accion' => $voluntario->ci // Trazabilidad API Gateway
            ]);
            
            
            // ========================================
            // GENERAR RECOMENDACIONES DE CURSOS CON GOOGLE GEMINI
            // ========================================
            try {
                // Obtener todos los cursos disponibles
                $cursosRaw = DB::table('curso')
                    ->join('capacitacion', 'curso.id_capacitacion', '=', 'capacitacion.id')
                    ->select(
                        'curso.id',
                        'curso.nombre',
                        'curso.descripcion',
                        'capacitacion.nombre as capacitacion_nombre'
                    )
                    ->get();

                // Convertir objetos stdClass a arrays
                $cursos = [];
                foreach ($cursosRaw as $curso) {
                    $cursos[] = [
                        'id' => $curso->id,
                        'nombre' => $curso->nombre,
                        'descripcion' => $curso->descripcion,
                        'capacitacion_nombre' => $curso->capacitacion_nombre
                    ];
                }

                if (count($cursos) > 0) {
                    Log::info('Generando recomendaciones de cursos', [
                        'voluntario_id' => $voluntario->id_usuario,
                        'total_cursos' => count($cursos)
                    ]);

                    // Llamar a la IA de Google Gemini
                    $recomendacion = $iaService->recomendarCursos(
                        $resumenFisico,
                        $resumenEmocional,
                        $cursos,
                        $voluntario->nombres . ' ' . $voluntario->apellidos
                    );

                    if ($recomendacion['success'] && ($recomendacion['tiene_recomendacion'] ?? false)) {
                        // HAY RECOMENDACIÓN(ES): Eliminar recomendaciones anteriores y crear nuevas
                        CursoRecomendacion::where('id_voluntario', $voluntario->id_usuario)->delete();

                        $cursosRecomendados = $recomendacion['cursos'] ?? [];
                        
                        foreach ($cursosRecomendados as $cursoRec) {
                            CursoRecomendacion::create([
                                'id_voluntario' => $voluntario->id_usuario,
                                'id_curso' => $cursoRec['id'],
                                'id_reporte' => $reporte->id,
                                'mensaje_ia' => $cursoRec['nombre'],
                                'razon' => $cursoRec['razon'] ?? '',
                                'estado' => 'pendiente',
                                'ci_voluntario_accion' => $voluntario->ci // Trazabilidad API Gateway
                            ]);

                            Log::info('Recomendación de curso CREADA', [
                                'voluntario_id' => $voluntario->id_usuario,
                                'curso_id' => $cursoRec['id'],
                                'tipo' => $cursoRec['tipo'] ?? 'N/A',
                                'nombre' => $cursoRec['nombre']
                            ]);
                        }

                        Log::info('Recomendaciones procesadas', [
                            'voluntario_id' => $voluntario->id_usuario,
                            'total_recomendaciones' => count($cursosRecomendados)
                        ]);
                    } elseif ($recomendacion['success'] && !($recomendacion['tiene_recomendacion'] ?? true)) {
                        // NO HAY RECOMENDACIÓN (voluntario está bien): Eliminar recomendaciones anteriores
                        $eliminadas = CursoRecomendacion::where('id_voluntario', $voluntario->id_usuario)->delete();
                        
                        Log::info('Recomendaciones eliminadas - voluntario sin padecimientos', [
                            'voluntario_id' => $voluntario->id_usuario,
                            'recomendaciones_eliminadas' => $eliminadas,
                            'razon' => $recomendacion['mensaje'] ?? 'Voluntario en rangos normales'
                        ]);
                    } else {
                        Log::info('No se generó recomendación de curso', [
                            'voluntario_id' => $voluntario->id_usuario,
                            'razon' => $recomendacion['mensaje'] ?? 'Sin razón'
                        ]);
                    }
                }
            } catch (\Exception $e) {
                // No fallar todo el proceso si falla la recomendación
                Log::error('Error al generar recomendación de curso', [
                    'voluntario_id' => $voluntario->id_usuario,
                    'error' => $e->getMessage()
                ]);
            }

            // ===============================================
            // EVALUAR APTITUD PARA ASIGNAR NECESIDADES
            // ===============================================
            try {
                // Obtener necesidades disponibles
                $necesidades = \App\Models\Necesidad::select('id', 'tipo', 'descripcion')
                    ->get()
                    ->toArray();

                if (!empty($necesidades)) {
                    // Llamar a la IA para evaluar aptitud
                    $evaluacionAptitud = $iaService->evaluarAptitudNecesidades(
                        $resumenFisico,
                        $resumenEmocional,
                        $necesidades
                    );

                    if ($evaluacionAptitud['success']) {
                        // Eliminar evaluación anterior
                        \App\Models\AptitudNecesidad::where('id_voluntario', $voluntario->id_usuario)->delete();

                        // Crear nueva evaluación de aptitud
                        \App\Models\AptitudNecesidad::create([
                            'id_voluntario' => $voluntario->id_usuario,
                            'id_necesidad' => null, // Evaluación general
                            'id_reporte' => $reporte->id,
                            'nivel_aptitud' => $evaluacionAptitud['nivel_aptitud'],
                            'razon_ia' => $evaluacionAptitud['razon'],
                            'necesidades_recomendadas' => json_encode($evaluacionAptitud['necesidades_aptas']),
                            'estado' => 'activo',
                            'ci_voluntario_accion' => $voluntario->ci // Trazabilidad API Gateway
                        ]);

                        Log::info('Aptitud de necesidades evaluada', [
                            'voluntario_id' => $voluntario->id_usuario,
                            'nivel' => $evaluacionAptitud['nivel_aptitud'],
                            'necesidades_aptas' => count($evaluacionAptitud['necesidades_aptas'])
                        ]);
                    }
                }
            } catch (\Exception $e) {
                Log::error('Error al evaluar aptitud de necesidades', [
                    'voluntario_id' => $voluntario->id_usuario,
                    'error' => $e->getMessage()
                ]);
            }
            
            // Marcar token como usado (al final, solo si todo fue exitoso)
            DB::table('evaluacion_tokens')
                ->where('token', $token)
                ->update([
                    'usado' => true,
                    'updated_at' => Carbon::now()
                ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Evaluación completada correctamente',
                'reporte_id' => $reporte->id
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al procesar la evaluación: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Obtener historial de encuestas realizadas por un voluntario
     */
    public function historialEncuestas($idVoluntario)
    {
        $historial = HistorialClinico::where('id_usuario', $idVoluntario)->first();
        
        if (!$historial) {
            return response()->json([
                'success' => true,
                'evaluaciones' => []
            ]);
        }
        
        $reportes = Reporte::where('id_historial', $historial->id)
            ->orderBy('fecha_generado', 'desc')
            ->get();
            
        return response()->json([
            'success' => true,
            'evaluaciones' => $reportes
        ]);
    }
    
    /**
     * Ver detalle de un reporte/encuesta realizada
     */
    public function verReporte($id, $tipo = 'fisico')
{
    // ✅ AGREGAR: Marcar como visto en la sesión
    $reportesVistos = session()->get('reportes_vistos', []);
    $key = $id . '_' . $tipo;
    
    if (!in_array($key, $reportesVistos)) {
        $reportesVistos[] = $key;
        session()->put('reportes_vistos', $reportesVistos);
    }

    // ... MANTENER TODO LO DEMÁS TAL CUAL ESTÁ ...
    $reporte = Reporte::find($id);
    
    if (!$reporte) {
        abort(404, 'Reporte no encontrado');
    }
    
    // Obtener el voluntario a través del historial clínico
    $historial = HistorialClinico::find($reporte->id_historial);
    $voluntario = null;
    
    if ($historial) {
        $voluntario = User::where('id_usuario', $historial->id_usuario)->first();
    }
    
    // Obtener universidades para el combo de asignar
    $universidades = DB::table('universidad')->orderBy('nombre')->get();
    
    // Parsear las respuestas del usuario (de los nuevos campos respuestas_*)
    // Si no existen, intentar parsear del resumen (para reportes antiguos)
    $textoFisico = $reporte->respuestas_fisico ?? $reporte->resumen_fisico;
    $textoEmocional = $reporte->respuestas_emocional ?? $reporte->resumen_emocional;
    
    $respuestasFisicas = $this->parsearRespuestas($textoFisico);
    $respuestasEmocionales = $this->parsearRespuestas($textoEmocional);
    
    // Parsear el estado del cuerpo del resumen físico
    $estadoCuerpo = $this->parsearEstadoCuerpo($textoFisico);
    
    return view('reportes.detalle', compact(
        'reporte',
        'voluntario',
        'universidades',
        'respuestasFisicas',
        'respuestasEmocionales',
        'estadoCuerpo',
        'tipo'
    ));
}
    


    /**
     * Parsear respuestas del resumen
     */
    private function parsearRespuestas($resumen)
    {
        $respuestas = [];
        
        if (empty($resumen)) {
            return $respuestas;
        }
        
        // Preguntas físicas
        $preguntasFisicas = [
            'f1' => '¿Te sientes más cansado o agotado de lo habitual después de las intervenciones?',
            'f2' => '¿Has notado quemaduras, irritación o enrojecimiento en la piel después de las intervenciones?',
            'f3' => '¿Has tenido dificultades para respirar o tos después de las intervenciones?',
            'f4' => '¿Tienes dolor o molestias en el pecho desde el incendio?',
            'f5' => '¿Has experimentado palpitaciones o un ritmo cardíaco irregular después de la intervención?',
            'f6' => '¿Tus ojos han estado irritados, con ardor o picazón desde la intervención?',
            'f7' => '¿Tienes dificultad para respirar profundamente desde la intervención?',
            'f8' => '¿Has notado que tu nariz está congestionada o bloqueada más de lo normal?',
        ];
        
        // Preguntas psicológicas
        $preguntasPsicologicas = [
            'p1' => '¿Con qué frecuencia has tenido pensamientos no deseados relacionados al incendio?',
            'p2' => '¿Sientes que últimamente piensas en qué pudiste hacer diferente durante la intervención?',
            'p3' => '¿Has notado disminución de apetito desde la intervención?',
            'p4' => '¿Te resulta difícil relajarte o desconectar mentalmente después de las intervenciones?',
            'p5' => '¿Has tenido dificultades para concentrarte en tus tareas diarias debido al estrés?',
            'p6' => '¿Has sufrido de insomnio recientemente?',
            'p7' => '¿Te has sentido emocionalmente más inestable o irritable desde el incendio?',
            'p8' => '¿Te sientes preocupado o ansioso constantemente desde el incendio?',
        ];
        
        $opciones = ['Nunca', 'Raramente', 'A veces', 'Frecuentemente', 'Siempre'];
        
        // Primero intentar formato compacto: f1:Nunca,f2:Siempre...
        $todasPreguntas = array_merge($preguntasFisicas, $preguntasPsicologicas);
        
        foreach ($todasPreguntas as $key => $pregunta) {
            foreach ($opciones as $opcion) {
                // Formato compacto: f1:Nunca
                if (preg_match('/' . $key . ':' . preg_quote($opcion, '/') . '(?:,|$|\[)/i', $resumen)) {
                    $respuestas[] = [
                        'pregunta' => $pregunta,
                        'respuesta' => $opcion
                    ];
                    break;
                }
                // Formato antiguo: pregunta completa: respuesta
                if (strpos($resumen, $pregunta . ': ' . $opcion) !== false) {
                    $respuestas[] = [
                        'pregunta' => $pregunta,
                        'respuesta' => $opcion
                    ];
                    break;
                }
            }
        }
        
        return $respuestas;
    }
    
    /**
     * Parsear estado del cuerpo del resumen
     */
    private function parsearEstadoCuerpo($resumen)
    {
        $estadoCuerpo = [
            'head' => 'normal',
            'leftShoulder' => 'normal',
            'rightShoulder' => 'normal',
            'leftArm' => 'normal',
            'rightArm' => 'normal',
            'chest' => 'normal',
            'stomach' => 'normal',
            'leftLeg' => 'normal',
            'rightLeg' => 'normal',
            'leftHand' => 'normal',
            'rightHand' => 'normal',
            'leftFoot' => 'normal',
            'rightFoot' => 'normal',
        ];
        
        if (empty($resumen)) {
            return $estadoCuerpo;
        }
        
        // Mapeo de abreviaturas a nombres de partes
        $abrevToKey = [
            'h' => 'head',
            'ls' => 'leftShoulder',
            'rs' => 'rightShoulder',
            'la' => 'leftArm',
            'ra' => 'rightArm',
            'c' => 'chest',
            's' => 'stomach',
            't' => 'chest', // compatibilidad con formato antiguo
            'll' => 'leftLeg',
            'rl' => 'rightLeg',
            'lh' => 'leftHand',
            'rh' => 'rightHand',
            'lf' => 'leftFoot',
            'rf' => 'rightFoot',
        ];
        
        // Mapeo de números a estados
        $numToEstado = [
            '1' => 'muybien',
            '2' => 'bien',
            '3' => 'normal',
            '4' => 'mal',
            '5' => 'muymal',
        ];
        
        // Formato compacto [BC]h:5|ls:2|...[/BC]
        if (preg_match('/\[BC\](.*?)\[\/BC\]/s', $resumen, $matches)) {
            $partes = explode('|', trim($matches[1]));
            
            foreach ($partes as $parte) {
                $parte = trim($parte);
                if (preg_match('/^([a-z]+):(\d)$/i', $parte, $m)) {
                    $abrev = strtolower($m[1]);
                    $num = $m[2];
                    
                    if (isset($abrevToKey[$abrev]) && isset($numToEstado[$num])) {
                        $key = $abrevToKey[$abrev];
                        $estadoCuerpo[$key] = $numToEstado[$num];
                    }
                }
            }
            
            return $estadoCuerpo;
        }
        
        // Formato anterior [ESTADO_CUERPO]...[/ESTADO_CUERPO]
        $mapeoPartes = [
            'Cabeza' => 'head',
            'Hombro Izquierdo' => 'leftShoulder',
            'Hombro Derecho' => 'rightShoulder',
            'Brazo Izquierdo' => 'leftArm',
            'Brazo Derecho' => 'rightArm',
            'Pecho' => 'chest',
            'Abdomen' => 'stomach',
            'Torso' => 'chest', // compatibilidad
            'Pierna Izquierda' => 'leftLeg',
            'Pierna Derecha' => 'rightLeg',
            'Mano Izquierda' => 'leftHand',
            'Mano Derecha' => 'rightHand',
            'Pie Izquierdo' => 'leftFoot',
            'Pie Derecho' => 'rightFoot',
        ];
        
        if (preg_match('/\[ESTADO_CUERPO\](.*?)\[\/ESTADO_CUERPO\]/s', $resumen, $matches)) {
            $partes = explode(' | ', trim($matches[1]));
            
            foreach ($partes as $parte) {
                if (preg_match('/^(.+?):\s*(muybien|bien|normal|mal|muymal)$/i', trim($parte), $m)) {
                    $nombreParte = trim($m[1]);
                    $estado = strtolower(trim($m[2]));
                    
                    if (isset($mapeoPartes[$nombreParte])) {
                        $estadoCuerpo[$mapeoPartes[$nombreParte]] = $estado;
                    }
                }
            }
        }
        
        // Formato nuevo: "Partes del cuerpo con molestias: Cabeza (muy mal), Brazo Izquierdo (mal)."
        if (preg_match('/Partes del cuerpo con molestias:\s*(.+?)\.?$/i', $resumen, $matches)) {
            $partesTexto = trim($matches[1]);
            // Dividir por coma
            $partes = preg_split('/,\s*/', $partesTexto);
            
            // Mapeo de estados con espacios a sin espacios
            $estadosMap = [
                'muy bien' => 'muybien',
                'bien' => 'bien',
                'normal' => 'normal',
                'mal' => 'mal',
                'muy mal' => 'muymal',
            ];
            
            foreach ($partes as $parte) {
                // Formato: "Nombre Parte (estado)"
                if (preg_match('/^(.+?)\s*\((.+?)\)$/i', trim($parte), $m)) {
                    $nombreParte = trim($m[1]);
                    $estadoTexto = strtolower(trim($m[2]));
                    
                    // Convertir estado con espacio a sin espacio
                    $estado = $estadosMap[$estadoTexto] ?? $estadoTexto;
                    
                    if (isset($mapeoPartes[$nombreParte])) {
                        $estadoCuerpo[$mapeoPartes[$nombreParte]] = $estado;
                    }
                }
            }
        }
        
        return $estadoCuerpo;
    }
}
