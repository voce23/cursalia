<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\HeaderNavigationLinkRequest;
use App\Models\HeaderNavigationLink;
use App\Services\GeneralSettingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class HeaderNavigationLinkController extends Controller
{
    public function index(): View
    {
        $items = HeaderNavigationLink::query()->orderBy('sort_order')->paginate(20);

        return view('admin.header-navigation-links.index', compact('items'));
    }

    public function create(): View
    {
        return view('admin.header-navigation-links.create');
    }

    public function store(HeaderNavigationLinkRequest $request): RedirectResponse
    {
        HeaderNavigationLink::create($request->validated() + [
            'is_active' => $request->boolean('is_active'),
            'open_in_new_tab' => $request->boolean('open_in_new_tab'),
        ]);

        GeneralSettingService::clearAppearanceCache();

        flash()->success('Link del menu principal creado correctamente.');

        return redirect()->route('admin.header-navigation-links.index');
    }

    public function edit(HeaderNavigationLink $headerNavigationLink): View
    {
        return view('admin.header-navigation-links.edit', ['item' => $headerNavigationLink]);
    }

    public function update(HeaderNavigationLinkRequest $request, HeaderNavigationLink $headerNavigationLink): RedirectResponse
    {
        $headerNavigationLink->update($request->validated() + [
            'is_active' => $request->boolean('is_active'),
            'open_in_new_tab' => $request->boolean('open_in_new_tab'),
        ]);

        GeneralSettingService::clearAppearanceCache();

        flash()->success('Link del menu principal actualizado correctamente.');

        return redirect()->route('admin.header-navigation-links.index');
    }

    public function destroy(HeaderNavigationLink $headerNavigationLink): RedirectResponse
    {
        $headerNavigationLink->delete();

        GeneralSettingService::clearAppearanceCache();

        flash()->success('Link del menu principal eliminado correctamente.');

        return redirect()->route('admin.header-navigation-links.index');
    }
}
