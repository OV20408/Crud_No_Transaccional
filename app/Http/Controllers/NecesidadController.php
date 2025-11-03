<?php

namespace App\Http\Controllers;

use App\Models\Necesidad;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Requests\NecesidadRequest;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class NecesidadController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $necesidades = Necesidad::paginate();

        return view('necesidad.index', compact('necesidades'))
            ->with('i', ($request->input('page', 1) - 1) * $necesidades->perPage());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $necesidad = new Necesidad();

        return view('necesidad.create', compact('necesidad'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(NecesidadRequest $request): RedirectResponse
    {
        Necesidad::create($request->validated());

        return Redirect::route('necesidades.index')
            ->with('success', 'Necesidad created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id): View
    {
        $necesidad = Necesidad::find($id);

        return view('necesidad.show', compact('necesidad'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): View
    {
        $necesidad = Necesidad::find($id);

        return view('necesidad.edit', compact('necesidad'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(NecesidadRequest $request, $id): RedirectResponse
    {
        $necesidad = Necesidad::findOrFail($id);
        $necesidad->update($request->validated());

        return Redirect::route('necesidades.index')
            ->with('success', 'Necesidad updated successfully');
    }







    public function destroy($id): RedirectResponse
    {
        Necesidad::find($id)->delete();

        return Redirect::route('necesidades.index')
            ->with('success', 'Necesidad deleted successfully');
    }
}
