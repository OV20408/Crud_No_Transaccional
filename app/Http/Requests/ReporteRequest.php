<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReporteRequest extends FormRequest
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
            'estado_general' => 'required|string|max:255',
            'fecha_generado' => 'required|date', // ✅ tipo fecha
            'observaciones' => 'nullable|string|max:2000',
            'recomendaciones' => 'nullable|string|max:255',
            'resumen_emocional' => 'nullable|string|max:1000',
            'resumen_fisico' => 'nullable|string|max:1000',
            'id_historial' => 'required|exists:historial_clinico,id', // ✅ relación
        ];
    }
}
