<?php

namespace App\View\Composers;

use App\Models\Footer;
use App\Models\FooterColumnOne;
use App\Models\FooterColumnTwo;
use App\Models\GeneralSetting;
use App\Models\HeaderNavigationLink;
use App\Models\HeaderSetting;
use App\Models\SocialLink;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

/**
 * ViewComposer global del white-label.
 *
 * Regla del proyecto: NO cacheamos objetos (ni stdClass ni modelos Eloquent
 * porque al deserializar fallan). Cacheamos arrays planos y los convertimos
 * a objetos solo en memoria, después de leer del cache.
 */
class BrandingComposer
{
    private const TTL = 3600;

    public function compose(View $view): void
    {
        $view->with([
            'generalSetting'  => (object) $this->generalSettingArr(),
            'headerSetting'   => (object) $this->headerSettingArr(),
            'headerLinks'     => $this->headerLinks(),
            'socialLinks'     => $this->socialLinks(),
            'footerInfo'      => (object) $this->footerInfoArr(),
            'footerColumnOne' => $this->footerColumn('one'),
            'footerColumnTwo' => $this->footerColumn('two'),
            'legalPages'      => $this->legalPages(),
        ]);
    }

    private function generalSettingArr(): array
    {
        return Cache::remember('branding.general', self::TTL, function () {
            $s = GeneralSetting::query()->first();
            $defaults = $this->defaultsArr();
            if (! $s) {
                return $defaults;
            }
            return array_merge($defaults, [
                'site_name'    => $s->site_name      ?: $defaults['site_name'],
                'site_slogan'  => $s->site_slogan    ?: $defaults['site_slogan'],
                'logo'         => $s->logo,
                'favicon'      => $s->favicon,
                'copyright'    => $s->copyright      ?: $defaults['copyright'],
                'brand_color'  => $s->brand_color    ?: $defaults['brand_color'],
                'accent_color' => $s->accent_color   ?: $defaults['accent_color'],
                'sun_color'    => $s->sun_color      ?: $defaults['sun_color'],
                'ink_color'    => $s->ink_color      ?: $defaults['ink_color'],
                'font_display' => $s->font_display   ?: $defaults['font_display'],
                'font_body'    => $s->font_body      ?: $defaults['font_body'],
                'theme_preset' => $s->theme_preset   ?: $defaults['theme_preset'],
                'default_locale'          => $s->default_locale          ?: $defaults['default_locale'],
                'seo_default_description' => $s->seo_default_description ?: $defaults['seo_default_description'],
                'og_image'                => $s->og_image,
                'enabled_sections'        => is_array($s->enabled_sections) ? $s->enabled_sections : $defaults['enabled_sections'],
            ]);
        });
    }

    private function headerSettingArr(): array
    {
        return Cache::remember('branding.header_setting', self::TTL, function () {
            $h = HeaderSetting::query()->first();
            return [
                'category_button_text' => $h?->category_button_text ?: 'Categorías',
                'category_limit'       => (int) ($h?->category_limit ?: 8),
                'show_search'          => (bool) ($h?->show_search ?? true),
                'search_placeholder'   => $h?->search_placeholder ?: '¿Qué quieres aprender hoy?',
            ];
        });
    }

    private function headerLinks(): array
    {
        return Cache::remember('branding.header_links', self::TTL, fn () =>
            HeaderNavigationLink::query()
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->get(['title', 'url', 'open_in_new_tab'])
                ->map(fn ($l) => [
                    'title'        => $l->title,
                    'url'          => $l->url,
                    'open_new_tab' => (bool) $l->open_in_new_tab,
                ])
                ->all()
        );
    }

    private function socialLinks(): array
    {
        return Cache::remember('branding.social_links', self::TTL, fn () =>
            SocialLink::query()
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->get(['name', 'icon_class', 'url'])
                ->map(fn ($s) => [
                    'name' => $s->name,
                    'icon' => $s->icon_class,
                    'url'  => $s->url,
                ])
                ->all()
        );
    }

    private function footerInfoArr(): array
    {
        return Cache::remember('branding.footer_info', self::TTL, function () {
            $f = Footer::query()->first();
            return [
                'description'   => $f?->description   ?: 'Aprende algo nuevo, a tu manera. Cursos prácticos creados por mentores reales.',
                'contact_title' => $f?->contact_title ?: 'Contacto',
                'email'         => $f?->email         ?: 'hola@cursalia.com',
                'phone'         => $f?->phone,
                'address'       => $f?->address       ?: 'Madrid, España',
                'bottom_text'   => $f?->bottom_text   ?: 'Hecho con ❤️ y Laravel 13',
                'is_active'     => (bool) ($f?->is_active ?? true),
            ];
        });
    }

    private function footerColumn(string $which): array
    {
        $cacheKey = "branding.footer_col_{$which}";
        $model    = $which === 'one' ? FooterColumnOne::class : FooterColumnTwo::class;

        return Cache::remember($cacheKey, self::TTL, fn () =>
            $model::query()
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->get(['title', 'url'])
                ->map(fn ($l) => ['title' => $l->title, 'url' => $l->url])
                ->all()
        );
    }

    private function legalPages(): array
    {
        return Cache::remember('branding.legal_pages', self::TTL, fn () =>
            \App\Models\CustomPage::query()
                ->where('status', true)
                ->where('slug', 'like', 'legal/%')
                ->orderBy('title')
                ->get(['title', 'slug'])
                ->map(fn ($p) => ['title' => $p->title, 'url' => '/'.$p->slug])
                ->all()
        );
    }

    private function defaultsArr(): array
    {
        return [
            'site_name'    => 'Cursalia',
            'site_slogan'  => 'Aprende algo nuevo, a tu manera',
            'logo'         => null,
            'favicon'      => null,
            'copyright'    => '© '.date('Y').' Cursalia.',
            'brand_color'  => '#10B981',
            'accent_color' => '#FB7185',
            'sun_color'    => '#FBBF24',
            'ink_color'    => '#1F2933',
            'font_display' => 'Poppins',
            'font_body'    => 'Inter',
            'theme_preset' => 'cursalia-green',
            'default_locale'          => 'es',
            'seo_default_description' => 'Cursalia · Plataforma de cursos online.',
            'og_image'                => null,
            'enabled_sections'        => array_keys(GeneralSetting::HOME_SECTIONS),
        ];
    }

    public static function flushCache(): void
    {
        $keys = [
            'branding.general', 'branding.header_setting', 'branding.header_links',
            'branding.social_links', 'branding.footer_info',
            'branding.footer_col_one', 'branding.footer_col_two', 'branding.legal_pages',
        ];
        foreach ($keys as $k) {
            Cache::forget($k);
        }
    }
}
