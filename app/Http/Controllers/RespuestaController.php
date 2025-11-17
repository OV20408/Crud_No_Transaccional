<?php

namespace App\Http\Controllers;

use App\Models\Respuesta;
use App\Models\Pregunta;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Requests\RespuestaRequest;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class RespuestaController extends Controller
{
    public function index(Request $request): View
    {
        $respuesta = Respuesta::paginate();

        return view('respuesta.index', compact('respuesta'))
            ->with('i', ($request->input('page', 1) - 1) * $respuesta->perPage());
    }

    public function create(): View
    {
        $respuesta = new Respuesta();
        $evaluaciones = \App\Models\Evaluacion::all();
        $preguntas = \App\Models\Pregunta::all();

        return view('respuesta.create', compact('respuesta', 'evaluaciones', 'preguntas'));
    }

    public function store(RespuestaRequest $request): RedirectResponse
    {
        // Obtener la pregunta seleccionada
        $pregunta = Pregunta::findOrFail($request->pregunta_id);

        // Guardar la respuesta
        Respuesta::create([
            'respuesta_texto' => $request->respuesta_texto,
            'texto_pregunta' => $pregunta->texto,
            'id_evaluacion' => $request->id_evaluacion,
        ]);

        return Redirect::route('respuesta.index')
            ->with('success', 'Respuesta creada exitosamente.');
    }

    public function show($id): View
    {
        $respuesta = Respuesta::find($id);
        return view('respuesta.show', compact('respuesta'));
    }

    public function edit($id): View
    {
        $respuesta = Respuesta::findOrFail($id);
        $evaluaciones = \App\Models\Evaluacion::all();
        $preguntas = \App\Models\Pregunta::all();

        return view('respuesta.edit', compact('respuesta', 'evaluaciones', 'preguntas'));
    }

    public function update(RespuestaRequest $request, Respuesta $respuesta): RedirectResponse
    {
        $pregunta = Pregunta::findOrFail($request->pregunta_id);

        $respuesta->update([
            'respuesta_texto' => $request->respuesta_texto,
            'texto_pregunta' => $pregunta->texto,
            'id_evaluacion' => $request->id_evaluacion,
        ]);

        return Redirect::route('respuesta.index')
            ->with('success', 'Respuesta actualizada correctamente.');
    }

    public function destroy($id): RedirectResponse
    {
        Respuesta::find($id)->delete();

        return Redirect::route('respuesta.index')
            ->with('success', 'Respuesta eliminada correctamente.');
    }
}
