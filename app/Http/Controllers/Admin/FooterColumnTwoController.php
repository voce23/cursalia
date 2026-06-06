<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\FooterColumnTwoRequest;
use App\Models\FooterColumnTwo;
use App\Services\GeneralSettingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class FooterColumnTwoController extends Controller
{
    public function index(): View
    {
        $items = FooterColumnTwo::query()->orderBy('sort_order')->paginate(20);

        return view('admin.footer-column-two.index', compact('items'));
    }

    public function create(): View
    {
        return view('admin.footer-column-two.create');
    }

    public function store(FooterColumnTwoRequest $request): RedirectResponse
    {
        FooterColumnTwo::create($request->validated() + [
            'is_active' => $request->boolean('is_active'),
        ]);

        GeneralSettingService::clearAppearanceCache();

        flash()->success('Link de la columna 2 creado correctamente.');

        return redirect()->route('admin.footer-column-two.index');
    }

    public function edit(FooterColumnTwo $footerColumnTwo): View
    {
        return view('admin.footer-column-two.edit', ['item' => $footerColumnTwo]);
    }

    public function update(FooterColumnTwoRequest $request, FooterColumnTwo $footerColumnTwo): RedirectResponse
    {
        $footerColumnTwo->update($request->validated() + [
            'is_active' => $request->boolean('is_active'),
        ]);

        GeneralSettingService::clearAppearanceCache();

        flash()->success('Link de la columna 2 actualizado correctamente.');

        return redirect()->route('admin.footer-column-two.index');
    }

    public function destroy(FooterColumnTwo $footerColumnTwo): RedirectResponse
    {
        $footerColumnTwo->delete();

        GeneralSettingService::clearAppearanceCache();

        flash()->success('Link de la columna 2 eliminado correctamente.');

        return redirect()->route('admin.footer-column-two.index');
    }
}