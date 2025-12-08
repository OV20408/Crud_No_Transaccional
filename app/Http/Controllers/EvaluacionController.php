<?php

namespace App\Http\Controllers;

use App\Models\Evaluacion;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Requests\EvaluacionRequest;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class EvaluacionController extends Controller
{

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $evaluaciones = Evaluacion::with(['reporte', 'test', 'universidad'])->paginate(); // ✅ carga relaciones

        return view('evaluacion.index', compact('evaluaciones'))
            ->with('i', ($request->input('page', 1) - 1) * $evaluaciones->perPage());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $evaluacion = new Evaluacion();
        $reportes = \App\Models\Reporte::all();
        $tests = \App\Models\Test::all();
        $universidades = \App\Models\Universidad::all();

        return view('evaluacion.create', compact('evaluacion', 'reportes', 'tests', 'universidades'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(EvaluacionRequest $request): RedirectResponse
    {
        Evaluacion::create($request->validated());

        return Redirect::route('evaluacion.index')
            ->with('success', 'Evaluacion created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id): View
    {
        $evaluacion = Evaluacion::find($id);

        return view('evaluacion.show', compact('evaluacion'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): View
    {
        $evaluacion = Evaluacion::findOrFail($id);
        $reportes = \App\Models\Reporte::all();
        $tests = \App\Models\Test::all();
        $universidades = \App\Models\Universidad::all();

        return view('evaluacion.edit', compact('evaluacion', 'reportes', 'tests', 'universidades'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(EvaluacionRequest $request, Evaluacion $evaluacion): RedirectResponse
    {
        $evaluacion->update($request->validated());

        return Redirect::route('evaluacion.index')
            ->with('success', 'Evaluacion updated successfully');
    }

    public function destroy($id): RedirectResponse
    {
        Evaluacion::find($id)->delete();

        return Redirect::route('evaluacion.index')
            ->with('success', 'Evaluacion deleted successfully');
    }
}
