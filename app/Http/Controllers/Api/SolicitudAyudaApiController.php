<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SolicitudAyuda;
use Illuminate\Http\Request;

class SolicitudAyudaApiController extends Controller
{
    // GET /api/solicitudes-ayuda
    public function index(Request $request)
    {
        $query = SolicitudAyuda::with(['voluntario']);

        // excluir solicitudes del mismo voluntario (comportamiento descrito para la app)
        if ($request->filled('voluntario_id')) {
            $query->where('voluntario_id', '!=', $request->voluntario_id);
        }

        // filtros opcionales
        // OJO: si en DB la columna es "tipo", filtra por "tipo"
        if ($request->filled('tipo')) {
            $query->where('tipo', $request->tipo);
        }

        if ($request->filled('nivel_emergencia')) {
            $query->where('nivel_emergencia', $request->nivel_emergencia);
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->filled('q')) { // descripción
            $query->where('descripcion', 'ILIKE', '%'.$request->q.'%');
        }

        $solicitudes = $query->orderBy('created_at', 'desc')->get();

        return response()->json($solicitudes->map(function ($s) {
            return [
                'id'             => $s->id,
                'voluntarioId'   => $s->voluntario_id,
                'voluntario'     => trim(($s->voluntario->nombres ?? '').' '.($s->voluntario->apellidos ?? '')),
                // devolvemos siempre tipoEmergencia, tomando primero tipo_emergencia si existe,
                // y si no, la columna "tipo" (que es la segura)
                'tipoEmergencia' => $s->tipo_emergencia ?? $s->tipo,
                'nivelEmergencia'=> $s->nivel_emergencia,
                'estado'         => $s->estado,
                'descripcion'    => $s->descripcion,
                'latitud'        => (float) $s->latitud,
                'longitud'       => (float) $s->longitud,
                'direccion'      => $s->direccion,
                'fecha'          => $s->created_at?->toIso8601String(),
            ];
        }));
    }

    // POST /api/solicitudes-ayuda
    public function store(Request $request)
    {
        $data = $request->validate([
            'voluntario_id'    => 'required|exists:usuario,id_usuario',
            'tipo_emergencia'  => 'required|string|max:50',
            'nivel_emergencia' => 'required|string|max:20',
            'descripcion'      => 'nullable|string',
            'latitud'          => 'required|numeric',
            'longitud'         => 'required|numeric',
            'direccion'        => 'nullable|string|max:255',
        ]);

        // 👇 tu tabla tiene columna "tipo" NOT NULL -> la rellenamos
        $data['tipo'] = $data['tipo_emergencia'];

        $solicitud = SolicitudAyuda::create($data);

        return response()->json($solicitud, 201);
    }

    public function actualizarEstado(Request $request, $id)
    {
        $request->validate([
            'estado' => 'required|string|max:30',
        ]);

        $solicitud = SolicitudAyuda::findOrFail($id);
        $solicitud->estado = $request->estado;
        $solicitud->save();

        return response()->json($solicitud);
    }
}
