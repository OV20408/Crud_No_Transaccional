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
            'data' => \App\Models\Consulta::with(['voluntario', 'necesidad'])->get()
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'voluntario_id' => 'required|integer',
            'mensaje' => 'required|string|max:500',
        ]);

        $consulta = Consulta::create($data);

        return response()->json([
            'success' => true,
            'data' => $consulta
        ], 201);
    }
}


