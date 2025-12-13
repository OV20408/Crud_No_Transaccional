<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RegistroSimpleController extends Controller
{
    /**
     * GET /api/registro/ci/{ci}
     * Devuelve datos básicos de un usuario por CI para autocompletar registros
     * usado por el API Gateway.
     */
    public function showByCi(Request $request, string $ci)
    {
        $clientSystem = $request->header('X-Client-System', 'unknown');

        Log::info('RegistroSimple lookup recibido', [
            'ci'            => $ci,
            'client_system' => $clientSystem,
            'ip'            => $request->ip(),
        ]);

        // Ajusta 'ci' al nombre real de la columna en tu tabla usuario
        $user = User::where('ci', $ci)->first();

        if (!$user) {
            return response()->json([
                'success' => true,
                'system'  => 'SEGUIMIENTO_DE_VOLUNTARIOS Y', 
                'ci'      => $ci,
                'found'   => false,
                'data'    => null,
            ], 200);
        }

        return response()->json([
            'success' => true,
            'system'  => 'SEGUIMIENTO_DE_VOLUNTARIOS', // ej: 'logistica'
            'ci'      => $ci,
            'found'   => true,
            'data'    => [
                // ADAPTA ESTOS CAMPOS según tu modelo User
                'ci'        => $user->ci,
                'nombre'    => $user->nombres ?? $user->nombre ?? null,
                'apellido'  => $user->apellidos ?? $user->apellido ?? null,
                'telefono'  => $user->telefono ?? null,
                'email'     => $user->email ?? null,
                'id'        => $user->id_usuario ?? $user->id ?? null,
            ],
        ], 200);
    }
}





