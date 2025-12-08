<?php

namespace App\Http\Controllers;

use App\Models\Curso;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Requests\CursoRequest;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class CursoController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:crear_cursos|editar_cursos')->except(['index', 'show']);
        $this->middleware('permission:gestionar_capacitaciones')->only(['index', 'show']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $cursos = Curso::paginate();

        return view('curso.index', compact('cursos'))
            ->with('i', ($request->input('page', 1) - 1) * $cursos->perPage());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $curso = new Curso();
        $capacitaciones = \App\Models\Capacitacion::all(); // 🔹 trae todas las capacitaciones
        return view('curso.create', compact('curso', 'capacitaciones'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CursoRequest $request): RedirectResponse
    {
        Curso::create($request->validated());

        return Redirect::route('curso.index')
            ->with('success', 'Curso created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id): View
    {
        $curso = Curso::find($id);

        return view('curso.show', compact('curso'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): View
    {
        $curso = Curso::find($id);
        $capacitaciones = \App\Models\Capacitacion::all();
        return view('curso.edit', compact('curso', 'capacitaciones'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CursoRequest $request, Curso $curso): RedirectResponse
    {
        $curso->update($request->validated());

        return Redirect::route('curso.index')
            ->with('success', 'Curso updated successfully');
    }

    public function destroy($id): RedirectResponse
    {
        Curso::find($id)->delete();

        return Redirect::route('curso.index')
            ->with('success', 'Curso deleted successfully');
    }
}
