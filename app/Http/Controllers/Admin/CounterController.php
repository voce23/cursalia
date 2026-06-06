<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CounterUpdateRequest;
use App\Models\Counter;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CounterController extends Controller
{
    public function index(): View
    {
        $counter = Counter::firstOrCreate(['id' => 1]);

        return view('admin.sections.counter.index', compact('counter'));
    }

    public function update(CounterUpdateRequest $request): RedirectResponse
    {
        $counter = Counter::firstOrCreate(['id' => 1]);
        $counter->update($request->validated());

        flash()->success('Contadores actualizados correctamente.');

        return redirect()->route('admin.counter.index');
    }
}
