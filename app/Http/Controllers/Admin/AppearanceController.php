<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GeneralSetting;
use App\View\Composers\BrandingComposer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

/**
 * Panel "Apariencia": colores, tipografía, logo, favicon y secciones del home.
 *
 * Todo lo que personaliza la marca sin tocar código vive aquí. Al guardar,
 * se invalida el cache de BrandingComposer → cambios visibles al instante.
 */
class AppearanceController extends Controller
{
    public function edit(): View
    {
        $setting = GeneralSetting::firstOrCreate(['id' => 1]);

        return view('admin.appearance.edit', [
            'setting' => $setting,
            'presets' => GeneralSetting::PRESETS,
            'fonts' => GeneralSetting::FONTS,
            'sections' => GeneralSetting::HOME_SECTIONS,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'site_name' => ['required', 'string', 'max:120'],
            'site_slogan' => ['nullable', 'string', 'max:255'],
            'copyright' => ['nullable', 'string', 'max:255'],
            'brand_color' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'accent_color' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'sun_color' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'ink_color' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'theme_preset' => ['nullable', 'string', 'in:'.implode(',', array_keys(GeneralSetting::PRESETS)).',custom'],
            'font_display' => ['required', 'string', 'max:80'],
            'font_body' => ['required', 'string', 'max:80'],
            'default_locale' => ['nullable', 'string', 'max:8'],
            'seo_default_description' => ['nullable', 'string', 'max:320'],
            'google_site_verification' => ['nullable', 'string', 'max:255'],
            'bing_site_verification' => ['nullable', 'string', 'max:255'],
            'google_analytics_id' => ['nullable', 'string', 'max:40', 'regex:/^(G|UA|GTM|AW|DC)-[A-Z0-9]+$/'],
            'enabled_sections' => ['nullable', 'array'],
            'enabled_sections.*' => ['string', 'in:'.implode(',', array_keys(GeneralSetting::HOME_SECTIONS))],
            'logo' => ['nullable', 'file', 'mimes:png,jpg,jpeg,webp,svg', 'max:2048'],
            'logo_dark' => ['nullable', 'file', 'mimes:png,jpg,jpeg,webp,svg', 'max:2048'],
            'favicon' => ['nullable', 'file', 'mimes:png,jpg,jpeg,webp,svg,ico', 'max:1024'],
            'og_image' => ['nullable', 'file', 'mimes:png,jpg,jpeg,webp', 'max:3072'],
            'hero_image' => ['nullable', 'file', 'mimes:png,jpg,jpeg,webp', 'max:4096'],
            // Botón flotante de WhatsApp (complemento gratis)
            'whatsapp_enabled' => ['nullable', 'boolean'],
            'whatsapp_number' => ['nullable', 'string', 'max:32'],
            'whatsapp_default_message' => ['nullable', 'string', 'max:255'],
            'whatsapp_key' => ['nullable', 'string', 'max:160'],
        ]);

        // Si activan el botón de WhatsApp, la llave debe ser válida y el número obligatorio.
        if ($request->boolean('whatsapp_enabled')) {
            if (! \App\Helpers\ActivationKey::validate((string) $request->input('whatsapp_key'), 'WA')) {
                return back()->withInput()->withErrors([
                    'whatsapp_key' => 'La llave de activación no es válida. Consíguela gratis en cursalia.org/whatsapp',
                ]);
            }
            if (! $request->filled('whatsapp_number')) {
                return back()->withInput()->withErrors([
                    'whatsapp_number' => 'Escribe tu número de WhatsApp (con código de país) para activar el botón.',
                ]);
            }
        }

        $setting = GeneralSetting::firstOrCreate(['id' => 1]);
        $setting->whatsapp_enabled = $request->boolean('whatsapp_enabled');
        $setting->whatsapp_number = $request->input('whatsapp_number');
        $setting->whatsapp_default_message = $request->input('whatsapp_default_message');
        $setting->whatsapp_key = $request->input('whatsapp_key');
        $setting->fill($request->only([
            'site_name', 'site_slogan', 'copyright',
            'brand_color', 'accent_color', 'sun_color', 'ink_color',
            'theme_preset', 'font_display', 'font_body',
            'default_locale', 'seo_default_description',
            'google_site_verification', 'bing_site_verification', 'google_analytics_id',
        ]));

        // enabled_sections: si admin desmarca todas, asume todas (no se queda sin home).
        $sections = $request->input('enabled_sections', []);
        $setting->enabled_sections = empty($sections) ? array_keys(GeneralSetting::HOME_SECTIONS) : $sections;

        // Subidas
        foreach (['logo', 'logo_dark', 'favicon', 'og_image', 'hero_image'] as $field) {
            if ($request->hasFile($field)) {
                Storage::disk('public')->makeDirectory('branding');
                if ($setting->{$field} && Storage::disk('public')->exists($setting->{$field})) {
                    Storage::disk('public')->delete($setting->{$field});
                }
                $ext = $request->file($field)->getClientOriginalExtension();
                $path = 'branding/'.$field.'_'.time().'.'.$ext;
                Storage::disk('public')->put($path, file_get_contents($request->file($field)->getRealPath()));
                $setting->{$field} = $path;
            }
        }

        $setting->save();
        BrandingComposer::flushCache();

        return back()->with('success', 'Apariencia actualizada. ¡Recarga el sitio público para ver los cambios!');
    }

    /** Aplica una paleta predefinida (AJAX o redirect form). */
    public function applyPreset(Request $request): RedirectResponse
    {
        $request->validate(['preset' => ['required', 'string', 'in:'.implode(',', array_keys(GeneralSetting::PRESETS))]]);
        $preset = GeneralSetting::PRESETS[$request->preset];

        $setting = GeneralSetting::firstOrCreate(['id' => 1]);
        $setting->theme_preset = $request->preset;
        $setting->brand_color = $preset['brand'];
        $setting->accent_color = $preset['accent'];
        $setting->sun_color = $preset['sun'];
        $setting->ink_color = $preset['ink'];
        $setting->save();

        BrandingComposer::flushCache();

        return back()->with('success', 'Paleta "'.$preset['label'].'" aplicada.');
    }
}
