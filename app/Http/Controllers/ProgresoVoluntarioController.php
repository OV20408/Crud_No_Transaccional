<?php

namespace App\Http\Controllers;

use App\Models\ProgresoVoluntario;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Requests\ProgresoVoluntarioRequest;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProgresoVoluntarioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $progresoVoluntarios = ProgresoVoluntario::with(['etapa', 'historialClinico'])->paginate();

        return view('progreso-voluntario.index', compact('progresoVoluntarios'))
            ->with('i', ($request->input('page', 1) - 1) * $progresoVoluntarios->perPage());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $progresoVoluntario = new ProgresoVoluntario();
        $etapas = \App\Models\Etapa::all();
        $usuarios = \App\Models\User::all();  // <-- CORREGIDO

        return view('progreso-voluntario.create', compact('progresoVoluntario', 'etapas', 'usuarios'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProgresoVoluntarioRequest $request): RedirectResponse
    {
        ProgresoVoluntario::create($request->validated());

        return Redirect::route('progreso-voluntario.index')
            ->with('success', 'ProgresoVoluntario created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id): View
    {
        $progresoVoluntario = ProgresoVoluntario::find($id);

        return view('progreso-voluntario.show', compact('progresoVoluntario'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): View
    {
        $progresoVoluntario = ProgresoVoluntario::findOrFail($id);
        $etapas = \App\Models\Etapa::all();
        $usuarios = \App\Models\User::all();  // <-- CORREGIDO

        return view('progreso-voluntario.edit', compact('progresoVoluntario', 'etapas', 'usuarios'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProgresoVoluntarioRequest $request, ProgresoVoluntario $progresoVoluntario): RedirectResponse
    {
        $progresoVoluntario->update($request->validated());

        return Redirect::route('progreso-voluntario.index')
            ->with('success', 'ProgresoVoluntario updated successfully');
    }

    public function destroy($id): RedirectResponse
    {
        ProgresoVoluntario::find($id)->delete();

        return Redirect::route('progreso-voluntario.index')
            ->with('success', 'ProgresoVoluntario deleted successfully');
    }
}
