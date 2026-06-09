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
        $counter = Counter::query()->firstOrCreate([]);

        return view('admin.sections.counter.index', compact('counter'));
    }

    public function update(CounterUpdateRequest $request): RedirectResponse
    {
        $counter = Counter::query()->firstOrCreate([]);

        // Las columnas *_value son NOT NULL: una cifra vacía se guarda como 0
        // (el frontend oculta las cifras con valor 0, así no estorban).
        $data = $request->validated();
        foreach (['one', 'two', 'three', 'four'] as $k) {
            $data["counter_{$k}_value"] = (int) ($data["counter_{$k}_value"] ?? 0);
        }

        $counter->update($data);

        flash()->success('Contadores actualizados correctamente.');

        return redirect()->route('admin.counter.index');
    }
}
