<?php

namespace App\Services;

use App\Models\Footer;
use App\Models\FooterColumnOne;
use App\Models\FooterColumnTwo;
use App\Models\GeneralSetting;
use App\Models\HeaderSetting;
use App\Models\HeaderNavigationLink;
use App\Models\CustomPage;
use App\Models\SocialLink;
use App\Models\TopBar;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Collection;

class GeneralSettingService
{
    private static function rememberSafe(string $key, callable $resolver, callable $isValid): mixed
    {
        $cached = Cache::get($key);

        if ($isValid($cached)) {
            return $cached;
        }

        if ($cached !== null) {
            Cache::forget($key);
        }

        $value = $resolver();

        Cache::forever($key, $value);

        return $value;
    }

    private static function rememberModel(string $key, string $modelClass, callable $resolver): mixed
    {
        return self::rememberSafe(
            $key,
            $resolver,
            static fn (mixed $cached): bool => $cached === null || $cached instanceof $modelClass,
        );
    }

    private static function rememberCollection(string $key, string $modelClass, callable $resolver): Collection
    {
        return self::rememberSafe(
            $key,
            $resolver,
            static fn (mixed $cached): bool => $cached instanceof Collection
                && $cached->every(static fn (mixed $item): bool => $item instanceof $modelClass),
        );
    }

    public static function clearAppearanceCache(): void
    {
        Cache::forget('general_setting');
        Cache::forget('site_top_bar');
        Cache::forget('site_footer');
        Cache::forget('site_social_links');
        Cache::forget('site_header_settings');
        Cache::forget('site_header_navigation_links');
        Cache::forget('site_footer_col_one');
        Cache::forget('site_footer_col_two');
        Cache::forget('site_custom_pages');
    }

    /**
     * Devuelve la única instancia de configuración (cacheada).
     */
    public static function instance(): GeneralSetting
    {
        $cached = Cache::get('general_setting');

        if ($cached instanceof GeneralSetting) {
            return $cached;
        }

        if (is_object($cached) || is_array($cached)) {
            Cache::forget('general_setting');
        }

        $setting = GeneralSetting::firstOrCreate(['id' => 1], [
            'site_name' => 'LMSL13',
        ]);

        Cache::forever('general_setting', $setting);

        return $setting;
    }

    /**
     * Aplica la configuración globalmente:
     *  - sobreescribe config('app.name')
     *  - comparte la variable $generalSetting con todas las vistas
     */
    public static function setGlobal(): void
    {
        $setting = self::instance();

        self::setMailConfig($setting);

        if (! empty($setting->site_name)) {
            config()->set('app.name', $setting->site_name);
        }

        View::share('generalSetting', $setting);

        if (Schema::hasTable('top_bars')) {
            View::share('topBar', self::rememberModel('site_top_bar', TopBar::class, function () {
                return TopBar::query()->first();
            }));
        }

        if (Schema::hasTable('header_settings')) {
            View::share('headerSetting', self::rememberModel('site_header_settings', HeaderSetting::class, function () {
                return HeaderSetting::query()->first();
            }));
        }

        if (Schema::hasTable('footers')) {
            View::share('siteFooter', self::rememberModel('site_footer', Footer::class, function () {
                return Footer::query()->first();
            }));
        }

        if (Schema::hasTable('social_links')) {
            View::share('socialLinks', self::rememberCollection('site_social_links', SocialLink::class, function () {
                return SocialLink::query()
                    ->where('is_active', true)
                    ->orderBy('sort_order')
                    ->get();
            }));
        }

        if (Schema::hasTable('header_navigation_links')) {
            View::share('headerNavigationLinks', self::rememberCollection('site_header_navigation_links', HeaderNavigationLink::class, function () {
                return HeaderNavigationLink::query()
                    ->where('is_active', true)
                    ->orderBy('sort_order')
                    ->get();
            }));
        }

        if (Schema::hasTable('footer_column_ones')) {
            View::share('footerColumnOneLinks', self::rememberCollection('site_footer_col_one', FooterColumnOne::class, function () {
                return FooterColumnOne::query()
                    ->where('is_active', true)
                    ->orderBy('sort_order')
                    ->get();
            }));
        }

        if (Schema::hasTable('footer_column_twos')) {
            View::share('footerColumnTwoLinks', self::rememberCollection('site_footer_col_two', FooterColumnTwo::class, function () {
                return FooterColumnTwo::query()
                    ->where('is_active', true)
                    ->orderBy('sort_order')
                    ->get();
            }));
        }

        if (Schema::hasTable('custom_pages')) {
            View::share('customPages', self::rememberCollection('site_custom_pages', CustomPage::class, function () {
                return CustomPage::query()
                    ->where('status', true)
                    ->where('show_at_nav', true)
                    ->orderBy('title')
                    ->get();
            }));
        }
    }

    public static function setMailConfig(?GeneralSetting $setting = null): void
    {
        $setting ??= self::instance();

        $defaultMailer = $setting->mail_mailer ?: config('mail.default', 'log');

        config()->set('mail.default', $defaultMailer);
        config()->set('mail.from.address', $setting->mail_from_address ?: config('mail.from.address'));
        config()->set('mail.from.name', $setting->mail_from_name ?: $setting->site_name ?: config('mail.from.name'));

        config()->set('mail.mailers.smtp.host', $setting->mail_host ?: config('mail.mailers.smtp.host'));
        config()->set('mail.mailers.smtp.port', $setting->mail_port ?: config('mail.mailers.smtp.port'));
        config()->set('mail.mailers.smtp.username', $setting->mail_username ?: config('mail.mailers.smtp.username'));
        config()->set('mail.mailers.smtp.password', $setting->mail_password ?: config('mail.mailers.smtp.password'));
        config()->set('mail.mailers.smtp.scheme', $setting->mail_scheme ?: null);
    }
}
