<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\FooterColumnOneRequest;
use App\Models\FooterColumnOne;
use App\Services\GeneralSettingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class FooterColumnOneController extends Controller
{
    public function index(): View
    {
        $items = FooterColumnOne::query()->orderBy('sort_order')->paginate(20);

        return view('admin.footer-column-one.index', compact('items'));
    }

    public function create(): View
    {
        return view('admin.footer-column-one.create');
    }

    public function store(FooterColumnOneRequest $request): RedirectResponse
    {
        FooterColumnOne::create($request->validated() + [
            'is_active' => $request->boolean('is_active'),
        ]);

        GeneralSettingService::clearAppearanceCache();

        flash()->success('Link de la columna 1 creado correctamente.');

        return redirect()->route('admin.footer-column-one.index');
    }

    public function edit(FooterColumnOne $footerColumnOne): View
    {
        return view('admin.footer-column-one.edit', ['item' => $footerColumnOne]);
    }

    public function update(FooterColumnOneRequest $request, FooterColumnOne $footerColumnOne): RedirectResponse
    {
        $footerColumnOne->update($request->validated() + [
            'is_active' => $request->boolean('is_active'),
        ]);

        GeneralSettingService::clearAppearanceCache();

        flash()->success('Link de la columna 1 actualizado correctamente.');

        return redirect()->route('admin.footer-column-one.index');
    }

    public function destroy(FooterColumnOne $footerColumnOne): RedirectResponse
    {
        $footerColumnOne->delete();

        GeneralSettingService::clearAppearanceCache();

        flash()->success('Link de la columna 1 eliminado correctamente.');

        return redirect()->route('admin.footer-column-one.index');
    }
}