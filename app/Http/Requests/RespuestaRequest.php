<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RespuestaRequest extends FormRequest
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
            'respuesta_texto' => 'required|string|max:255',
            'texto_pregunta' => 'required|string|max:255',
            'id_evaluacion' => 'required|exists:evaluacion,id',
            'id_pregunta' => 'required|exists:pregunta,id',
        ];
    }
}
