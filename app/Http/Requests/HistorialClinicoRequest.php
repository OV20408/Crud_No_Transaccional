<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class HistorialClinicoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id_usuario' => 'required|exists:usuario,id_usuario|unique:historial_clinico,id_usuario',
            'fecha_inicio' => 'nullable|date',
            'fecha_actualizacion' => 'nullable|date',
        ];
    }
}
