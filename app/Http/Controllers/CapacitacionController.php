<?php

namespace App\Http\Controllers;

use App\Models\Capacitacion;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Requests\CapacitacionRequest;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class CapacitacionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $capacitaciones = Capacitacion::paginate();

        return view('capacitacion.index', compact('capacitaciones'))
            ->with('i', ($request->input('page', 1) - 1) * $capacitaciones->perPage());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $capacitacion = new Capacitacion();

        return view('capacitacion.create', compact('capacitacion'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CapacitacionRequest $request): RedirectResponse
    {
        Capacitacion::create($request->validated());

        return Redirect::route('capacitaciones.index')
            ->with('success', 'Capacitacion created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id): View
    {
        $capacitacion = Capacitacion::find($id);

        return view('capacitacion.show', compact('capacitacion'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): View
    {
        $capacitacion = Capacitacion::find($id);

        return view('capacitacion.edit', compact('capacitacion'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CapacitacionRequest $request, $id): RedirectResponse
    {
        $capacitacion = Capacitacion::findOrFail($id);
        $capacitacion->update($request->validated());


        return Redirect::route('capacitaciones.index')
            ->with('success', 'Capacitacion updated successfully');
    }




    public function destroy($id): RedirectResponse
    {
        Capacitacion::find($id)->delete();

        return Redirect::route('capacitaciones.index')
            ->with('success', 'Capacitacion deleted successfully');
    }
}
