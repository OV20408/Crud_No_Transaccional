<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
            $query->where('usuario.nombres', 'ILIKE', '%' . $request->q . '%')
                  ->orWhere('usuario.apellidos', 'ILIKE', '%' . $request->q . '%');
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

    // 3. Obtener reportes del voluntario
    // Relación REAL según tu BD:
    // usuario → progreso_voluntario → reporte_progreso_voluntario → reporte
    $reportes = DB::select("
        SELECT DISTINCT r.*
        FROM reporte r
        JOIN reporte_progreso_voluntario rpv ON rpv.id_reporte = r.id
        JOIN progreso_voluntario pv ON pv.id = rpv.id_progreso
        WHERE pv.id_usuario = ?
        ORDER BY r.fecha_generado DESC
    ", [$id]);

    // 4. Obtener reporte más reciente (si existe)
    $reporteMasReciente = $reportes[0] ?? null;

    // 5. Obtener capacitaciones asociadas al último reporte
    $capacitaciones = [];
    if ($reporteMasReciente) {
        $capacitaciones = DB::select("
            SELECT DISTINCT c.*
            FROM vw_reporte_capacitacion vrc
            JOIN capacitacion c ON c.id = vrc.id_capacitacion
            WHERE vrc.id_reporte = ?
        ", [$reporteMasReciente->id]);
    }

    // 6. Obtener necesidades asociadas al último reporte
    $necesidades = [];
    if ($reporteMasReciente) {
        $necesidades = DB::table('reporte_necesidad')
            ->join('necesidad', 'reporte_necesidad.id_necesidad', '=', 'necesidad.id')
            ->where('reporte_necesidad.id_reporte', $reporteMasReciente->id)
            ->select('necesidad.*')
            ->get();
    }

    // 7. Obtener cursos del voluntario (según progresos)
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

    // 8. Obtener evaluaciones del reporte más reciente
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