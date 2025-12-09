<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Curso;
use Illuminate\Http\Request;

class CursoSyncController extends Controller
{
    public function search(Request $request)
    {
        $nombre = $request->query('nombre');

        if (!$nombre) {
            return response()->json([
                'exists' => false,
                'message' => 'Debe enviar el parámetro nombre'
            ], 400);
        }

        $curso = Curso::where('nombre', $nombre)->first();

        // 👉 Si NO existe
        if (!$curso) {
            return response()->json([
                'exists' => false,
                'data' => null
            ], 200);
        }

        // 👉 Si existe (aunque sea simple)
        return response()->json([
            'exists' => true,
            'data' => [
                'id' => $curso->id,
                'nombre' => $curso->nombre,
                'descripcion' => $curso->descripcion,
                'capacitacion' => null, // Estructura estándar
                'etapas' => []          // Estructura estándar
            ]
        ], 200);
    }



    public function syncStore(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string',
            'descripcion' => 'nullable|string',
            'id_capacitacion' => 'nullable|integer'
        ]);

        // Buscar si existe
        $curso = \App\Models\Curso::where('nombre', $request->nombre)->first();

        if ($curso) {
            // Actualizar si existe
            $curso->update([
                'descripcion' => $request->descripcion ?? $curso->descripcion,
                'id_capacitacion' => $request->id_capacitacion ?? $curso->id_capacitacion,
            ]);
        } else {
            // Crear si no existe
            $curso = \App\Models\Curso::create([
                'nombre' => $request->nombre,
                'descripcion' => $request->descripcion,
                'id_capacitacion' => $request->id_capacitacion,
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => $curso
        ]);
    }




}
