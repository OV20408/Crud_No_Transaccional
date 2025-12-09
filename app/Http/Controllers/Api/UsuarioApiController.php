<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UsuarioApiController extends Controller
{
    public function index()
    {
        return User::all();
    }

    public function show($id)
    {
        $u = User::find($id);

        if (!$u) return response()->json(['message' => 'Usuario no encontrado'], 404);

        return $this->normalize($u);
    }

    public function getByCi($ci)
    {
        $u = User::where('ci', $ci)->first();

        if (!$u) return response()->json(['message' => 'Usuario no encontrado'], 404);

        return $this->normalize($u);
    }

    private function normalize($u)
    {
        return [
            'id' => $u->id_usuario,
            'ci' => $u->ci,
            'nombre' => $u->nombres,
            'apellido' => $u->apellidos,
            'telefono' => $u->telefono,
            'tipo_sangre' => $u->tipo_sangre,
            'rol_id' => $u->id_rol,
            'fotoPerfil' => $u->foto_ci,
        ];
    }

    public function updateEstado(Request $request, $id)
{
    $request->validate([
        'estado' => 'required|string'
    ]);

    $user = User::find($id);

    if (!$user) {
        return response()->json([
            'success' => false,
            'message' => 'Usuario no encontrado'
        ], 404);
    }

    // Actualizar estado
    $user->estado = $request->estado;
    $user->save();

    // 🔥 Disparar evento de sincronización automática
    \App\Events\UsuarioEstadoActualizado::dispatch($user);

    return response()->json([
        'success' => true,
        'message' => 'Estado actualizado correctamente',
        'data' => $user
    ]);
}

}
