<?php

namespace App\Listeners;

use App\Events\UsuarioEstadoActualizado;
use App\Services\RemoteIncendiosUsuariosService;

class EnviarEstadoUsuarioAIncendios
{
    public function handle(UsuarioEstadoActualizado $event)
    {
        $user = $event->user;

        app(RemoteIncendiosUsuariosService::class)
            ->actualizarEstado($user->id, $user->estado);
    }
}


