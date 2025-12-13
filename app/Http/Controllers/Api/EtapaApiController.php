<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ProgresoVoluntario;
use Illuminate\Http\Request;

class EtapaApiController extends Controller
{
    /**
     * Cambia el estado de la etapa para un voluntario
     * sin_empezar -> en_progreso -> completado -> sin_empezar
     */
public function toggleEstado(Request $request, $idEtapa)
{
    $data = $request->validate([
        'id_usuario' => 'required|integer|exists:usuario,id_usuario',
    ]);

    $progreso = ProgresoVoluntario::where('id_usuario', $data['id_usuario'])
        ->where('id_etapa', $idEtapa)
        ->firstOrFail();

    $estadoActual = $progreso->estado;
    switch ($estadoActual) {
        case 'sin_empezar':
            $nuevoEstado = 'en_progreso';
            break;
        case 'en_progreso':
            $nuevoEstado = 'completado';
            break;
        case 'completado':
        default:
            $nuevoEstado = 'sin_empezar';
            break;
    }

    $progreso->estado = $nuevoEstado;

    // 🔥 SOLUCIÓN: Solo actualizar si el valor NO es null
    if ($nuevoEstado === 'en_progreso' && !$progreso->fecha_inicio) {
        $progreso->fecha_inicio = now();
    }

    if ($nuevoEstado === 'completado') {
        $progreso->fecha_finalizacion = now();
    }

    // 🔥 NO INTENTAR SETEAR A NULL si la columna no lo permite
    if ($nuevoEstado === 'sin_empezar') {
        // Si la tabla NO permite NULL, usa una fecha por defecto
        // O simplemente no toques estas columnas
        // O usa el valor actual sin modificarlo
    }

    // Trazabilidad API Gateway
    $progreso->ci_voluntario = \App\Models\User::where('id_usuario', $data['id_usuario'])->value('ci');

    $progreso->save();

    return response()->json([
        'success' => true,
        'data'    => [
            'id_etapa'          => $progreso->id_etapa,
            'estado'            => $progreso->estado,
            'fecha_inicio'      => $progreso->fecha_inicio,
            'fecha_finalizacion'=> $progreso->fecha_finalizacion,
        ],
    ]);
}
}
