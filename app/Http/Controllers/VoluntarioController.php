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

        // 3. Reportes del voluntario
        $reportes = DB::select("
            SELECT DISTINCT r.*
            FROM reporte r
            JOIN reporte_progreso_voluntario rpv ON rpv.id_reporte = r.id
            JOIN progreso_voluntario pv ON pv.id = rpv.id_progreso
            WHERE pv.id_usuario = ?
            ORDER BY r.fecha_generado DESC
        ", [$id]);

        // 4. Reporte más reciente
        $reporteMasReciente = $reportes[0] ?? null;

        // 5. Capacitaciones del último reporte
        $capacitaciones = [];
        if ($reporteMasReciente) {
            $capacitaciones = DB::select("
                SELECT DISTINCT c.*
                FROM vw_reporte_capacitacion vrc
                JOIN capacitacion c ON c.id = vrc.id_capacitacion
                WHERE vrc.id_reporte = ?
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

        // 8. Evaluaciones del último reporte
        $evaluaciones = [];
        if ($reporteMasReciente) {
            $evaluaciones = DB::table('evaluacion')
                ->join('test', 'evaluacion.id_test', '=', 'test.id')
                ->where('evaluacion.id_reporte', $reporteMasReciente->id)
                ->select('evaluacion.*', 'test.nombre as test_nombre')
                ->get();
        }

        return view('voluntarios.show', compact(
            'voluntario',
            'historial',
            'reportes',
            'reporteMasReciente',
            'capacitaciones',
            'necesidades',
            'cursos',
            'evaluaciones'
        ));
    }
}
