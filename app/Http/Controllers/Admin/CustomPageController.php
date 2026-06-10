<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CustomPageRequest;
use App\Models\CustomPage;
use App\Services\GeneralSettingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CustomPageController extends Controller
{
    public function index(): View
    {
        $items = CustomPage::query()->latest()->paginate(15);

        return view('admin.custom-pages.index', compact('items'));
    }

    public function create(): View
    {
        return view('admin.custom-pages.create');
    }

    public function store(CustomPageRequest $request): RedirectResponse
    {
        CustomPage::create($request->validated() + [
            'show_at_nav' => $request->boolean('show_at_nav'),
            'status' => $request->boolean('status', true),
        ]);

        GeneralSettingService::clearAppearanceCache();

        flash()->success('Pagina personalizada creada correctamente.');

        return redirect()->route('admin.custom-pages.index');
    }

    public function edit(CustomPage $customPage): View
    {
        return view('admin.custom-pages.edit', compact('customPage'));
    }

    public function update(CustomPageRequest $request, CustomPage $customPage): RedirectResponse
    {
        $customPage->update($request->validated() + [
            'show_at_nav' => $request->boolean('show_at_nav'),
            'status' => $request->boolean('status', true),
        ]);

        GeneralSettingService::clearAppearanceCache();

        flash()->success('Pagina personalizada actualizada correctamente.');

        return redirect()->route('admin.custom-pages.index');
    }

    public function destroy(CustomPage $customPage): RedirectResponse
    {
        $customPage->delete();

        GeneralSettingService::clearAppearanceCache();

        flash()->success('Pagina personalizada eliminada correctamente.');

        return redirect()->route('admin.custom-pages.index');
    }
}
