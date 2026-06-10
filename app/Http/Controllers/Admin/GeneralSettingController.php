<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\GeneralSettingUpdateRequest;
use App\Models\GeneralSetting;
use App\Services\GeneralSettingService;
use enshrined\svgSanitize\Sanitizer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class GeneralSettingController extends Controller
{
    public function index(): View
    {
        $setting = GeneralSetting::firstOrCreate(['id' => 1], ['site_name' => 'LMSL13']);

        return view('admin.general-settings.index', compact('setting'));
    }

    public function update(GeneralSettingUpdateRequest $request): RedirectResponse
    {
        $setting = GeneralSetting::firstOrCreate(['id' => 1], ['site_name' => 'LMSL13']);

        $data = $request->only([
            'site_name',
            'site_slogan',
            'copyright',
            'mail_mailer',
            'mail_scheme',
            'mail_host',
            'mail_port',
            'mail_username',
            'mail_password',
            'mail_from_address',
            'mail_from_name',
        ]);

        if (! $request->filled('mail_password')) {
            unset($data['mail_password']);
        }

        $data['mail_scheme'] = $request->filled('mail_scheme') ? $request->string('mail_scheme')->toString() : null;
        $data['mail_port'] = $request->filled('mail_port') ? (int) $request->input('mail_port') : null;
        $data['mail_mailer'] = $request->filled('mail_mailer') ? $request->string('mail_mailer')->toString() : 'log';

        if ($request->hasFile('logo')) {
            if ($setting->logo) {
                Storage::disk('public')->delete($setting->logo);
            }
            $data['logo'] = $request->file('logo')->store('settings', 'public');
            $this->sanitizeIfSvg($data['logo']);
        }

        if ($request->hasFile('favicon')) {
            if ($setting->favicon) {
                Storage::disk('public')->delete($setting->favicon);
            }
            $data['favicon'] = $request->file('favicon')->store('settings', 'public');
            $this->sanitizeIfSvg($data['favicon']);
        }

        $setting->update($data);

        GeneralSettingService::clearAppearanceCache();

        flash()->success('Configuración general y SMTP actualizada correctamente.');

        return redirect()->route('admin.general-settings.index');
    }

    /**
     * Sanitiza un SVG recién subido (elimina <script>, handlers on*, etc.)
     * para neutralizar XSS embebido. No afecta a PNG/JPG/etc.
     */
    private function sanitizeIfSvg(?string $path): void
    {
        if (! $path || ! str_ends_with(strtolower($path), '.svg')) {
            return;
        }

        $disk = Storage::disk('public');
        $content = $disk->get($path);
        if ($content === null) {
            return;
        }

        $clean = (new Sanitizer)->sanitize($content);
        if ($clean !== false) {
            $disk->put($path, $clean);
        }
    }
}
