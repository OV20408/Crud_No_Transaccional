<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\Consulta;
use Illuminate\Support\Facades\Log;

class ConsultaCreada implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $consulta;

    public function __construct(Consulta $consulta)
    {
        $this->consulta = $consulta->load('voluntario');
        Log::info('🔥 ConsultaCreada disparada', [
            'consulta_id' => $consulta->id,
            'voluntario_id' => $consulta->voluntario_id,
        ]);
    }

    public function broadcastOn(): Channel
    {
        return new Channel('consultas');
    }

    public function broadcastAs(): string
    {
        return 'ConsultaCreada';
    }

    public function broadcastWith(): array
    {
        return [
            'consulta' => [
                'id' => $this->consulta->id,
                'voluntario_id' => $this->consulta->voluntario_id,
                'mensaje' => $this->consulta->mensaje,
                'estado' => $this->consulta->estado,
                'created_at' => $this->consulta->created_at,
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