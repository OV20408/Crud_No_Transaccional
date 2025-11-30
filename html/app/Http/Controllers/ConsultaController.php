<?php

namespace App\Http\Controllers;

use App\Models\Consulta;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Requests\ConsultaRequest;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use App\Events\ConsultaRespondida;

class ConsultaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $consultas = Consulta::with('voluntario')
            ->orderBy('created_at', 'asc')
            ->get(); // Cambiado de paginate() a get() para el chat

        return view('consultas-web.index', compact('consultas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $consulta = new Consulta();
        $voluntarios = \App\Models\User::all();
        $necesidades = \App\Models\Necesidad::all();

        return view('consultas-web.create', compact('consulta', 'voluntarios', 'necesidades'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ConsultaRequest $request): RedirectResponse
    {
        Consulta::create($request->validated());

        return Redirect::route('consultas-web.index')
            ->with('success', 'Consulta created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id): View
    {
        $consulta = Consulta::find($id);

        return view('consultas-web.show', compact('consulta'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): View
    {
        $consulta = Consulta::findOrFail($id);
        $voluntarios = \App\Models\User::all();
        $necesidades = \App\Models\Necesidad::all();

        return view('consultas-web.edit', compact('consulta', 'voluntarios', 'necesidades'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ConsultaRequest $request, Consulta $consulta): RedirectResponse
    {
        $consulta->update($request->validated());

        return Redirect::route('consultas-web.index')
            ->with('success', 'Consulta updated successfully');
    }

    public function destroy($id): RedirectResponse
    {
        Consulta::find($id)->delete();

        return Redirect::route('consultas-web.index')
            ->with('success', 'Consulta deleted successfully');
    }

    /**
     * Responder a una consulta (para el chat en tiempo real)
     */
    public function responder(Request $request, $id)
    {
        $validated = $request->validate([
            'respuesta_admin' => 'required|string|max:500',
        ]);

        $consulta = Consulta::findOrFail($id);
        
        $consulta->update([
            'respuesta_admin' => $validated['respuesta_admin'],
            'estado'          => 'respondida',
        ]);

        // Cargar relaciones necesarias para el evento
        $consulta->load('voluntario');

        // Disparar el evento para notificar al móvil en tiempo real
        ConsultaRespondida::dispatch($consulta);

        // Si es una petición AJAX, devolver JSON
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Respuesta enviada correctamente',
                'data'    => $consulta,
            ]);
        }

        // Si no, redirect normal
        return Redirect::back()->with('success', 'Respuesta enviada correctamente');
    }
}