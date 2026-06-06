<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SocialLinkRequest;
use App\Models\SocialLink;
use App\Services\GeneralSettingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SocialLinkController extends Controller
{
    public function index(): View
    {
        $items = SocialLink::query()->orderBy('sort_order')->paginate(20);

        return view('admin.social-links.index', compact('items'));
    }

    public function create(): View
    {
        return view('admin.social-links.create');
    }

    public function store(SocialLinkRequest $request): RedirectResponse
    {
        SocialLink::create($request->validated() + [
            'is_active' => $request->boolean('is_active'),
        ]);

        GeneralSettingService::clearAppearanceCache();

        flash()->success('Red social creada correctamente.');

        return redirect()->route('admin.social-links.index');
    }

    public function edit(SocialLink $socialLink): View
    {
        return view('admin.social-links.edit', ['item' => $socialLink]);
    }

    public function update(SocialLinkRequest $request, SocialLink $socialLink): RedirectResponse
    {
        $socialLink->update($request->validated() + [
            'is_active' => $request->boolean('is_active'),
        ]);

        GeneralSettingService::clearAppearanceCache();

        flash()->success('Red social actualizada correctamente.');

        return redirect()->route('admin.social-links.index');
    }

    public function destroy(SocialLink $socialLink): RedirectResponse
    {
        $socialLink->delete();

        GeneralSettingService::clearAppearanceCache();

        flash()->success('Red social eliminada correctamente.');

        return redirect()->route('admin.social-links.index');
    }
}