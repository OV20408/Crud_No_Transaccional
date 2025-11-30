<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReporteRequest extends FormRequest
{
    /**
     * Determina si el usuario está autorizado para realizar esta solicitud.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Reglas de validación para la tabla reporte.
     */
    public function rules(): array
    {
        return [
            'estado_general' => 'nullable|string|max:255',
            'fecha_generado' => 'nullable|date',
            'observaciones' => 'nullable|string|max:2000',
            'recomendaciones' => 'nullable|string|max:255',
            'resumen_emocional' => 'nullable|string|max:1000',
            'resumen_fisico' => 'nullable|string|max:1000',
        ];
    }
}
