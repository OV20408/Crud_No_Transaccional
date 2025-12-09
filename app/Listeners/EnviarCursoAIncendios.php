<?php

namespace App\Listeners;

use App\Events\CursoCreadoOActualizado;
use App\Services\RemoteIncendiosCursosService;

class EnviarCursoAIncendios
{
    public function handle(CursoCreadoOActualizado $event)
    {
        $curso = $event->curso;

        // SOLO ENVÍA SI NO existe en el otro sistema
        $exists = app(RemoteIncendiosCursosService::class)
                    ->cursoExiste($curso->nombre);

        if (!$exists['exists']) {
            // Aquí podrías implementar un endpoint POST remoto si existiera
            // Por ahora solo evitamos duplicados
        }
    }
}


