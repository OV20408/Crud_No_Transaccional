<?php

namespace App\Http\Controllers;

use App\Models\Etapa;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Requests\EtapaRequest;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class EtapaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $etapas = Etapa::paginate();

        return view('etapa.index', compact('etapas'))
            ->with('i', ($request->input('page', 1) - 1) * $etapas->perPage());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $etapa = new Etapa();
        $cursos = \App\Models\Curso::all(); // 🔹 trae todos los cursos
        return view('etapa.create', compact('etapa', 'cursos'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(EtapaRequest $request): RedirectResponse
    {
        Etapa::create($request->validated());

        return Redirect::route('etapas.index')
            ->with('success', 'Etapa created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id): View
    {
        $etapa = Etapa::find($id);

        return view('etapa.show', compact('etapa'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): View
    {
        $etapa = Etapa::find($id);
        $cursos = \App\Models\Curso::all(); // 🔹 igual para el select al editar
        return view('etapa.edit', compact('etapa', 'cursos'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(EtapaRequest $request, Etapa $etapa): RedirectResponse
    {
        $etapa->update($request->validated());

        return Redirect::route('etapas.index')
            ->with('success', 'Etapa updated successfully');
    }

    public function destroy($id): RedirectResponse
    {
        Etapa::find($id)->delete();

        return Redirect::route('etapas.index')
            ->with('success', 'Etapa deleted successfully');
    }
}
