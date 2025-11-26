<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Voluntario; // Cambia al modelo correcto

class VoluntarioApiController extends Controller
{
    public function index()
    {
        return response()->json(Voluntario::all());
    }

    public function show($id)
    {
        $voluntario = Voluntario::find($id);

        if (!$voluntario) {
            return response()->json(['message' => 'Voluntario no encontrado'], 404);
        }

        return response()->json($voluntario);
    }
}


