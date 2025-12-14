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
        $idRolVoluntario = Rol::where('nombre', 'Voluntario')->value('id');

        $voluntariosBase = User::query();
        if ($idRolVoluntario) {
            $voluntariosBase->where('id_rol', $idRolVoluntario);
        }

        //Tarjetas de arriba
        $voluntariosActivos   = (clone $voluntariosBase)->where('estado', 'activo')->count();
        $voluntariosInactivos = (clone $voluntariosBase)->where('estado', 'inactivo')->count();

        $alertasRecientes = Reporte::count();        
        $evaluacionesCompletadas = Evaluacion::count();

        $ultimosVoluntarios = (clone $voluntariosBase)
            ->orderByDesc('created_at')
            ->take(3)
            ->get();

        $ultimosReportes = Reporte::query()
            ->orderByDesc('fecha_generado')
            ->take(3)
            ->get();



        // Datos chart
        //Voluntarios por universidad (basado en evaluaciones asignadas)
        $universidadesData = Universidad::select(
            'universidad.nombre as label',
            DB::raw('COUNT(evaluacion.id) as total')
        )
        ->leftJoin('evaluacion', 'evaluacion.id_universidad', '=', 'universidad.id')
        ->groupBy('universidad.nombre')
        ->get();

        //Necesidades
        $necesidadesData = Necesidad::select(
                DB::raw('COALESCE(necesidad.tipo, necesidad.descripcion) as label'),
                DB::raw('COUNT(necesidad.id) as total')
            )
            ->groupBy('label')
            ->get();

        // Capacitaciones 
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
