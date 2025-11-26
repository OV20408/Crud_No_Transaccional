<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ConsultaRequest extends FormRequest
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
        'voluntario_id' => 'required|exists:usuario,id_usuario',
        'necesidad_id'  => 'required|exists:necesidad,id',
        'mensaje'       => 'required|string|max:500',
        'estado'        => 'required|string|max:20',
    ];
    }
}
