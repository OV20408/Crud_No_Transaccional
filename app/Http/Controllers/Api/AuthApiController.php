<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthApiController extends Controller
{
    public function login(Request $request)
{
    $request->validate([
        'ci' => 'required',
        'contrasena' => 'required',
    ]);

    $user = User::where('ci', $request->ci)->first();

    // 💥 Comparación directa porque tu BD usa texto plano
    if (!$user || $user->contrasena !== $request->contrasena) {
        return response()->json([
            'success' => false,
            'message' => 'Credenciales incorrectas'
        ], 401);
    }

    return response()->json([
        'success' => true,
        'access_token' => 'token-' . $user->id_usuario
    ]);
}

}
