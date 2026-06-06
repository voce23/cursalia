<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\FooterUpdateRequest;
use App\Models\Footer;
use App\Services\GeneralSettingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class FooterController extends Controller
{
    public function index(): View
    {
        $setting = Footer::query()->firstOrCreate(['id' => 1], [
            'contact_title' => 'Contacto',
            'is_active' => true,
        ]);

        return view('admin.footer.index', compact('setting'));
    }

    public function update(FooterUpdateRequest $request): RedirectResponse
    {
        $setting = Footer::query()->firstOrCreate(['id' => 1], [
            'contact_title' => 'Contacto',
            'is_active' => true,
        ]);

        $setting->update($request->validated() + [
            'is_active' => $request->boolean('is_active'),
        ]);

        GeneralSettingService::clearAppearanceCache();

        flash()->success('Footer actualizado correctamente.');

        return redirect()->route('admin.footer.index');
    }
}