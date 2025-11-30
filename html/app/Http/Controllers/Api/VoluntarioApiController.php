<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class VoluntarioApiController extends Controller
{
    // Lista de voluntarios
    public function index()
    {
        $voluntarios = User::where('id_rol', 2)->get();

        return response()->json([
            'success' => true,
            'total' => $voluntarios->count(),
            'data' => $voluntarios
        ]);
    }

    // Mostrar voluntario por ID
    public function show($id)
    {
        $voluntario = User::where('id_rol', 2)
            ->where('id_usuario', $id)
            ->first();

        if (!$voluntario) {
            return response()->json(['message' => 'Voluntario no encontrado'], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $voluntario
        ]);
    }


    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'nombres' => 'required|string|max:255',
                'apellidos' => 'required|string|max:255',
                'ci' => 'required|string|max:255|unique:usuario,ci',
                'fecha_nacimiento' => 'nullable|date',
                'genero' => 'nullable|string|max:50',
                'telefono' => 'nullable|string|max:255',
                'email' => 'nullable|string|email|max:255|unique:usuario,email',
                'direccion_domicilio' => 'nullable|string|max:255',
                'tipo_sangre' => 'nullable|string|max:10',
                'nivel_entrenamiento' => 'nullable|string|max:255',
                'entidad_pertenencia' => 'nullable|string|max:255',
                'contrasena' => 'required|string|min:4'
            ]);

            // Forzar rol = 2 (voluntario) y estado activo
            $validatedData['id_rol'] = 2;
            $validatedData['estado'] = 'activo';

            // Crear el voluntario (el mutador hasheará la contraseña automáticamente)
            $voluntario = User::create($validatedData);

            return response()->json([
                'success' => true,
                'message' => 'Voluntario registrado correctamente',
                'data' => $voluntario
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear el voluntario',
                'error' => $e->getMessage()
            ], 500);
        }
    }


}
