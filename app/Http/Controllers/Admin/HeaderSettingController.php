<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\HeaderSettingUpdateRequest;
use App\Models\HeaderSetting;
use App\Services\GeneralSettingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class HeaderSettingController extends Controller
{
    public function index(): View
    {
        $setting = HeaderSetting::query()->firstOrCreate([], [
            'category_button_text' => 'Categorías',
            'category_limit' => 6,
            'show_search' => true,
            'search_placeholder' => 'Buscar cursos...',
        ]);

        return view('admin.header-settings.index', compact('setting'));
    }

    public function update(HeaderSettingUpdateRequest $request): RedirectResponse
    {
        $setting = HeaderSetting::query()->firstOrCreate([], [
            'category_button_text' => 'Categorías',
            'category_limit' => 6,
            'show_search' => true,
            'search_placeholder' => 'Buscar cursos...',
        ]);

        $setting->update($request->validated() + [
            'show_search' => $request->boolean('show_search'),
        ]);

        GeneralSettingService::clearAppearanceCache();

        flash()->success('Configuración del header actualizada correctamente.');

        return redirect()->route('admin.header-settings.index');
    }
}
