<?php

namespace App\Http\Controllers\Api\Sync;

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
                'error' => 'Debe enviar el parámetro nombre'
            ], 400);
        }

        $curso = Curso::where('nombre', $nombre)->first();

        return response()->json([
            'exists' => $curso ? true : false,
            'data'   => $curso
        ]);
    }
}
