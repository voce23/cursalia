<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\TopBarUpdateRequest;
use App\Models\TopBar;
use App\Services\GeneralSettingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class TopBarController extends Controller
{
    public function index(): View
    {
        $setting = TopBar::query()->firstOrCreate(['id' => 1], [
            'background_color' => '#111827',
            'text_color' => '#d1d5db',
            'is_active' => true,
        ]);

        return view('admin.top-bar.index', compact('setting'));
    }

    public function update(TopBarUpdateRequest $request): RedirectResponse
    {
        $setting = TopBar::query()->firstOrCreate(['id' => 1], [
            'background_color' => '#111827',
            'text_color' => '#d1d5db',
            'is_active' => true,
        ]);

        $setting->update($request->validated() + [
            'is_active' => $request->boolean('is_active'),
        ]);

        GeneralSettingService::clearAppearanceCache();

        flash()->success('Top bar actualizada correctamente.');

        return redirect()->route('admin.top-bar.index');
    }
}