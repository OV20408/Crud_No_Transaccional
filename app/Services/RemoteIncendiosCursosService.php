<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class RemoteIncendiosCursosService
{
    public function cursoExiste($nombre)
    {
        $response = Http::withToken(env('INCENDIOS_TOKEN'))
            ->get(env('INCENDIOS_URL') . '/sync/cursos/search', [
                'nombre' => $nombre
            ]);

        return $response->json();
    }
}

