<?php

namespace App\Http\Controllers;

use App\Models\Universidad;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Requests\UniversidadRequest;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class UniversidadController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $universidades = Universidad::paginate();

        return view('universidad.index', compact('universidades'))
            ->with('i', ($request->input('page', 1) - 1) * $universidades->perPage());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $universidad = new Universidad();

        return view('universidad.create', compact('universidad'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UniversidadRequest $request): RedirectResponse
    {
        Universidad::create($request->validated());

        return Redirect::route('universidades.index')
            ->with('success', 'Universidad created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id): View
    {
        $universidad = Universidad::find($id);

        return view('universidad.show', compact('universidad'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): View
    {
        $universidad = Universidad::find($id);

        return view('universidad.edit', compact('universidad'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UniversidadRequest $request, $id): RedirectResponse
    {
        $universidad = Universidad::findOrFail($id);
        $universidad->update($request->validated());

        return Redirect::route('universidades.index')
            ->with('success', 'Universidad updated successfully');
    }


    public function destroy($id): RedirectResponse
    {
        Universidad::find($id)->delete();

        return Redirect::route('universidades.index')
            ->with('success', 'Universidad deleted successfully');
    }
}
