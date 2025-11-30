<?php

namespace App\Http\Controllers;

use App\Models\Rol;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Requests\RolRequest;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class RolController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $roles = Rol::paginate();

        return view('rol.index', compact('roles'))
            ->with('i', ($request->input('page', 1) - 1) * $roles->perPage());
    }

    





    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $rol = new Rol();

        return view('rol.create', compact('rol'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(RolRequest $request): RedirectResponse
    {
        Rol::create($request->validated());

        return Redirect::route('roles.index')
            ->with('success', 'Rol created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id): View
    {
        $rol = Rol::find($id);

        return view('rol.show', compact('rol'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): View
    {
        $rol = Rol::find($id);

        return view('rol.edit', compact('rol'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(RolRequest $request, $id): RedirectResponse
    {
        $rol = Rol::findOrFail($id);
        $rol->update($request->validated());


        return Redirect::route('roles.index')
            ->with('success', 'Rol updated successfully');
    }





    public function destroy($id): RedirectResponse
    {
        Rol::find($id)->delete();

        return Redirect::route('roles.index')
            ->with('success', 'Rol deleted successfully');
    }
}
