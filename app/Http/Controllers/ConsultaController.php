<?php

namespace App\Http\Controllers;

use App\Models\Consulta;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Requests\ConsultaRequest;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ConsultaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $consultas = Consulta::paginate();

        return view('consulta.index', compact('consultas'))
            ->with('i', ($request->input('page', 1) - 1) * $consultas->perPage());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
{
    $consulta = new Consulta();
    $voluntarios = \App\Models\User::all();
    $necesidades = \App\Models\Necesidad::all();

    return view('consulta.create', compact('consulta', 'voluntarios', 'necesidades'));
}

    /**
     * Store a newly created resource in storage.
     */
    public function store(ConsultaRequest $request): RedirectResponse
    {
        Consulta::create($request->validated());

        return Redirect::route('consultas.index')
            ->with('success', 'Consulta created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id): View
    {
        $consulta = Consulta::find($id);

        return view('consulta.show', compact('consulta'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): View
{
    $consulta = Consulta::findOrFail($id);
    $voluntarios = \App\Models\User::all();
    $necesidades = \App\Models\Necesidad::all();

    return view('consulta.edit', compact('consulta', 'voluntarios', 'necesidades'));
}


    /**
     * Update the specified resource in storage.
     */
    public function update(ConsultaRequest $request, Consulta $consulta): RedirectResponse
    {
        $consulta->update($request->validated());

        return Redirect::route('consultas.index')
            ->with('success', 'Consulta updated successfully');
    }

    public function destroy($id): RedirectResponse
    {
        Consulta::find($id)->delete();

        return Redirect::route('consultas.index')
            ->with('success', 'Consulta deleted successfully');
    }
}
