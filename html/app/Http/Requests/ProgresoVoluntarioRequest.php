<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProgresoVoluntarioRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'estado' => 'required|string|max:255',
            'fecha_inicio' => 'required|date',
            'fecha_finalizacion' => 'nullable|date',
            'id_etapa' => 'required|exists:etapa,id',
            'id_usuario' => 'required|exists:usuario,id_usuario',  // <-- CORREGIDO
        ];
    }

}
