<?php

namespace App\Http\Controllers;
use App\Models\SolicitudAyuda;
use Illuminate\Http\Request;

class AyudasSolicitadasController extends Controller
{
    public function index(Request $request)
    {
        $solicitudes = SolicitudAyuda::with('voluntario')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($s) {
                return [
                    'id'         => $s->id,
                    'voluntario_id' => $s->voluntario_id, 
                    'voluntario' => trim(($s->voluntario->nombres ?? '').' '.($s->voluntario->apellidos ?? '')),
                    'prioridad'  => strtolower($s->nivel_emergencia), // 'alto', 'medio', 'bajo'
                    'estado'     => strtolower($s->estado),
                    'tipo'       => $s->tipo_emergencia,
                    'direccion'  => $s->direccion ?? 'Ubicación reportada',
                    'detalle'    => $s->descripcion,
                    'latitud'    => (float) $s->latitud,
                    'longitud'   => (float) $s->longitud,
                    'fecha'      => optional($s->created_at)->format('d/m/Y H:i'),
                ];
            });

        return view('ayudas_solicitadas.index', [
            'solicitudesJson' => $solicitudes->toJson(),
        ]);
    }
}
