<?php

namespace Database\Seeders;

use App\Models\Footer;
use App\Models\FooterColumnOne;
use App\Models\FooterColumnTwo;
use App\Models\GeneralSetting;
use App\Models\HeaderNavigationLink;
use App\Models\HeaderSetting;
use App\Models\SocialLink;
use App\Models\TopBar;
use App\Services\GeneralSettingService;
use Illuminate\Database\Seeder;

class InitialConfigurationSeeder extends Seeder
{
    public function run(): void
    {
        GeneralSetting::updateOrCreate(
            ['id' => 1],
            [
                'site_name' => 'LMSL13',
                'site_slogan' => 'Cursos Online',
                'copyright' => '© '.date('Y').' LMSL13. Todos los derechos reservados.',
                'mail_mailer' => 'log',
                'mail_from_address' => 'info@lmsl13.test',
                'mail_from_name' => 'LMSL13',
            ]
        );

        TopBar::updateOrCreate(
            ['id' => 1],
            [
                'email' => 'info@lmsl13.test',
                'phone' => '+591 7000 0000',
                'offer_text' => 'Inscripciones abiertas para nuevos cursos',
                'offer_url' => '/courses',
                'background_color' => '#111827',
                'text_color' => '#E5E7EB',
                'is_active' => true,
            ]
        );

        HeaderSetting::updateOrCreate(
            ['id' => 1],
            [
                'category_button_text' => 'Categorías',
                'category_limit' => 6,
                'show_search' => true,
                'search_placeholder' => 'Buscar cursos...',
            ]
        );

        Footer::updateOrCreate(
            ['id' => 1],
            [
                'description' => 'Plataforma de cursos online con los mejores instructores de Bolivia y Latinoamérica.',
                'contact_title' => 'Contacto',
                'email' => 'info@lmsl13.test',
                'phone' => '+591 7000 0000',
                'address' => 'La Paz, Bolivia',
                'bottom_text' => 'Hecho con amor en Bolivia',
                'is_active' => true,
            ]
        );

        foreach ([
            ['title' => 'Inicio', 'url' => '/', 'sort_order' => 1],
            ['title' => 'Cursos', 'url' => '/courses', 'sort_order' => 2],
            ['title' => 'Blog', 'url' => '/blog', 'sort_order' => 3],
            ['title' => 'Nosotros', 'url' => '/about', 'sort_order' => 4],
            ['title' => 'Contacto', 'url' => '/contact', 'sort_order' => 5],
        ] as $item) {
            HeaderNavigationLink::updateOrCreate(
                ['title' => $item['title']],
                $item + ['is_active' => true, 'open_in_new_tab' => false]
            );
        }

        foreach ([
            ['title' => 'Sobre Nosotros', 'url' => '/about', 'sort_order' => 1],
            ['title' => 'Todos los Cursos', 'url' => '/courses', 'sort_order' => 2],
            ['title' => 'Blog', 'url' => '/blog', 'sort_order' => 4],
        ] as $item) {
            FooterColumnOne::updateOrCreate(
                ['title' => $item['title']],
                $item + ['is_active' => true]
            );
        }

        foreach ([
            ['title' => 'Centro de Ayuda', 'url' => '#', 'sort_order' => 1],
            ['title' => 'Términos y Condiciones', 'url' => '#', 'sort_order' => 2],
            ['title' => 'Política de Privacidad', 'url' => '#', 'sort_order' => 3],
            ['title' => 'Contacto', 'url' => '/contact', 'sort_order' => 4],
        ] as $item) {
            FooterColumnTwo::updateOrCreate(
                ['title' => $item['title']],
                $item + ['is_active' => true]
            );
        }

        foreach ([
            ['name' => 'Facebook', 'icon_class' => 'fa-brands fa-facebook-f', 'url' => '#', 'sort_order' => 1],
            ['name' => 'X', 'icon_class' => 'fa-brands fa-x-twitter', 'url' => '#', 'sort_order' => 2],
            ['name' => 'LinkedIn', 'icon_class' => 'fa-brands fa-linkedin-in', 'url' => '#', 'sort_order' => 3],
            ['name' => 'YouTube', 'icon_class' => 'fa-brands fa-youtube', 'url' => '#', 'sort_order' => 4],
        ] as $item) {
            SocialLink::updateOrCreate(
                ['name' => $item['name']],
                $item + ['is_active' => true]
            );
        }

        GeneralSettingService::clearAppearanceCache();
    }
}
