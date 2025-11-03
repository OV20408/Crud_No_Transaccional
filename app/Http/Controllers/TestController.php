<?php

namespace App\Http\Controllers;

use App\Models\Test as TestModel;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Requests\TestRequest;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class TestController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $tests = TestModel::paginate();

        return view('test.index', compact('tests'))
            ->with('i', ($request->input('page', 1) - 1) * $tests->perPage());
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $test = new Test();

        return view('test.create', compact('test'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TestRequest $request): RedirectResponse
    {
        Test::create($request->validated());

        return Redirect::route('test.index')
            ->with('success', 'Test created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id): View
    {
        $test = Test::find($id);

        return view('test.show', compact('test'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): View
    {
        $test = Test::find($id);

        return view('test.edit', compact('test'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TestRequest $request, $id): RedirectResponse
    {
        $test = Test::findOrFail($id);
        $test->update($request->validated());

        return Redirect::route('test.index')
            ->with('success', 'Test updated successfully');
    }

    public function destroy($id): RedirectResponse
    {
        Test::find($id)->delete();

        return Redirect::route('test.index')
            ->with('success', 'Test deleted successfully');
    }
}
