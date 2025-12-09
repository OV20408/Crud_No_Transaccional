<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class RemoteIncendiosUsuariosService
{
    public function buscarPorCi($ci)
    {
        $response = Http::withToken(env('INCENDIOS_TOKEN'))
            ->get(env('INCENDIOS_URL') . '/sync/usuarios/ci/' . $ci);

        return $response->json();
    }

    public function actualizarEstado($id, $estado)
    {
        $response = Http::withToken(env('INCENDIOS_TOKEN'))
            ->put(env('INCENDIOS_URL') . '/sync/usuarios/' . $id . '/estado', [
                'estado' => $estado
            ]);

        return $response->json();
    }
}

