<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Consulta;
use App\Events\ConsultaCreada;

class ConsultaApiController extends Controller
{
    public function index(Request $request)
    {
        $query = Consulta::with('voluntario')
            ->orderBy('created_at', 'asc');

        if ($request->filled('voluntario_id')) {
            $query->where('voluntario_id', $request->voluntario_id);
        }

        return response()->json([
            'success' => true,
            'data'    => $query->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'voluntario_id' => 'required|integer',
            'mensaje'       => 'required|string|max:500',
        ]);

        $consulta = Consulta::create([
            'voluntario_id' => $validated['voluntario_id'],
            'necesidad_id'  => 1,
            'mensaje'       => $validated['mensaje'],
            'estado'        => 'pendiente',
            'ci_voluntario_accion' => \App\Models\User::where('id_usuario', $validated['voluntario_id'])->value('ci'), // Trazabilidad API Gateway
        ]);

        // Cargamos el voluntario para tener sus datos en el evento
        $consulta->load('voluntario');

        // Disparar el evento para notificar en tiempo real
        ConsultaCreada::dispatch($consulta);

        return response()->json([
            'success' => true,
            'message' => 'Consulta registrada',
            'data'    => $consulta,
        ], 201);
    }
}