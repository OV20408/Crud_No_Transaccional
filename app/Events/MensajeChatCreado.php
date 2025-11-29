<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\ChatMensaje;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
class MensajeChatCreado implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $mensaje;

    public function __construct(ChatMensaje $mensaje)
    {
        // cargamos voluntario para nombres/CI en la web
        $this->mensaje = $mensaje->load('voluntario');
    }

    public function broadcastOn(): Channel
    {
        // reutilizamos el mismo canal público que ya estás usando
        return new Channel('consultas');
    }

    public function broadcastAs(): string
    {
        return 'MensajeChatCreado';
    }

    public function broadcastWith(): array
    {
        return [
            'mensaje' => [
                'id'            => $this->mensaje->id,
                'voluntario_id' => $this->mensaje->voluntario_id,
                'de'            => $this->mensaje->de,       // 'voluntario' | 'admin'
                'texto'         => $this->mensaje->texto,
                'created_at'    => $this->mensaje->created_at,

                'voluntario'    => $this->mensaje->voluntario ? [
                    'id_usuario' => $this->mensaje->voluntario->id_usuario,
                    'nombres'    => $this->mensaje->voluntario->nombres,
                    'apellidos'  => $this->mensaje->voluntario->apellidos,
                    'ci'         => $this->mensaje->voluntario->ci,
                ] : null,
            ],
        ];
    }
}
