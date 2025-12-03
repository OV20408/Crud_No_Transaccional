<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Rol;
use App\Models\Capacitacion;
use App\Models\ProgresoVoluntario;
use App\Models\CursoRecomendacion;
use Barryvdh\DomPDF\Facade\Pdf;

class VoluntarioController extends Controller
{
    public function index(Request $request)
    {
        $query = DB::table('usuario')
            ->join('rol', 'usuario.id_rol', '=', 'rol.id')
            ->where('rol.nombre', 'Voluntario')
            ->select('usuario.*');

        if ($request->filled('q')) {
            $query->where(function ($q) use ($request) {
                $q->where('usuario.nombres', 'ILIKE', '%' . $request->q . '%')
                  ->orWhere('usuario.apellidos', 'ILIKE', '%' . $request->q . '%');
            });
        }

        if ($request->filled('ci')) {
            $query->where('usuario.ci', 'LIKE', '%' . $request->ci . '%');
        }

        if ($request->filled('tipo_sangre')) {
            $query->where('usuario.tipo_sangre', $request->tipo_sangre);
        }

        if ($request->filled('estado')) {
            $query->where('usuario.estado', 'ILIKE', $request->estado);
        }

        $voluntarios = $query->get();

        return view('voluntarios.index', compact('voluntarios'));
    }

    public function create()
    {
        return view('voluntarios.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombres'             => 'required|string|max:255',
            'apellidos'           => 'required|string|max:255',
            'ci'                  => 'required|string|max:255|unique:usuario,ci',
            'fecha_nacimiento'    => [
                'nullable',
                'date',
                'before:today',
                'before_or_equal:' . now()->subYears(18)->format('Y-m-d')
            ],
            'genero'              => 'nullable|string|max:50',
            'telefono'            => 'nullable|string|max:255',
            'email'               => 'required|email|max:255|unique:usuario,email',
            'direccion_domicilio' => 'nullable|string|max:255',
            'estado'              => 'nullable|string|max:50',
            'nivel_entrenamiento' => 'nullable|string|max:255',
            'entidad_pertenencia' => 'nullable|string|max:255',
            'tipo_sangre'         => 'nullable|string|max:10',
        ],
        [
            'fecha_nacimiento.before' => 'La fecha de nacimiento debe ser una fecha pasada.',
            'fecha_nacimiento.before_or_equal' => 'El voluntario debe tener al menos 18 años de edad.',
        ]);

        $rolVoluntarioId = Rol::where('nombre', 'Voluntario')->value('id');

        if (!$rolVoluntarioId) {
            abort(500, 'Rol "Voluntario" no está configurado en la tabla rol.');
        }

        $passwordTemporal = Str::random(12);

        $user = User::create([
            'nombres'             => $validated['nombres'],
            'apellidos'           => $validated['apellidos'],
            'ci'                  => $validated['ci'],
            'fecha_nacimiento'    => $validated['fecha_nacimiento'] ?? null,
            'genero'              => $validated['genero'] ?? null,
            'telefono'            => $validated['telefono'] ?? null,
            'email'               => $validated['email'],
            'direccion_domicilio' => $validated['direccion_domicilio'] ?? null,
            'estado'              => $validated['estado'] ?? 'activo',
            'id_rol'              => $rolVoluntarioId,
            'nivel_entrenamiento' => $validated['nivel_entrenamiento'] ?? null,
            'entidad_pertenencia' => $validated['entidad_pertenencia'] ?? null,
            'tipo_sangre'         => $validated['tipo_sangre'] ?? null,
            'password'            => $passwordTemporal,
        ]);

        DB::table('historial_clinico')->insert([
            'id_usuario'         => $user->id_usuario,
            'fecha_inicio'       => now(),
            'fecha_actualizacion'=> now(),
        ]);

        if (!empty($user->email)) {
            Password::sendResetLink(['email' => $user->email]);
        }

        // Guardar el ID del voluntario recién creado en la sesión
        return redirect()
            ->route('voluntarios.index')
            ->with('nuevo_voluntario_id', $user->id_usuario)
            ->with('success', 'Voluntario creado correctamente. Se envió un correo para que configure su contraseña.');
    }



    public function show($id)
    {
        // 1. Obtener voluntario
        $voluntario = DB::table('usuario')
            ->join('rol', 'usuario.id_rol', '=', 'rol.id')
            ->where('usuario.id_usuario', $id)
            ->where('rol.nombre', 'Voluntario')
            ->select('usuario.*')
            ->first();

        if (!$voluntario) {
            abort(404, 'Voluntario no encontrado');
        }

        // 2. Obtener historial clínico
        $historial = DB::table('historial_clinico')
            ->where('id_usuario', $id)
            ->first();

        // 3. Reportes del voluntario
        $reportes = DB::select("
            SELECT DISTINCT r.*
            FROM reporte r
            LEFT JOIN reporte_progreso_voluntario rpv ON rpv.id_reporte = r.id
            LEFT JOIN progreso_voluntario pv ON pv.id = rpv.id_progreso
            LEFT JOIN historial_clinico hc ON hc.id = r.id_historial
            WHERE pv.id_usuario = ? OR hc.id_usuario = ?
            ORDER BY r.fecha_generado DESC
        ", [$id, $id]);

        // 4. Reporte más reciente (para capacitaciones y necesidades)
        $reporteMasRecienteGeneral = $reportes[0] ?? null;
        
        // 4.1 Reporte más reciente CON evaluaciones (para mostrar en la vista)
        $reporteMasReciente = null;
        foreach ($reportes as $reporte) {
            if ($reporte->resumen_fisico || $reporte->resumen_emocional) {
                $reporteMasReciente = $reporte;
                break;
            }
        }

        // 5. Capacitaciones del último reporte
        $capacitaciones = [];
        if ($reporteMasRecienteGeneral) {
            $capacitaciones = DB::select("
                SELECT DISTINCT c.*
                FROM reporte_progreso_voluntario rpv
                JOIN progreso_voluntario pv ON pv.id = rpv.id_progreso
                JOIN etapa e ON e.id = pv.id_etapa
                JOIN curso cu ON cu.id = e.id_curso
                JOIN capacitacion c ON c.id = cu.id_capacitacion
                WHERE rpv.id_reporte = ?
            ", [$reporteMasRecienteGeneral->id]);
        }

        // 6. Necesidades del último reporte
        $necesidades = [];
        if ($reporteMasRecienteGeneral) {
            $necesidades = DB::table('reporte_necesidad')
                ->join('necesidad', 'reporte_necesidad.id_necesidad', '=', 'necesidad.id')
                ->where('reporte_necesidad.id_reporte', $reporteMasRecienteGeneral->id)
                ->select('necesidad.*')
                ->get();
        }

        // 7. TODAS LAS NECESIDADES DEL SISTEMA (para asignar)
        $necesidadesAll = DB::table('necesidad')
            ->orderBy('tipo')
            ->orderBy('descripcion')
            ->get();

        // 8. NECESIDADES ASIGNADAS AL VOLUNTARIO
        $necesidadesAsignadas = DB::table('reporte_necesidad')
            ->join('reporte', 'reporte.id', '=', 'reporte_necesidad.id_reporte')
            ->join('historial_clinico', 'historial_clinico.id', '=', 'reporte.id_historial')
            ->join('necesidad', 'necesidad.id', '=', 'reporte_necesidad.id_necesidad')
            ->where('historial_clinico.id_usuario', $id)
            ->select('necesidad.*', 'reporte.fecha_generado')
            ->orderBy('reporte.fecha_generado', 'desc')
            ->get();

        // 9. CURSOS DEL VOLUNTARIO
        $cursos = DB::select("
            SELECT DISTINCT 
                cu.id,
                cu.nombre,
                cu.descripcion,
                cap.id AS capacitacion_id,
                cap.nombre AS capacitacion_nombre
            FROM progreso_voluntario pv
            JOIN etapa e ON e.id = pv.id_etapa
            JOIN curso cu ON cu.id = e.id_curso
            JOIN capacitacion cap ON cap.id = cu.id_capacitacion
            WHERE pv.id_usuario = ?
            ORDER BY cu.nombre
        ", [$id]);

        // 10. Evaluaciones del voluntario
        $evaluaciones = [];
        if ($historial) {
            $evaluaciones = DB::table('reporte')
                ->join('evaluacion', 'evaluacion.id_reporte', '=', 'reporte.id')
                ->join('test', 'evaluacion.id_test', '=', 'test.id')
                ->where('reporte.id_historial', $historial->id)
                ->select(
                    'reporte.id as reporte_id',
                    'reporte.resumen_fisico',
                    'reporte.resumen_emocional',
                    'reporte.estado_general',
                    'reporte.fecha_generado',
                    'evaluacion.id as evaluacion_id',
                    'evaluacion.fecha',
                    'test.nombre as test_nombre'
                )
                ->orderBy('reporte.fecha_generado', 'desc')
                ->get();
        }
        
        if (empty($evaluaciones) || count($evaluaciones) == 0) {
            $reportesEvaluacion = DB::table('reporte')
                ->join('historial_clinico', 'historial_clinico.id', '=', 'reporte.id_historial')
                ->where('historial_clinico.id_usuario', $id)
                ->select(
                    'reporte.id as reporte_id',
                    'reporte.resumen_fisico',
                    'reporte.resumen_emocional',
                    'reporte.estado_general',
                    'reporte.fecha_generado'
                )
                ->orderBy('reporte.fecha_generado', 'desc')
                ->get();
                
            $evaluaciones = $reportesEvaluacion->map(function($reporte) {
                return (object)[
                    'reporte_id' => $reporte->reporte_id,
                    'resumen_fisico' => $reporte->resumen_fisico,
                    'resumen_emocional' => $reporte->resumen_emocional,
                    'estado_general' => $reporte->estado_general,
                    'fecha_generado' => $reporte->fecha_generado,
                    'fecha' => $reporte->fecha_generado,
                    'test_nombre' => 'Evaluación Física y Psicológica'
                ];
            });
        }

        // 11. Capacitaciones con progreso
        $capacitacionesProgreso = DB::select("
            SELECT DISTINCT 
                c.*
            FROM progreso_voluntario pv
            JOIN etapa e   ON e.id = pv.id_etapa
            JOIN curso cu  ON cu.id = e.id_curso
            JOIN capacitacion c ON c.id = cu.id_capacitacion
            WHERE pv.id_usuario = ?
            ORDER BY c.nombre
        ", [$id]);

        $capacitacionesAll = Capacitacion::orderBy('nombre')->get();

        $reportesVistos = session()->get('reportes_vistos', []);
        
        $reportesNoVistos = [];
        foreach ($reportes as $reporte) {
            $reportesNoVistos[] = [
                'reporte_id' => $reporte->id,
                'fisico_no_visto' => !in_array($reporte->id . '_fisico', $reportesVistos) && $reporte->resumen_fisico ? 'fisico' : null,
                'emocional_no_visto' => !in_array($reporte->id . '_emocional', $reportesVistos) && $reporte->resumen_emocional ? 'emocional' : null,
            ];
        }

        // 12. RECOMENDACIONES MÁS RECIENTES DE CURSOS POR LA IA (hasta 2)
        $recomendacionesCursos = DB::table('curso_recomendaciones')
            ->leftJoin('curso', 'curso_recomendaciones.id_curso', '=', 'curso.id')
            ->leftJoin('capacitacion', 'curso.id_capacitacion', '=', 'capacitacion.id')
            ->where('curso_recomendaciones.id_voluntario', $id)
            ->select(
                'curso_recomendaciones.*',
                'curso.nombre as curso_nombre',
                'curso.descripcion as curso_descripcion',
                'capacitacion.nombre as capacitacion_nombre'
            )
            ->orderBy('curso_recomendaciones.updated_at', 'desc')
            ->limit(2) // Hasta 2 recomendaciones
            ->get();

        // 13. APTITUD PARA ASIGNAR NECESIDADES (evaluada por IA)
        $aptitudNecesidades = \App\Models\AptitudNecesidad::where('id_voluntario', $id)
            ->where('estado', 'activo')
            ->orderBy('updated_at', 'desc')
            ->first();

        return view('voluntarios.show', compact(
            'voluntario',
            'historial',
            'reportes',
            'reporteMasReciente',
            'necesidades',
            'cursos',
            'evaluaciones',
            'capacitacionesProgreso',
            'capacitacionesAll',
            'necesidadesAll',
            'necesidadesAsignadas',
            'reportesNoVistos',
            'recomendacionesCursos',
            'aptitudNecesidades'
        ));
    }


    public function marcarReporteVisto($voluntarioId, $reporteId, $tipo)
    {
        if (!in_array($tipo, ['fisico', 'emocional'])) {
            abort(404);
        }

        $reportesVistos = session()->get('reportes_vistos', []);
        $key = $reporteId . '_' . $tipo;
        
        if (!in_array($key, $reportesVistos)) {
            $reportesVistos[] = $key;
            session()->put('reportes_vistos', $reportesVistos);
        }

        return redirect()
            ->route('voluntarios.show', $voluntarioId)
            ->with('success', 'Reporte marcado como visto');
    }





    public function asignarCapacitacion(Request $request, $idUsuario)
    {
        $request->validate([
            'capacitacion_id' => 'required|exists:capacitacion,id',
        ]);

        $etapas = DB::table('etapa')
            ->join('curso', 'curso.id', '=', 'etapa.id_curso')
            ->where('curso.id_capacitacion', $request->capacitacion_id)
            ->select('etapa.id')
            ->get();

        if ($etapas->isEmpty()) {
            return redirect()
                ->back()
                ->withErrors('La capacitación seleccionada no tiene etapas configuradas, no se puede asignar.');
        }

        DB::transaction(function () use ($idUsuario, $etapas) {
            foreach ($etapas as $etapa) {
                ProgresoVoluntario::firstOrCreate(
                    [
                        'id_usuario' => $idUsuario,
                        'id_etapa'   => $etapa->id,
                    ],
                    [
                        'estado'            => 'en_progreso',
                        'fecha_inicio'      => now(),
                        'fecha_finalizacion'=> null,
                    ]
                );
            }
        });

        return redirect()
            ->route('voluntarios.show', $idUsuario)
            ->with('success', 'Capacitación asignada al voluntario correctamente.');
    }

    /**
     * ✅ NUEVO: Asignar necesidad a un voluntario
     */
    public function asignarNecesidad(Request $request, $idUsuario)
    {
        $request->validate([
            'necesidad_id' => 'required|exists:necesidad,id',
        ]);

        // 1. Obtener el historial clínico del voluntario
        $historial = DB::table('historial_clinico')
            ->where('id_usuario', $idUsuario)
            ->first();

        if (!$historial) {
            return redirect()
                ->back()
                ->withErrors('El voluntario no tiene historial clínico configurado.');
        }

        // 2. Crear un nuevo reporte para registrar la necesidad
        $reporteId = DB::table('reporte')->insertGetId([
            'id_historial'     => $historial->id,
            'estado_general'   => 'Necesidad asignada',
            'observaciones'    => 'Necesidad asignada manualmente desde el perfil del voluntario.',
            'fecha_generado'   => now(),
        ]);

        // 3. Asociar la necesidad al reporte
        DB::table('reporte_necesidad')->insert([
            'id_reporte'    => $reporteId,
            'id_necesidad'  => $request->necesidad_id,
            'created_at'    => now(),
        ]);

        return redirect()
            ->route('voluntarios.show', $idUsuario)
            ->with('success', 'Necesidad asignada correctamente.');
    }

    /**
     * ✅ NUEVO: Asignar curso a un voluntario
     */
    public function asignarCurso(Request $request, $idUsuario)
    {
        $request->validate([
            'curso_id' => 'required|exists:curso,id',
        ]);

        // Obtener las etapas del curso seleccionado
        $etapas = DB::table('etapa')
            ->where('id_curso', $request->curso_id)
            ->select('id', 'nombre', 'orden')
            ->orderBy('orden')
            ->get();

        if ($etapas->isEmpty()) {
            return redirect()
                ->back()
                ->withErrors('El curso seleccionado no tiene etapas configuradas. Por favor, configure etapas antes de asignar el curso.');
        }

        // Asignar las etapas del curso al voluntario
        DB::transaction(function () use ($idUsuario, $etapas) {
            foreach ($etapas as $etapa) {
                ProgresoVoluntario::firstOrCreate(
                    [
                        'id_usuario' => $idUsuario,
                        'id_etapa'   => $etapa->id,
                    ],
                    [
                        'estado'            => 'no_iniciado',
                        'fecha_inicio'      => null,
                        'fecha_finalizacion'=> null,
                    ]
                );
            }
        });

        // Obtener nombre del curso para el mensaje
        $curso = DB::table('curso')->where('id', $request->curso_id)->first();

        return redirect()
            ->route('voluntarios.show', $idUsuario)
            ->with('success', "Curso '{$curso->nombre}' asignado correctamente al voluntario.");
    }

    public function descargarHistorialPDF($id)
    {
        // 1. Obtener voluntario
        $voluntario = DB::table('usuario')
            ->join('rol', 'usuario.id_rol', '=', 'rol.id')
            ->where('usuario.id_usuario', $id)
            ->where('rol.nombre', 'Voluntario')
            ->select('usuario.*')
            ->first();

        if (!$voluntario) {
            abort(404, 'Voluntario no encontrado');
        }

        // 2. Obtener historial clínico
        $historial = DB::table('historial_clinico')
            ->where('id_usuario', $id)
            ->first();

        // 3. Obtener reportes (ordenados por fecha)
        $reportes = DB::select("
            SELECT DISTINCT r.*
            FROM reporte r
            LEFT JOIN reporte_progreso_voluntario rpv ON rpv.id_reporte = r.id
            LEFT JOIN progreso_voluntario pv ON pv.id = rpv.id_progreso
            LEFT JOIN historial_clinico hc ON hc.id = r.id_historial
            WHERE pv.id_usuario = ? OR hc.id_usuario = ?
            ORDER BY r.fecha_generado DESC
        ", [$id, $id]);

        // 4. Obtener capacitaciones con progreso
        $capacitaciones = DB::select("
            SELECT DISTINCT 
                c.id AS capacitacion_id,
                c.nombre AS capacitacion,
                cu.id AS curso_id,
                cu.nombre AS curso,
                e.id AS etapa_id,
                e.nombre AS etapa,
                e.orden AS etapa_orden,
                pv.estado,
                pv.fecha_inicio,
                pv.fecha_finalizacion
            FROM progreso_voluntario pv
            JOIN etapa e ON e.id = pv.id_etapa
            JOIN curso cu ON cu.id = e.id_curso
            JOIN capacitacion c ON c.id = cu.id_capacitacion
            WHERE pv.id_usuario = ?
            ORDER BY c.nombre, cu.nombre, e.orden
        ", [$id]);

        // 5. Obtener necesidades
        $necesidades = DB::table('reporte_necesidad')
            ->join('reporte', 'reporte.id', '=', 'reporte_necesidad.id_reporte')
            ->join('historial_clinico', 'historial_clinico.id', '=', 'reporte.id_historial')
            ->join('necesidad', 'necesidad.id', '=', 'reporte_necesidad.id_necesidad')
            ->where('historial_clinico.id_usuario', $id)
            ->select('necesidad.tipo', 'necesidad.descripcion', 'reporte.fecha_generado')
            ->orderBy('reporte.fecha_generado', 'desc')
            ->get();

        // 6. Generar PDF
        $pdf = PDF::loadView('voluntarios.historial-pdf', compact(
            'voluntario',
            'historial',
            'reportes',
            'capacitaciones',
            'necesidades'
        ));

        // Configurar PDF
        $pdf->setPaper('letter', 'portrait');

        // Descargar con nombre personalizado
        $nombreArchivo = 'Historial_' . str_replace(' ', '_', $voluntario->nombres . '_' . $voluntario->apellidos) . '.pdf';

        return $pdf->download($nombreArchivo);
    }




        /**
     * API: Obtener datos actualizados del voluntario para refresh automático
     */
    public function getDatosActualizados($id)
    {
        try {
            // 1. Obtener historial clínico
            $historial = DB::table('historial_clinico')
                ->where('id_usuario', $id)
                ->first();

            // 2. Reportes del voluntario
            $reportes = DB::select("
                SELECT DISTINCT r.*
                FROM reporte r
                LEFT JOIN reporte_progreso_voluntario rpv ON rpv.id_reporte = r.id
                LEFT JOIN progreso_voluntario pv ON pv.id = rpv.id_progreso
                LEFT JOIN historial_clinico hc ON hc.id = r.id_historial
                WHERE pv.id_usuario = ? OR hc.id_usuario = ?
                ORDER BY r.fecha_generado DESC
            ", [$id, $id]);

            // 3. Reporte más reciente CON evaluaciones
            $reporteMasReciente = null;
            foreach ($reportes as $reporte) {
                if ($reporte->resumen_fisico || $reporte->resumen_emocional) {
                    $reporteMasReciente = $reporte;
                    break;
                }
            }

            // 4. Evaluaciones del voluntario
            $evaluaciones = [];
            if ($historial) {
                $evaluaciones = DB::table('reporte')
                    ->join('evaluacion', 'evaluacion.id_reporte', '=', 'reporte.id')
                    ->join('test', 'evaluacion.id_test', '=', 'test.id')
                    ->where('reporte.id_historial', $historial->id)
                    ->select(
                        'reporte.id as reporte_id',
                        'reporte.resumen_fisico',
                        'reporte.resumen_emocional',
                        'reporte.estado_general',
                        'reporte.fecha_generado',
                        'evaluacion.id as evaluacion_id',
                        'evaluacion.fecha',
                        'test.nombre as test_nombre'
                    )
                    ->orderBy('reporte.fecha_generado', 'desc')
                    ->get();
            }

            if (empty($evaluaciones) || count($evaluaciones) == 0) {
                $reportesEvaluacion = DB::table('reporte')
                    ->join('historial_clinico', 'historial_clinico.id', '=', 'reporte.id_historial')
                    ->where('historial_clinico.id_usuario', $id)
                    ->select(
                        'reporte.id as reporte_id',
                        'reporte.resumen_fisico',
                        'reporte.resumen_emocional',
                        'reporte.estado_general',
                        'reporte.fecha_generado'
                    )
                    ->orderBy('reporte.fecha_generado', 'desc')
                    ->get();

                $evaluaciones = $reportesEvaluacion->map(function($reporte) {
                    return [
                        'reporte_id' => $reporte->reporte_id,
                        'resumen_fisico' => $reporte->resumen_fisico,
                        'resumen_emocional' => $reporte->resumen_emocional,
                        'estado_general' => $reporte->estado_general,
                        'fecha_generado' => $reporte->fecha_generado,
                        'fecha' => $reporte->fecha_generado,
                        'test_nombre' => 'Evaluación Física y Psicológica'
                    ];
                });
            }

            // 5. Necesidades asignadas
            $necesidadesAsignadas = DB::table('reporte_necesidad')
                ->join('reporte', 'reporte.id', '=', 'reporte_necesidad.id_reporte')
                ->join('historial_clinico', 'historial_clinico.id', '=', 'reporte.id_historial')
                ->join('necesidad', 'necesidad.id', '=', 'reporte_necesidad.id_necesidad')
                ->where('historial_clinico.id_usuario', $id)
                ->select('necesidad.*', 'reporte.fecha_generado')
                ->orderBy('reporte.fecha_generado', 'desc')
                ->get();

            // 6. Capacitaciones asignadas
            $capacitaciones = DB::select("
                SELECT DISTINCT c.*
                FROM progreso_voluntario pv
                JOIN etapa e ON e.id = pv.id_etapa
                JOIN curso cu ON cu.id = e.id_curso
                JOIN capacitacion c ON c.id = cu.id_capacitacion
                WHERE pv.id_usuario = ?
                ORDER BY c.nombre
            ", [$id]);

            // 7. Total de reportes para detectar nuevos
            $totalReportes = count($reportes);

            // 8. Recomendaciones de cursos más recientes (hasta 2)
            $recomendacionesCursos = DB::table('curso_recomendaciones')
                ->leftJoin('curso', 'curso_recomendaciones.id_curso', '=', 'curso.id')
                ->leftJoin('capacitacion', 'curso.id_capacitacion', '=', 'capacitacion.id')
                ->where('curso_recomendaciones.id_voluntario', $id)
                ->select(
                    'curso_recomendaciones.*',
                    'curso.nombre as curso_nombre',
                    'curso.descripcion as curso_descripcion',
                    'capacitacion.nombre as capacitacion_nombre'
                )
                ->orderBy('curso_recomendaciones.updated_at', 'desc')
                ->limit(2)
                ->get();

            // 9. Aptitud para asignar necesidades (evaluada por IA)
            $aptitudNecesidades = \App\Models\AptitudNecesidad::where('id_voluntario', $id)
                ->where('estado', 'activo')
                ->orderBy('updated_at', 'desc')
                ->first();

            // 10. Reportes no vistos (para tag "Nueva")
            $reportesVistos = session()->get('reportes_vistos', []);
            $reportesNoVistos = [];
            foreach ($reportes as $reporte) {
                $reportesNoVistos[] = [
                    'reporte_id' => $reporte->id,
                    'fisico_no_visto' => !in_array($reporte->id . '_fisico', $reportesVistos) && $reporte->resumen_fisico ? 'fisico' : null,
                    'emocional_no_visto' => !in_array($reporte->id . '_emocional', $reportesVistos) && $reporte->resumen_emocional ? 'emocional' : null,
                ];
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'reporteMasReciente' => $reporteMasReciente,
                    'reportes' => $reportes,
                    'evaluaciones' => $evaluaciones,
                    'necesidadesAsignadas' => $necesidadesAsignadas,
                    'capacitaciones' => $capacitaciones,
                    'totalReportes' => $totalReportes,
                    'recomendacionesCursos' => $recomendacionesCursos,
                    'aptitudNecesidades' => $aptitudNecesidades,
                    'reportesNoVistos' => $reportesNoVistos
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

}



