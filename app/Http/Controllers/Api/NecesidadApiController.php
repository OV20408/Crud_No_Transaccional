<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Necesidad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NecesidadApiController extends Controller
{
    /**
     * Listar todas las necesidades
     */
    public function index()
    {
        $necesidades = Necesidad::all();
        
        return response()->json([
            'success' => true,
            'data' => $necesidades
        ]);
    }

    /**
     * Obtener necesidades de un voluntario específico
     */
    public function getByVoluntario($idVoluntario)
    {
        $necesidades = DB::table('necesidad')
            ->join('reporte_necesidad', 'necesidad.id', '=', 'reporte_necesidad.id_necesidad')
            ->join('reporte', 'reporte.id', '=', 'reporte_necesidad.id_reporte')
            ->join('historial_clinico', 'historial_clinico.id', '=', 'reporte.id_historial')
            ->where('historial_clinico.id_usuario', $idVoluntario)
            ->select('necesidad.*', 'reporte.fecha_generado')
            ->distinct()
            ->get();
        
        return response()->json([
            'success' => true,
            'data' => $necesidades
        ]);
    }

    /**
     * Crear una nueva necesidad
     */
    public function store(Request $request)
    {
        $request->validate([
            'descripcion' => 'required|string|max:255',
            'tipo' => 'nullable|string|max:100'
        ]);

        $necesidad = Necesidad::create([
            'descripcion' => $request->descripcion,
            'tipo' => $request->tipo
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Necesidad creada correctamente',
            'data' => $necesidad
        ], 201);
    }

    /**
     * Mostrar una necesidad específica
     */
    public function show($id)
    {
        $necesidad = Necesidad::find($id);
        
        if (!$necesidad) {
            return response()->json([
                'success' => false,
                'message' => 'Necesidad no encontrada'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $necesidad
        ]);
    }

    /**
     * Actualizar una necesidad
     */
    public function update(Request $request, $id)
    {
        $necesidad = Necesidad::find($id);
        
        if (!$necesidad) {
            return response()->json([
                'success' => false,
                'message' => 'Necesidad no encontrada'
            ], 404);
        }

        $request->validate([
            'descripcion' => 'sometimes|string|max:255',
            'tipo' => 'nullable|string|max:100'
        ]);

        $necesidad->update($request->only(['descripcion', 'tipo']));

        return response()->json([
            'success' => true,
            'message' => 'Necesidad actualizada correctamente',
            'data' => $necesidad
        ]);
    }

    /**
     * Eliminar una necesidad
     */
    public function destroy($id)
    {
        $necesidad = Necesidad::find($id);
        
        if (!$necesidad) {
            return response()->json([
                'success' => false,
                'message' => 'Necesidad no encontrada'
            ], 404);
        }

        $necesidad->delete();

        return response()->json([
            'success' => true,
            'message' => 'Necesidad eliminada correctamente'
        ]);
    }
}
