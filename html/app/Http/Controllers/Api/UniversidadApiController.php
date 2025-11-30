<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Universidad;
use Illuminate\Http\Request;
use App\Http\Resources\UniversidadResource;


class UniversidadApiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $universidades = Universidad::all();
        return UniversidadResource::collection($universidades);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
        ]);

        $universidad = Universidad::create($validated);
        return new UniversidadResource($universidad);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $universidad = Universidad::findOrFail($id);
        return new UniversidadResource($universidad);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $universidad = Universidad::findOrFail($id);
        
        $validated = $request->validate([
            'nombre' => 'sometimes|required|string|max:255',
        ]);

        $universidad->update($validated);
        return new UniversidadResource($universidad);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $universidad = Universidad::findOrFail($id);
        $universidad->delete();
        
        return response()->json([
            'message' => 'Universidad eliminada correctamente'
        ], 200);
    } 
}
