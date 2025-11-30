<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\Consulta;
use Illuminate\Support\Facades\Log;

class ConsultaRespondida implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $consulta;

    public function __construct(Consulta $consulta)
    {
        $this->consulta = $consulta->load('voluntario');
        Log::info('💬 ConsultaRespondida disparada', [
            'consulta_id' => $consulta->id,
            'respuesta' => $consulta->respuesta_admin,
        ]);
    }

    public function broadcastOn(): Channel
    {
        return new Channel('consultas');
    }

    public function broadcastAs(): string
    {
        return 'ConsultaRespondida';
    }

    public function broadcastWith(): array
    {
        return [
            'consulta' => [
                'id' => $this->consulta->id,
                'voluntario_id' => $this->consulta->voluntario_id,
                'mensaje' => $this->consulta->mensaje,
                'respuesta_admin' => $this->consulta->respuesta_admin,
                'estado' => $this->consulta->estado,
                'updated_at' => $this->consulta->updated_at,
                'voluntario' => [
                    'id_usuario' => $this->consulta->voluntario->id_usuario,
                    'nombres' => $this->consulta->voluntario->nombres,
                    'apellidos' => $this->consulta->voluntario->apellidos,
                    'ci' => $this->consulta->voluntario->ci,
                ],
            ],
        ];
    }
}