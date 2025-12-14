<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SolicitudAyuda;
use App\Models\ChatMensaje;
use App\Models\User;
use Illuminate\Http\Request;

class SolicitudAyudaApiController extends Controller
{
    // GET /api/solicitudes-ayuda
    public function index(Request $request)
    {
        $query = SolicitudAyuda::with(['voluntario']);

        if ($request->filled('voluntario_id')) {
            $query->where('voluntario_id', '!=', $request->voluntario_id);
        }

        if ($request->filled('tipo')) {
            $query->where('tipo', $request->tipo);
        }

        if ($request->filled('nivel_emergencia')) {
            $query->where('nivel_emergencia', $request->nivel_emergencia);
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->filled('q')) { 
            $query->where('descripcion', 'ILIKE', '%'.$request->q.'%');
        }

        $solicitudes = $query->orderBy('created_at', 'desc')->get();

        return response()->json($solicitudes->map(function ($s) {
            return [
                'id'             => $s->id,
                'voluntarioId'   => $s->voluntario_id,
                'voluntario'     => trim(($s->voluntario->nombres ?? '').' '.($s->voluntario->apellidos ?? '')),
                'tipoEmergencia' => $s->tipo,
                'nivelEmergencia'=> $s->nivel_emergencia,
                'estado'         => $s->estado,
                'descripcion'    => $s->descripcion,
                'latitud'        => (float) $s->latitud,
                'longitud'       => (float) $s->longitud,
                // ❌ QUITAR: 'direccion' => $s->direccion,
                'fecha'          => $s->created_at?->toIso8601String(),
            ];
        }));
    }

    // POST /api/solicitudes-ayuda
    public function store(Request $request)
    {
        $validated = $request->validate([
            'tipo_emergencia'   => 'required|string|max:100',
            'descripcion'       => 'required|string|max:500',
            'nivel_emergencia'  => 'required|in:BAJO,MEDIO,ALTO,baja,media,alta',
            'voluntario_id'     => 'required|integer|exists:usuario,id_usuario',
            'latitud'           => 'required|numeric',
            'longitud'          => 'required|numeric',
            // ❌ QUITAR: 'direccion' => 'nullable|string|max:500',
        ]);

        // ✅ Normalizar nivel a mayúsculas
        $nivelNormalizado = strtoupper($validated['nivel_emergencia']);
        
        $mapeoNivel = [
            'BAJA'  => 'BAJO',
            'MEDIA' => 'MEDIO',
            'ALTA'  => 'ALTO',
        ];
        
        $nivelFinal = $mapeoNivel[$nivelNormalizado] ?? $nivelNormalizado;

        $dataToCreate = [
            'voluntario_id'     => $validated['voluntario_id'],
            'tipo'              => $validated['tipo_emergencia'],
            'nivel_emergencia'  => $nivelFinal,
            'descripcion'       => $validated['descripcion'],
            'latitud'           => $validated['latitud'],
            'longitud'          => $validated['longitud'],
            // ❌ QUITAR: 'direccion' => $validated['direccion'] ?? null,
            'estado'            => 'sin responder',
            'ci_voluntario_accion' => \App\Models\User::where('id_usuario', $validated['voluntario_id'])->value('ci'),
        ];

        $solicitud = SolicitudAyuda::create($dataToCreate);

        ChatMensaje::create([
            'voluntario_id' => $validated['voluntario_id'],
            'de' => 'voluntario',
            'texto' => "🚨 [EMERGENCIA #{$solicitud->id}] {$validated['descripcion']} - Nivel: {$nivelFinal}",
            'ci_voluntario_accion' => \App\Models\User::where('id_usuario', $validated['voluntario_id'])->value('ci'),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Solicitud de ayuda creada exitosamente',
            'data' => [
                'id'               => $solicitud->id,
                'tipo'             => $solicitud->tipo,
                'descripcion'      => $solicitud->descripcion,
                'nivel_emergencia' => $solicitud->nivel_emergencia,
                'voluntario_id'    => $solicitud->voluntario_id,
                'estado'           => $solicitud->estado,
                'latitud'          => $solicitud->latitud,
                'longitud'         => $solicitud->longitud,
                // ❌ QUITAR: 'direccion' => $solicitud->direccion,
            ],
        ], 201);
    }

    // PATCH /api/solicitudes-ayuda/{id}/estado
    public function actualizarEstado(Request $request, $id)
    {
        $request->validate([
            'estado' => 'required|string|max:30',
            // ❌ QUITAR: 'resolucion' => 'nullable|string|max:500',
        ]);

        $solicitud = SolicitudAyuda::findOrFail($id);
        $solicitud->estado = $request->estado;
        
        // ❌ QUITAR todo el bloque de resolución
        
        $solicitud->save();

        return response()->json($solicitud);
    }


    public function marcarResuelta($id)
    {
        $solicitud = \App\Models\SolicitudAyuda::findOrFail($id);
        
        $solicitud->update([
            'estado' => 'resuelto',
            'fecha_respondida' => now(),
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Solicitud marcada como resuelta',
            'data' => $solicitud,
        ]);
    }
}