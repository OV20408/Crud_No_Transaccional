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


class VoluntarioController extends Controller
{
    public function index(Request $request)
    {
        // Obtener solo usuarios con rol de "Voluntario"
        $query = DB::table('usuario')
            ->join('rol', 'usuario.id_rol', '=', 'rol.id')
            ->where('rol.nombre', 'Voluntario')
            ->select('usuario.*');

        // Filtro por nombre
        if ($request->filled('q')) {
            $query->where(function ($q) use ($request) {
                $q->where('usuario.nombres', 'ILIKE', '%' . $request->q . '%')
                  ->orWhere('usuario.apellidos', 'ILIKE', '%' . $request->q . '%');
            });
        }

        // Filtro por CI
        if ($request->filled('ci')) {
            $query->where('usuario.ci', 'LIKE', '%' . $request->ci . '%');
        }

        // Filtro por tipo de sangre
        if ($request->filled('tipo_sangre')) {
            $query->where('usuario.tipo_sangre', $request->tipo_sangre);
        }

        // Filtro por estado (disponibilidad)
        if ($request->filled('estado')) {
            $query->where('usuario.estado', 'ILIKE', $request->estado);
        }

        $voluntarios = $query->get();

        return view('voluntarios.index', compact('voluntarios'));
    }

    /**
     * Mostrar formulario multi-step para crear voluntario.
     */
    public function create()
    {
        return view('voluntarios.create');
    }

    /**
     * Guardar voluntario y enviar correo de “configura tu contraseña”.
     */
    public function store(Request $request)
    {
        // Validación básica (puedes afinarla después)
        $validated = $request->validate([
            'nombres'             => 'required|string|max:255',
            'apellidos'           => 'required|string|max:255',
            'ci'                  => 'required|string|max:255|unique:usuario,ci',
            'fecha_nacimiento'    => 'nullable|date',
            'genero'              => 'nullable|string|max:50',
            'telefono'            => 'nullable|string|max:255',
            'email'               => 'required|email|max:255|unique:usuario,email',
            'direccion_domicilio' => 'nullable|string|max:255',
            'estado'              => 'nullable|string|max:50',
            'nivel_entrenamiento' => 'nullable|string|max:255',
            'entidad_pertenencia' => 'nullable|string|max:255',
            'tipo_sangre'         => 'nullable|string|max:10',
            
        ]);

        // Obtener ID del rol "Voluntario"
        $rolVoluntarioId = Rol::where('nombre', 'Voluntario')->value('id');

        if (!$rolVoluntarioId) {
            abort(500, 'Rol "Voluntario" no está configurado en la tabla rol.');
        }

        // Contraseña temporal aleatoria (solo para cumplir NOT NULL).
        // El usuario la cambiará vía link de reset.
        $passwordTemporal = Str::random(12);

        // Crear usuario usando el modelo (mapea password -> contrasena)
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

            // IMPORTANTE: este campo "password" dispara tu setPasswordAttribute
            'password'            => $passwordTemporal,
        ]);

        // (Opcional) Crear historial clínico vacío al vuelo
        DB::table('historial_clinico')->insert([
            'id_usuario'         => $user->id_usuario,
            'fecha_inicio'       => now(),
            'fecha_actualizacion'=> now(),
        ]);

        // Enviar link de reset de contraseña al correo del voluntario
        // (mismo flujo que ya usas para admin)
        if (!empty($user->email)) {
            Password::sendResetLink(['email' => $user->email]);
        }

        return redirect()
            ->route('voluntarios.show', $user->id_usuario)
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

        // 3. Reportes del voluntario (incluye los de progreso Y los del historial clínico/IA)
        $reportes = DB::select("
            SELECT DISTINCT r.*
            FROM reporte r
            LEFT JOIN reporte_progreso_voluntario rpv ON rpv.id_reporte = r.id
            LEFT JOIN progreso_voluntario pv ON pv.id = rpv.id_progreso
            LEFT JOIN historial_clinico hc ON hc.id = r.id_historial
            WHERE pv.id_usuario = ? OR hc.id_usuario = ?
            ORDER BY r.fecha_generado DESC
        ", [$id, $id]);

        // 4. Reporte más reciente
        $reporteMasReciente = $reportes[0] ?? null;

        // 5. Capacitaciones del último reporte (a través de progreso_voluntario)
        $capacitaciones = [];
        if ($reporteMasReciente) {
            $capacitaciones = DB::select("
                SELECT DISTINCT c.*
                FROM reporte_progreso_voluntario rpv
                JOIN progreso_voluntario pv ON pv.id = rpv.id_progreso
                JOIN etapa e ON e.id = pv.id_etapa
                JOIN curso cu ON cu.id = e.id_curso
                JOIN capacitacion c ON c.id = cu.id_capacitacion
                WHERE rpv.id_reporte = ?
            ", [$reporteMasReciente->id]);
        }

        // 6. Necesidades del último reporte
        $necesidades = [];
        if ($reporteMasReciente) {
            $necesidades = DB::table('reporte_necesidad')
                ->join('necesidad', 'reporte_necesidad.id_necesidad', '=', 'necesidad.id')
                ->where('reporte_necesidad.id_reporte', $reporteMasReciente->id)
                ->select('necesidad.*')
                ->get();
        }

        // 7. Cursos del voluntario
        $cursos = DB::select("
            SELECT DISTINCT 
                cu.id,
                cu.nombre,
                cu.descripcion,
                cap.nombre AS capacitacion_nombre
            FROM progreso_voluntario pv
            JOIN etapa e ON e.id = pv.id_etapa
            JOIN curso cu ON cu.id = e.id_curso
            JOIN capacitacion cap ON cap.id = cu.id_capacitacion
            WHERE pv.id_usuario = ?
            ORDER BY cu.nombre
        ", [$id]);

        // 8. Evaluaciones del voluntario (a través de historial_clinico -> reporte -> evaluacion)
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
        
        // Si no hay evaluaciones por historial, intentar obtener los reportes directamente
        if (empty($evaluaciones) || count($evaluaciones) == 0) {
            // Obtener reportes que tengan id_historial del usuario
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
                
            // Convertir reportes a formato de evaluaciones para la vista
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

        // 9. Todas las capacitaciones del sistema (para el combo de asignar)
        $capacitacionesAll = Capacitacion::orderBy('nombre')->get();

                return view('voluntarios.show', compact(
            'voluntario',
            'historial',
            'reportes',
            'reporteMasReciente',
            'necesidades',
            'cursos',
            'evaluaciones',
            'capacitacionesProgreso',
            'capacitacionesAll'
        ));

    }




        public function asignarCapacitacion(Request $request, $idUsuario)
    {
        $request->validate([
            'capacitacion_id' => 'required|exists:capacitacion,id',
        ]);

        // Buscar todas las etapas de los cursos de esa capacitación
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

}
