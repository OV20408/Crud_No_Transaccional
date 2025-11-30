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
    public function index(Request $request): View
    {
        $tests = TestModel::paginate();

        return view('test.index', compact('tests'))
            ->with('i', ($request->input('page', 1) - 1) * $tests->perPage());
    }

    public function create(): View
    {
        $test = new TestModel();

        return view('test.create', compact('test'));
    }

    public function store(TestRequest $request): RedirectResponse
    {
        TestModel::create($request->validated());

        return Redirect::route('test.index')
            ->with('success', 'Test created successfully.');
    }

    public function show($id): View
    {
        $test = TestModel::find($id);

        return view('test.show', compact('test'));
    }

    public function edit($id): View
    {
        $test = TestModel::find($id);

        return view('test.edit', compact('test'));
    }

    public function update(TestRequest $request, $id): RedirectResponse
    {
        $test = TestModel::findOrFail($id);
        $test->update($request->validated());

        return Redirect::route('test.index')
            ->with('success', 'Test updated successfully');
    }

    public function destroy($id): RedirectResponse
    {
        TestModel::find($id)->delete();

        return Redirect::route('test.index')
            ->with('success', 'Test deleted successfully');
    }
}
