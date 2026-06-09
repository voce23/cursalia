<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ContactSettingUpdateRequest;
use App\Models\ContactSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ContactSettingController extends Controller
{
    public function index(): View
    {
        $setting = ContactSetting::query()->firstOrCreate([]);

        return view('admin.contact-settings.index', compact('setting'));
    }

    public function update(ContactSettingUpdateRequest $request): RedirectResponse
    {
        $setting = ContactSetting::query()->firstOrCreate([]);
        $setting->update($request->validated());

        flash()->success('Configuración de contacto actualizada correctamente.');

        return redirect()->route('admin.contact-settings.index');
    }
}
