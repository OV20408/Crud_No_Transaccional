<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Rol;
use App\Models\Reporte;
use App\Models\Evaluacion;
use App\Models\Universidad;
use App\Models\Necesidad;
use App\Models\Capacitacion;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        // 1) ID del rol "Voluntario"
        $idRolVoluntario = Rol::where('nombre', 'Voluntario')->value('id');

        $voluntariosBase = User::query();
        if ($idRolVoluntario) {
            $voluntariosBase->where('id_rol', $idRolVoluntario);
        }

        // 2) Tarjetas de arriba
        $voluntariosActivos   = (clone $voluntariosBase)->where('estado', 'activo')->count();
        $voluntariosInactivos = (clone $voluntariosBase)->where('estado', 'inactivo')->count();

        // Ajusta estos según tus columnas reales
        $alertasRecientes = Reporte::count();         // o where('created_at','>',now()->subDays(7))
        $evaluacionesCompletadas = Evaluacion::count();

        // 3) Listas de “últimos …”
        $ultimosVoluntarios = (clone $voluntariosBase)
            ->orderByDesc('created_at')
            ->take(3)
            ->get();

        $ultimosReportes = Reporte::query()
            ->orderByDesc('fecha_generado')
            ->take(3)
            ->get();



        // 4) Datos tipo “chart” (simple: nombre + cantidad)
        // 🔸 Voluntarios por universidad
        $universidadesData = Universidad::select(
        'universidad.nombre as label',
            DB::raw('COUNT(usuario.id_usuario) as total')
        )
        // unimos por el NOMBRE, no por id_universidad
        ->leftJoin('usuario', 'usuario.entidad_pertenencia', '=', 'universidad.nombre')
        ->groupBy('universidad.nombre')
        ->get();

        // 🔸 Necesidades (aquí sólo contamos necesidades, ajusta si tienes tabla puente)
        $necesidadesData = Necesidad::select(
                DB::raw('COALESCE(necesidad.tipo, necesidad.descripcion) as label'),
                DB::raw('COUNT(necesidad.id) as total')
            )
            ->groupBy('label')
            ->get();

        // 🔸 Capacitaciones (ejemplo simple: cantidad de cursos por capacitación)
        $capacitacionesData = Capacitacion::select(
                'capacitacion.nombre as label',
                DB::raw('COUNT(curso.id) as total')
            )
            ->leftJoin('curso', 'curso.id_capacitacion', '=', 'capacitacion.id')
            ->groupBy('capacitacion.nombre')
            ->get();

        return view('home', [
            'voluntariosActivos'       => $voluntariosActivos,
            'voluntariosInactivos'     => $voluntariosInactivos,
            'alertasRecientes'         => $alertasRecientes,
            'evaluacionesCompletadas'  => $evaluacionesCompletadas,
            'ultimosVoluntarios'       => $ultimosVoluntarios,
            'ultimosReportes'          => $ultimosReportes,
            'universidadesData'        => $universidadesData,
            'necesidadesData'          => $necesidadesData,
            'capacitacionesData'       => $capacitacionesData,
        ]);
    }
}
