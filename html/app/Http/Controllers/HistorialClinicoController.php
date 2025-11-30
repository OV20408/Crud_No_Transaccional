<?php

namespace App\Http\Controllers;

use App\Models\HistorialClinico;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Requests\HistorialClinicoRequest;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class HistorialClinicoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $historial_clinico  = HistorialClinico::paginate();

        return view('historial_clinico.index', compact('historial_clinico'))
            ->with('i', ($request->input('page', 1) - 1) * $historial_clinico ->perPage());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $historialClinico = new HistorialClinico();

        return view('historial_clinico.create', compact('historialClinico'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(HistorialClinicoRequest $request): RedirectResponse
    {
        HistorialClinico::create($request->validated());

        return Redirect::route('historial_clinico.index')
            ->with('success', 'HistorialClinico created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id): View
    {
        $historialClinico = HistorialClinico::find($id);

        return view('historial_clinico.show', compact('historialClinico'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): View
    {
        $historialClinico = HistorialClinico::find($id);

        return view('historial_clinico.edit', compact('historialClinico'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(HistorialClinicoRequest $request, $id): RedirectResponse
    {
        $historialClinico = HistorialClinico::findOrFail($id);
        $historialClinico->update($request->validated());

        return Redirect::route('historial_clinico.index')
            ->with('success', 'HistorialClinico updated successfully');
    }

    public function destroy($id): RedirectResponse
    {
        HistorialClinico::find($id)->delete();

        return Redirect::route('historial_clinico.index')
            ->with('success', 'HistorialClinico deleted successfully');
    }
}
