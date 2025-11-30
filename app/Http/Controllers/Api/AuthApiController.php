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
            'ci'         => 'required',
            'contrasena' => 'required',
        ]);

        // Buscar usuario
        $user = User::where('ci', $request->ci)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Usuario no encontrado'
            ], 404);
        }

        // Verificar contraseña
        if (!Hash::check($request->contrasena, $user->contrasena)) {
            return response()->json([
                'success' => false,
                'message' => 'Credenciales incorrectas'
            ], 401);
        }

        // SOLO VOLUNTARIOS PUEDEN ENTRAR A LA APP
        if ($user->rol?->nombre !== 'Voluntario') {
            return response()->json([
                'success' => false,
                'message' => 'Solo los voluntarios pueden usar la aplicación móvil.'
            ], 403);
        }

        // Verificar que esté activo
        if (strtolower($user->estado) !== 'activo') {
            return response()->json([
                'success' => false,
                'message' => 'Usuario inactivo'
            ], 403);
        }

        // 🔥 GENERAR TOKEN REAL DE SANCTUM
        // Primero revoca tokens antiguos (opcional)
        $user->tokens()->delete();
        
        // Crear nuevo token
        $token = $user->createToken('mobile-app')->plainTextToken;

        return response()->json([
            'success' => true,
            'access_token' => $token,  // 👈 Token real de Sanctum
            'user' => [
                'id' => $user->id_usuario,
                'ci' => $user->ci,
                'nombres' => $user->nombres,
                'apellidos' => $user->apellidos,
            ]
        ]);
    }
}