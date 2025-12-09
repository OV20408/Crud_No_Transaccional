<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UsuarioSyncController extends Controller
{
    // GET /api/sync/usuarios/ci/{ci}
    public function buscarPorCi($ci)
    {
        $user = User::where('ci', $ci)->first();

        return response()->json([
            'exists' => $user ? true : false,
            'data'   => $user
        ]);
    }

    // PUT /api/sync/usuarios/{id}/estado
    public function actualizarEstado(Request $request, $id)
    {
        $request->validate([
            'estado' => 'required'
        ]);

        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'error' => 'Usuario no encontrado'
            ], 404);
        }

        $user->estado = $request->estado;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Estado actualizado',
            'data'    => $user
        ]);
    }
}
