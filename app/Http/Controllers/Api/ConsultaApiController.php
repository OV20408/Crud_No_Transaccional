<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Consulta;

class ConsultaApiController extends Controller
{

    public function index()
    {
        return response()->json([
            'success' => true,
            'data' => Consulta::with(['voluntario', 'necesidad'])->get()
        ]);
    }

    public function store(Request $request)
{
    $request->validate([
        'voluntario_id' => 'required|integer',
        'mensaje' => 'required|string|max:500',
    ]);

    $consulta = \DB::table('consultas')->insert([
        'voluntario_id' => $request->voluntario_id,
        'necesidad_id' => 1, // valor por defecto
        'mensaje' => $request->mensaje,
        'estado' => 'pendiente',
        'created_at' => now(),
        'updated_at' => now(),

    ]);

    return response()->json(['success' => true, 'message' => 'Consulta registrada']);
}

}


