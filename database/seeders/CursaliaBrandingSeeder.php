<?php

namespace Database\Seeders;

use App\Models\CustomPage;
use App\Models\Footer;
use App\Models\FooterColumnOne;
use App\Models\FooterColumnTwo;
use App\Models\GeneralSetting;
use App\Models\HeaderNavigationLink;
use App\Models\HeaderSetting;
use App\Models\SocialLink;
use Illuminate\Database\Seeder;

/**
 * Datos de marca Cursalia por defecto + branding white-label.
 *
 * Esto deja TODO editable desde admin: el sitio funciona con estos valores
 * pero el admin puede cambiarlos en cualquier momento. Si vuelves a correr
 * este seeder NO se pisan tus cambios (usa updateOrCreate y first-or-create).
 */
class CursaliaBrandingSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedGeneralSettings();
        $this->seedHeaderSettings();
        $this->seedHeaderLinks();
        $this->seedSocialLinks();
        $this->seedFooter();
        $this->seedFooterColumns();
        $this->seedLegalPages();

        $this->command->info('  ✓ general_settings: marca Cursalia + paleta verde');
        $this->command->info('  ✓ header_navigation_links: '.HeaderNavigationLink::count());
        $this->command->info('  ✓ social_links: '.SocialLink::count());
        $this->command->info('  ✓ footer + '.FooterColumnOne::count().' / '.FooterColumnTwo::count().' columnas');
        $this->command->info('  ✓ legales (custom_pages): '.CustomPage::where('slug','like','legal%')->count().' o más');
    }

    // ──────────────────────────────────────────────────────────────────────

    private function seedGeneralSettings(): void
    {
        $row = GeneralSetting::firstOrNew(['id' => 1]);
        $row->fill([
            'site_name'    => $row->site_name ?: 'Cursalia',
            'site_slogan'  => $row->site_slogan ?: 'Aprende algo nuevo, a tu manera',
            'copyright'    => $row->copyright ?: '© '.date('Y').' Cursalia. Todos los derechos reservados.',
            // Marca Cursalia
            'brand_color'   => $row->brand_color   ?: '#10B981',
            'accent_color'  => $row->accent_color  ?: '#FB7185',
            'sun_color'     => $row->sun_color     ?: '#FBBF24',
            'ink_color'     => $row->ink_color     ?: '#1F2933',
            'font_display'  => $row->font_display  ?: 'Poppins',
            'font_body'     => $row->font_body     ?: 'Inter',
            'theme_preset'  => $row->theme_preset  ?: 'cursalia-green',
            'default_locale'=> $row->default_locale?: 'es',
            'seo_default_description' => $row->seo_default_description
                ?: 'Cursalia es una academia online cálida y cercana: cursos prácticos creados por mentores reales, a tu ritmo, con certificado al terminar.',
            'enabled_sections' => $row->enabled_sections ?: array_keys(GeneralSetting::HOME_SECTIONS),
        ]);
        $row->save();
    }

    private function seedHeaderSettings(): void
    {
        HeaderSetting::updateOrCreate(['id' => 1], [
            'category_button_text' => 'Categorías',
            'category_limit'       => 8,
            'show_search'          => true,
            'search_placeholder'   => '¿Qué quieres aprender hoy?',
        ]);
    }

    private function seedHeaderLinks(): void
    {
        $links = [
            ['title' => 'Cursos',     'url' => '/courses',   'sort_order' => 1],
            ['title' => 'Plantillas', 'url' => '/templates', 'sort_order' => 2],
            ['title' => 'Nosotros',   'url' => '/about',     'sort_order' => 3],
            ['title' => 'Blog',       'url' => '/blog',      'sort_order' => 4],
            ['title' => 'Contacto',   'url' => '/contact',   'sort_order' => 5],
        ];
        foreach ($links as $l) {
            HeaderNavigationLink::updateOrCreate(
                ['title' => $l['title']],
                $l + ['is_active' => true, 'open_in_new_tab' => false]
            );
        }
    }

    private function seedSocialLinks(): void
    {
        $socials = [
            ['name' => 'Facebook',  'icon_class' => 'fa-brands fa-facebook-f', 'url' => 'https://facebook.com/cursalia',  'sort_order' => 1],
            ['name' => 'X (Twitter)','icon_class' => 'fa-brands fa-x-twitter', 'url' => 'https://x.com/cursalia',         'sort_order' => 2],
            ['name' => 'Instagram', 'icon_class' => 'fa-brands fa-instagram',  'url' => 'https://instagram.com/cursalia', 'sort_order' => 3],
            ['name' => 'YouTube',   'icon_class' => 'fa-brands fa-youtube',    'url' => 'https://youtube.com/@cursalia',  'sort_order' => 4],
            ['name' => 'LinkedIn',  'icon_class' => 'fa-brands fa-linkedin-in','url' => 'https://linkedin.com/company/cursalia','sort_order' => 5],
        ];
        foreach ($socials as $s) {
            SocialLink::updateOrCreate(
                ['name' => $s['name']],
                $s + ['is_active' => true]
            );
        }
    }

    private function seedFooter(): void
    {
        Footer::updateOrCreate(['id' => 1], [
            'description'   => 'Aprende algo nuevo, a tu manera. Cursos prácticos creados por mentores reales.',
            'contact_title' => 'Contacto',
            'email'         => 'hola@cursalia.com',
            'phone'         => null,
            'address'       => 'Madrid, España',
            'bottom_text'   => 'Hecho con ❤️ y Laravel 13',
            'is_active'     => true,
        ]);
    }

    private function seedFooterColumns(): void
    {
        // Columna 1: Explorar
        $col1 = [
            ['title' => 'Todos los cursos', 'url' => '/courses',                'sort_order' => 1],
            ['title' => 'Cursos gratis',    'url' => '/courses?price=free',     'sort_order' => 2],
            ['title' => 'Plantillas',       'url' => '/templates',              'sort_order' => 3],
            ['title' => 'Blog',             'url' => '/blog',                   'sort_order' => 4],
            ['title' => 'Crear cuenta',     'url' => '/register',               'sort_order' => 5],
        ];
        foreach ($col1 as $l) {
            FooterColumnOne::updateOrCreate(['title' => $l['title']], $l + ['is_active' => true]);
        }

        // Columna 2: Empresa
        $col2 = [
            ['title' => 'Sobre nosotros',  'url' => '/about',    'sort_order' => 1],
            ['title' => 'Contacto',        'url' => '/contact',  'sort_order' => 2],
            ['title' => 'Ser instructor',  'url' => '/register', 'sort_order' => 3],
        ];
        foreach ($col2 as $l) {
            FooterColumnTwo::updateOrCreate(['title' => $l['title']], $l + ['is_active' => true]);
        }
    }

    /**
     * Las 4 páginas legales pasan a vivir en custom_pages → editables desde
     * admin con WYSIWYG. Su contenido inicial sale de LegalPageController.
     */
    private function seedLegalPages(): void
    {
        $controller = new \App\Http\Controllers\Frontend\LegalPageController;
        $reflection = new \ReflectionClass($controller);

        $pages = [
            ['slug' => 'legal/privacy',       'title' => 'Política de privacidad',  'summary' => 'Cómo recopilamos, usamos y protegemos tus datos personales en Cursalia.', 'method' => 'privacyBody'],
            ['slug' => 'legal/terms',         'title' => 'Términos y condiciones',   'summary' => 'Las reglas básicas para usar Cursalia como estudiante o instructor.', 'method' => 'termsBody'],
            ['slug' => 'legal/data-deletion', 'title' => 'Eliminación de datos',     'summary' => 'Cómo puedes eliminar tu cuenta y todos tus datos personales de Cursalia.', 'method' => 'dataDeletionBody'],
            ['slug' => 'legal/refunds',       'title' => 'Política de reembolsos',   'summary' => 'Condiciones para solicitar el reembolso de un curso adquirido en Cursalia.', 'method' => 'refundsBody'],
        ];

        foreach ($pages as $p) {
            // Llamamos al método protegido por reflexión para reutilizar el HTML
            try {
                $method = $reflection->getMethod($p['method']);
                $method->setAccessible(true);
                $body = $method->invoke($controller);
            } catch (\Throwable) {
                $body = '<p>Contenido pendiente de redactar.</p>';
            }

            CustomPage::updateOrCreate(
                ['slug' => $p['slug']],
                [
                    'title'           => $p['title'],
                    'description'     => $body,
                    'seo_title'       => $p['title'].' · Cursalia',
                    'seo_description' => $p['summary'],
                    'show_at_nav'     => false,
                    'status'          => true,
                ]
            );
        }
    }
}
