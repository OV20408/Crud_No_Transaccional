<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Capacitacion;
use App\Models\Curso;
use App\Models\Etapa;
use App\Models\ProgresoVoluntario;
use Illuminate\Http\Request;
use App\Events\CursoCreadoOActualizado;

class CapacitacionApiController extends Controller
{
    /**
     * Listar todas las capacitaciones con sus cursos
     */
    public function index()
    {
        $capacitaciones = Capacitacion::with('cursos.etapas')->get();
        return response()->json([
            'success' => true,
            'data' => $capacitaciones
        ]);
    }

    /**
     * Crear una nueva capacitación
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'nombre' => 'required|string|max:255',
                'descripcion' => 'nullable|string|max:255',
            ]);

            $capacitacion = Capacitacion::create($request->only(['nombre', 'descripcion']));

            return response()->json([
                'success' => true,
                'message' => 'Capacitación creada exitosamente',
                'data' => $capacitacion
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear capacitación: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mostrar una capacitación específica con cursos y etapas
     */
    public function show($id)
    {
        $capacitacion = Capacitacion::with('cursos.etapas')->find($id);
        
        if (!$capacitacion) {
            return response()->json([
                'success' => false,
                'message' => 'Capacitación no encontrada'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $capacitacion
        ]);
    }

    /**
     * Obtener cursos asignados a un voluntario con su progreso
     */
    public function getCursosByVoluntario($idUsuario)
    {
        $progresos = ProgresoVoluntario::where('id_usuario', $idUsuario)
            ->with(['etapa.curso.capacitacion'])
            ->get();

        // Agrupar por curso
        $cursosMap = [];
        foreach ($progresos as $progreso) {
            $curso = $progreso->etapa->curso;
            $cursoId = $curso->id;

            if (!isset($cursosMap[$cursoId])) {
                $cursosMap[$cursoId] = [
                    'id' => $curso->id,
                    'nombre' => $curso->nombre,
                    'descripcion' => $curso->descripcion,
                    'capacitacion' => $curso->capacitacion->nombre ?? null,
                    'etapas' => []
                ];
            }

            $cursosMap[$cursoId]['etapas'][] = [
                'id' => $progreso->etapa->id,
                'nombre' => $progreso->etapa->nombre,
                'orden' => $progreso->etapa->orden,
                'estado' => $progreso->estado,
                'fechaInicio' => $progreso->fecha_inicio,
                'fechaFinalizacion' => $progreso->fecha_finalizacion
            ];
        }

        // Ordenar etapas por orden
        foreach ($cursosMap as &$curso) {
            usort($curso['etapas'], fn($a, $b) => $a['orden'] <=> $b['orden']);
        }

        return response()->json([
            'success' => true,
            'data' => array_values($cursosMap)
        ]);
    }

    /**
     * Asignar un curso a un voluntario (inscribir en todas sus etapas)
     */
    public function asignarCursoAVoluntario(Request $request)
    {
        try {
            $request->validate([
                'id_usuario' => 'required|integer',
                'id_curso' => 'required|integer',
            ]);

            $curso = Curso::with('etapas')->find($request->id_curso);
            
            if (!$curso) {
                return response()->json([
                    'success' => false,
                    'message' => 'Curso no encontrado'
                ], 404);
            }

            if ($curso->etapas->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'El curso no tiene etapas definidas'
                ], 400);
            }

            $asignados = [];
            foreach ($curso->etapas as $index => $etapa) {
                // Verificar si ya está asignado
                $existe = ProgresoVoluntario::where('id_usuario', $request->id_usuario)
                    ->where('id_etapa', $etapa->id)
                    ->first();

                if (!$existe) {
                    $progreso = ProgresoVoluntario::create([
                        'id_usuario' => $request->id_usuario,
                        'id_etapa' => $etapa->id,
                        'estado' => $index === 0 ? 'en_progreso' : 'sin_empezar',
                        'fecha_inicio' => $index === 0 ? now() : null,
                        'ci_voluntario_accion' => \App\Models\User::where('id_usuario', $request->id_usuario)->value('ci'), // Trazabilidad API Gateway
                    ]);
                    $asignados[] = $progreso;
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Curso asignado exitosamente',
                'data' => [
                    'curso' => $curso->nombre,
                    'etapas_asignadas' => count($asignados)
                ]
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al asignar curso: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Crear un curso dentro de una capacitación
     */
    public function storeCurso(Request $request)
    {
        try {
            $request->validate([
                'nombre' => 'required|string|max:255',
                'descripcion' => 'nullable|string|max:255',
                'id_capacitacion' => 'required|integer|exists:capacitacion,id',
            ]);

            // 🚀 CROSS-CHECK CON INCENDIOS
            $remote = app(\App\Services\RemoteIncendiosCursosService::class)
                ->cursoExiste($request->nombre);

            if ($remote['exists']) {
                return response()->json([
                    'success' => false,
                    'message' => 'El curso ya existe en INCENDIOS ALAS'
                ], 409);
            }

            // Crear local
            $curso = Curso::create($request->only(['nombre', 'descripcion', 'id_capacitacion']));

            // 🚀 Disparar evento de sincronización automática
            CursoCreadoOActualizado::dispatch($curso);

            return response()->json([
                'success' => true,
                'message' => 'Curso creado exitosamente',
                'data' => $curso
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear curso: ' . $e->getMessage()
            ], 500);
        }
    }


    /**
     * Crear una etapa dentro de un curso
     */
    public function storeEtapa(Request $request)
    {
        try {
            $request->validate([
                'nombre' => 'required|string|max:255',
                'descripcion' => 'nullable|string|max:255',
                'orden' => 'required|integer',
                'id_curso' => 'required|integer|exists:curso,id',
            ]);

            $etapa = Etapa::create($request->only(['nombre', 'descripcion', 'orden', 'id_curso']));

            return response()->json([
                'success' => true,
                'message' => 'Etapa creada exitosamente',
                'data' => $etapa
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear etapa: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cambiar el estado de una etapa para un voluntario
     */
    public function cambiarEstadoEtapa(Request $request, $idProgreso)
    {
        try {
            $progreso = ProgresoVoluntario::find($idProgreso);

            if (!$progreso) {
                return response()->json([
                    'success' => false,
                    'message' => 'Progreso no encontrado'
                ], 404);
            }

            // Ciclar estados: sin_empezar -> en_progreso -> completado -> sin_empezar
            $estados = ['sin_empezar', 'en_progreso', 'completado'];
            $estadoActual = $progreso->estado;
            $indiceActual = array_search($estadoActual, $estados);
            $nuevoEstado = $estados[($indiceActual + 1) % count($estados)];

            $progreso->estado = $nuevoEstado;
            
            if ($nuevoEstado === 'en_progreso' && !$progreso->fecha_inicio) {
                $progreso->fecha_inicio = now();
            } elseif ($nuevoEstado === 'completado') {
                $progreso->fecha_finalizacion = now();
            } elseif ($nuevoEstado === 'sin_empezar') {
                $progreso->fecha_inicio = null;
                $progreso->fecha_finalizacion = null;
            }

            $progreso->save();

            return response()->json([
                'success' => true,
                'message' => 'Estado actualizado',
                'data' => [
                    'id' => $progreso->id,
                    'estado' => $progreso->estado,
                    'fecha_inicio' => $progreso->fecha_inicio,
                    'fecha_finalizacion' => $progreso->fecha_finalizacion
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cambiar estado: ' . $e->getMessage()
            ], 500);
        }
    }
}
