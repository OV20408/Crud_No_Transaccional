<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UniversidadResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'       => $this->id,
            'nombre'   => $this->nombre,
            'direccion'=> $this->direccion,
            'telefono' => $this->telefono,
            'created_at' => optional($this->created_at)->format('Y-m-d H:i:s'),
        ];
    }

}
