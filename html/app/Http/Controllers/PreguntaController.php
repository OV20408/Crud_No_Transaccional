<?php

namespace App\Http\Controllers;

use App\Models\Pregunta;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Requests\PreguntaRequest;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class PreguntaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $pregunta = Pregunta::paginate();

        return view('pregunta.index', compact('pregunta'))
            ->with('i', ($request->input('page', 1) - 1) * $pregunta->perPage());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $pregunta = new Pregunta();
        $tests = \App\Models\Test::all(); // ✅ Para el select
        return view('pregunta.create', compact('pregunta', 'tests'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PreguntaRequest $request): RedirectResponse
    {
        Pregunta::create($request->validated());

        return Redirect::route('pregunta.index')
            ->with('success', 'Pregunta created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id): View
    {
        $pregunta = Pregunta::find($id);

        return view('pregunta.show', compact('pregunta'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): View
    {
        $pregunta = Pregunta::findOrFail($id);
        $tests = \App\Models\Test::all(); // ✅ Para el select
        return view('pregunta.edit', compact('pregunta', 'tests'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PreguntaRequest $request, Pregunta $pregunta): RedirectResponse
    {
        $pregunta->update($request->validated());

        return Redirect::route('pregunta.index')
            ->with('success', 'Pregunta updated successfully');
    }

    public function destroy($id): RedirectResponse
{
    $pregunta = Pregunta::findOrFail($id);
    $pregunta->delete();

    return Redirect::route('pregunta.index')
        ->with('success', 'Pregunta deleted successfully');
}


}
