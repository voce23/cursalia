<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class GeneralSetting extends Model
{
    protected $fillable = [
        'site_name',
        'site_slogan',
        'logo',
        'favicon',
        'copyright',
        'brand_color', 'accent_color', 'sun_color', 'ink_color',
        'font_display', 'font_body',
        'theme_preset', 'default_locale',
        'seo_default_description', 'og_image',
        'google_site_verification', 'bing_site_verification', 'google_analytics_id',
        'enabled_sections',
        'whatsapp_number',
        'whatsapp_default_message',
        'services_email',
        'mail_mailer',
        'mail_scheme',
        'mail_host',
        'mail_port',
        'mail_username',
        'mail_password',
        'mail_from_address',
        'mail_from_name',
    ];

    // Ocultar en respuestas JSON/array para no exponer credenciales
    protected $hidden = [
        'mail_password',
    ];

    protected $casts = [
        'mail_port'        => 'integer',
        'enabled_sections' => 'array',
    ];

    /** Paletas predefinidas que el admin puede aplicar de un click. */
    public const PRESETS = [
        'cursalia-green' => [
            'label'  => 'Cursalia Verde',
            'brand'  => '#10B981', 'accent' => '#FB7185', 'sun' => '#FBBF24', 'ink' => '#1F2933',
        ],
        'aulacursos-purple' => [
            'label'  => 'AulaCursos Morado',
            'brand'  => '#6741E8', 'accent' => '#3E6CF6', 'sun' => '#FFB45C', 'ink' => '#15152A',
        ],
        'coral-warm' => [
            'label'  => 'Coral Cálido',
            'brand'  => '#FB7185', 'accent' => '#FBBF24', 'sun' => '#10B981', 'ink' => '#1F2933',
        ],
        'azure-trust' => [
            'label'  => 'Azul Confianza',
            'brand'  => '#3E6CF6', 'accent' => '#10B981', 'sun' => '#FBBF24', 'ink' => '#0F172A',
        ],
        'mono-minimal' => [
            'label'  => 'Negro Minimalista',
            'brand'  => '#1F2933', 'accent' => '#FB7185', 'sun' => '#FBBF24', 'ink' => '#0B0B1A',
        ],
    ];

    /** Pares de tipografías disponibles (display + body). */
    public const FONTS = [
        'poppins-inter'  => ['label' => 'Poppins + Inter (Cursalia)',  'display' => 'Poppins',          'body' => 'Inter'],
        'manrope-inter'  => ['label' => 'Manrope + Inter',             'display' => 'Manrope',          'body' => 'Inter'],
        'plus-jakarta'   => ['label' => 'Plus Jakarta + Inter',        'display' => 'Plus Jakarta Sans','body' => 'Inter'],
        'outfit-inter'   => ['label' => 'Outfit + Inter',              'display' => 'Outfit',           'body' => 'Inter'],
        'ibm-plex'       => ['label' => 'IBM Plex Sans (mono pareja)', 'display' => 'IBM Plex Sans',    'body' => 'IBM Plex Sans'],
        'space-grotesk'  => ['label' => 'Space Grotesk + Inter',       'display' => 'Space Grotesk',    'body' => 'Inter'],
    ];

    /** Lista de secciones del home cuyo on/off puede manejar el admin. */
    public const HOME_SECTIONS = [
        'hero'            => 'Hero principal',
        'features'        => 'Cuatro razones',
        'categories'      => 'Categorías (bento)',
        'about'           => 'Sobre nosotros',
        'courses'         => 'Cursos destacados',
        'newsletter'      => 'Newsletter',
        'video'           => 'Sección de video',
        'brands'          => 'Marcas/empresas',
        'instructors'     => 'Instructores destacados',
        'testimonials'    => 'Testimonios',
        'cta_instructor'  => 'CTA "Ser instructor"',
    ];

    public function setMailPasswordAttribute(?string $value): void
    {
        if ($value !== null && $value !== '') {
            $this->attributes['mail_password'] = Crypt::encryptString($value);
        }
    }

    public function getMailPasswordAttribute(?string $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        try {
            return Crypt::decryptString($value);
        } catch (\Exception) {
            return null;
        }
    }
}
