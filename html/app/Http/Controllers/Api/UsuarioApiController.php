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
}
