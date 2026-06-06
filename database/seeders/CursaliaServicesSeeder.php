<?php

namespace Database\Seeders;

use App\Models\GeneralSetting;
use App\Models\Service;
use Illuminate\Database\Seeder;

/**
 * Servicios y asesoría de Cursalia. Modelo freemium honesto:
 * el producto (Cursalia FREE) es gratis pero el TIEMPO de los humanos
 * se cobra. Cuatro niveles claros que el admin puede editar.
 */
class CursaliaServicesSeeder extends Seeder
{
    public function run(): void
    {
        $s = GeneralSetting::firstOrCreate(['id' => 1]);
        $s->whatsapp_number          ??= '34600000000';
        $s->whatsapp_default_message ??= 'Hola Cursalia, vi su web y quería preguntar por…';
        $s->services_email           ??= 'servicios@cursalia.com';
        $s->save();

        $services = [
            [
                'slug'  => 'documentacion',
                'title' => 'Documentación + Comunidad',
                'headline' => 'Para quien sabe programar y solo necesita los docs',
                'description' => '<p>Acceso completo a la documentación oficial de Cursalia FREE, ejemplos y comunidad en GitHub Discussions. Suficiente si tienes experiencia con Laravel.</p>',
                'icon'  => 'fa-solid fa-book-open',
                'color' => '#10B981',
                'price' => 0,
                'currency' => 'USD',
                'price_suffix' => 'gratis',
                'is_free' => true,
                'features' => [
                    'Documentación técnica completa',
                    'Guía paso a paso de instalación',
                    'Acceso a GitHub Discussions',
                    'Cambios y release notes públicos',
                ],
                'badge_text' => null,
                'cta_text' => 'Ir a la documentación',
                'cta_url'  => 'https://github.com/voce23/cursalia',
                'is_featured' => false,
                'sort_order' => 1,
            ],
            [
                'slug'  => 'asesoria',
                'title' => 'Asesoría 1 hora',
                'headline' => 'Resuelve tus dudas en una llamada por Zoom o WhatsApp',
                'description' => '<p>Una hora cara a cara con nuestro equipo para resolver dudas concretas: arquitectura, despliegue, personalización o estrategia de tu academia.</p><p>Ideal cuando estás atascado y necesitas hablar con alguien que ya pasó por ahí.</p>',
                'icon'  => 'fa-solid fa-headset',
                'color' => '#FBBF24',
                'price' => 29.00,
                'currency' => 'USD',
                'price_suffix' => 'por sesión',
                'is_free' => false,
                'features' => [
                    'Sesión 1 hora por Zoom o WhatsApp',
                    'Auditoría rápida de tu situación',
                    'Recomendaciones técnicas accionables',
                    'Grabación del encuentro para que la revises',
                    'Resumen por correo con próximos pasos',
                ],
                'badge_text' => 'Popular',
                'cta_text' => 'Reservar asesoría',
                'cta_url'  => null,
                'is_featured' => true,
                'sort_order' => 2,
            ],
            [
                'slug'  => 'instalacion',
                'title' => 'Instalación llave en mano',
                'headline' => 'Te entregamos Cursalia FREE instalado y configurado en TU dominio',
                'description' => '<p>Olvídate de la parte técnica. Te entregamos Cursalia FREE corriendo en tu dominio, configurado, con SSL, base de datos lista y panel admin con tu primer usuario.</p><p>Solo tienes que empezar a subir tus cursos.</p>',
                'icon'  => 'fa-solid fa-rocket',
                'color' => '#FB7185',
                'price' => 97.00,
                'currency' => 'USD',
                'price_suffix' => 'pago único',
                'is_free' => false,
                'features' => [
                    'Compra del dominio gestionada (no incluida en el precio)',
                    'Hosting compartido configurado',
                    'Instalación + DB + storage:link + cron',
                    'Certificado SSL https://',
                    'Configuración de correo SMTP',
                    'Sesión de 30 min de onboarding',
                    'Garantía de instalación 7 días',
                ],
                'badge_text' => 'Recomendado',
                'cta_text' => 'Solicitar instalación',
                'cta_url'  => null,
                'is_featured' => true,
                'sort_order' => 3,
            ],
            [
                'slug'  => 'personalizacion',
                'title' => 'Personalización completa',
                'headline' => 'Tu marca, tu paleta, tu logo, tus textos y todo lo necesario para arrancar',
                'description' => '<p>El paquete completo: instalación + diseño de identidad visual + carga del contenido inicial. Te entregamos un sitio listo para vender, con tu marca aplicada en cada rincón.</p>',
                'icon'  => 'fa-solid fa-wand-magic-sparkles',
                'color' => '#3E6CF6',
                'price' => 297.00,
                'currency' => 'USD',
                'price_suffix' => 'pago único',
                'is_free' => false,
                'features' => [
                    'Todo lo de "Instalación llave en mano"',
                    'Logo + favicon + colores aplicados',
                    'Selección y configuración de tipografía',
                    'Carga del catálogo inicial (hasta 10 cursos)',
                    'Carga de 5 artículos de blog SEO',
                    '2 rondas de revisiones',
                    'Soporte por correo durante 30 días',
                ],
                'badge_text' => null,
                'cta_text' => 'Solicitar personalización',
                'cta_url'  => null,
                'is_featured' => false,
                'sort_order' => 4,
            ],
        ];

        foreach ($services as $svc) {
            Service::updateOrCreate(['slug' => $svc['slug']], $svc + ['is_active' => true]);
        }

        $this->command->info('  ✓ '.Service::count().' servicios + WhatsApp/email de servicios');
    }
}
