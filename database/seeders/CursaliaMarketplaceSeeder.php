<?php

namespace Database\Seeders;

use App\Models\Template;
use App\Models\TemplateCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Datos demo del Marketplace de plantillas: 4 categorías + 6 productos
 * Cursalia con SVGs placeholders bonitos.
 */
class CursaliaMarketplaceSeeder extends Seeder
{
    public function run(): void
    {
        Storage::disk('public')->makeDirectory('templates');

        $cats = [
            ['LMS Themes',                 'lms-themes',        'fa-solid fa-graduation-cap', '#10B981', 'Plantillas Cursalia para academias online completas.'],
            ['Paletas y Componentes',      'paletas-componentes','fa-solid fa-palette',       '#FB7185', 'Packs de colores, headers, footers y componentes Tailwind.'],
            ['Plantillas de Contenido',    'contenido',         'fa-solid fa-pen-fancy',      '#FBBF24', 'Títulos de cursos, plantillas de emails, copy listos para usar.'],
            ['Packs Multi-tema',           'packs',             'fa-solid fa-boxes-stacked',  '#3E6CF6', 'Bundles con varias plantillas hermanas Cursalia con descuento.'],
        ];

        $catMap = [];
        foreach ($cats as $i => [$name, $slug, $icon, $color, $desc]) {
            $cat = TemplateCategory::updateOrCreate(
                ['slug' => $slug],
                [
                    'name'        => $name,
                    'description' => $desc,
                    'icon'        => $icon,
                    'color'       => $color,
                    'sort_order'  => $i + 1,
                    'is_active'   => true,
                ]
            );
            $catMap[$slug] = $cat->id;
        }

        $templates = [
            [
                'cat'      => 'lms-themes',
                'title'    => 'Cursalia FREE',
                'headline' => 'El LMS Cursalia completo · gratis para empezar tu academia online',
                'price'    => 0,
                'is_free'  => true,
                'featured' => true,
                'version'  => '1.0.0',
                'demo'     => 'https://cursalia.test',
                'download' => 'https://github.com/voce23/cursalia/releases/latest',
                'tech'     => ['Laravel 13', 'Tailwind CSS 4', 'Alpine.js', 'PHP 8.3', 'MySQL'],
                'features' => [
                    'Catálogo completo de cursos con filtros y búsqueda',
                    'Auth de estudiantes e instructores (login/register/forgot)',
                    'Inscripción gratuita y panel del alumno',
                    'Blog con comentarios moderados',
                    'Páginas Nosotros, Contacto y Legales editables',
                    'Panel admin con CRUD de categorías y CMS',
                    'White-label: 5 paletas + tipografías + logos sin tocar código',
                    'Menú de navegación dinámico con drag-to-reorder',
                    'SEO completo (sitemap, OpenGraph, Twitter Cards, JSON-LD)',
                ],
                'desc'     => '<p>La versión gratuita y descargable de <strong>Cursalia</strong>: un LMS profesional construido sobre Laravel 13 con todo lo que necesitas para lanzar tu academia online el mismo día.</p><p>Es 100% white-label: cambia colores, logo, tipografía, menú y textos desde el admin <strong>sin tocar una sola línea de código</strong>. Perfecta como punto de partida para tu academia o como base para construir otras plantillas hermanas.</p>',
                'palette'  => ['#10B981', '#34D399'],
            ],
            [
                'cat'      => 'lms-themes',
                'title'    => 'Cursalia PRO',
                'headline' => 'Cobra por tus cursos · Marketplace multi-instructor · Soporte premium',
                'price'    => 97.00,
                'discount' => 47.00, // oferta de lanzamiento
                'is_free'  => false,
                'featured' => true,
                'version'  => '0.9.0-beta',
                'demo'     => null,
                'tech'     => ['Laravel 13', 'Tailwind CSS 4', 'Alpine.js', 'Stripe', 'PayPal'],
                'features' => [
                    'Todo lo que incluye Cursalia FREE',
                    'Pagos con Stripe y PayPal',
                    'Carrito de compra y cupones',
                    'Marketplace multi-instructor con comisiones automáticas',
                    'Certificados PDF al completar un curso',
                    'Correos transaccionales con SMTP (bienvenida, compra, recordatorios)',
                    'Sistema de actualizaciones in-app (estilo SOAPS)',
                    'Soporte prioritario por correo',
                ],
                'desc'     => '<p><strong>Cursalia PRO</strong> es la evolución para quienes quieren convertir su academia en un negocio. Incluye todo lo de la versión gratis más la capa completa de cobro, marketplace y soporte.</p><p>🎁 <strong>Oferta de lanzamiento:</strong> los primeros 100 compradores pagan $47 (después $97).</p>',
                'palette'  => ['#10B981', '#3E6CF6'],
            ],
            [
                'cat'      => 'paletas-componentes',
                'title'    => 'Pack 10 Paletas Cursalia',
                'headline' => 'Diez paletas profesionales listas para aplicar en tu admin con 1 click',
                'price'    => 19.00,
                'is_free'  => false,
                'featured' => false,
                'version'  => '1.0.0',
                'tech'     => ['CSS Variables', 'Tailwind 4', 'JSON'],
                'features' => [
                    'Verde Esmeralda · Morado Cursalia · Coral Cálido',
                    'Azul Confianza · Sol Otoño · Negro Minimalista',
                    'Rosa Pastel · Verde Bosque · Marino Profundo · Mostaza Suave',
                    'Cada paleta con 4 colores: principal, acento, sol, texto',
                    'Importable desde admin → Apariencia con 1 click',
                ],
                'desc'     => '<p>Diez paletas cuidadosamente seleccionadas para academias online, tiendas de plantillas, portafolios y blogs. Compatibles con Cursalia FREE y PRO.</p>',
                'palette'  => ['#FB7185', '#FBBF24'],
            ],
            [
                'cat'      => 'paletas-componentes',
                'title'    => 'Header + Footer Pack Tailwind',
                'headline' => 'Doce headers y diez footers responsive listos para copiar y pegar',
                'price'    => 9.00,
                'is_free'  => false,
                'featured' => false,
                'version'  => '1.2.0',
                'tech'     => ['Tailwind CSS 4', 'Alpine.js', 'HTML'],
                'features' => [
                    '12 headers: pill flotante, glass oscuro, mega-menú, sticky compact',
                    '10 footers: 5 columnas, minimalista, con CTA, con mapa',
                    'Todos responsive y accesibles (foco visible, aria-labels)',
                    'Variantes Light y Dark mode',
                ],
                'desc'     => '<p>22 componentes header + footer en Tailwind 4 listos para integrar en cualquier proyecto Laravel o estático.</p>',
                'palette'  => ['#FB7185', '#10B981'],
            ],
            [
                'cat'      => 'contenido',
                'title'    => 'Pack 200 Títulos de Cursos',
                'headline' => 'Doscientos títulos probados para llenar tu academia el mismo día',
                'price'    => 9.00,
                'is_free'  => false,
                'featured' => false,
                'version'  => '2026.06',
                'tech'     => ['CSV', 'JSON', 'SQL seeder'],
                'features' => [
                    '200 títulos en español (programación, diseño, marketing, idiomas)',
                    '100 descripciones SEO de 155 caracteres',
                    '50 temarios de ejemplo (5-10 lecciones cada uno)',
                    'Importable como seeder de Laravel',
                ],
                'desc'     => '<p>El pack que arranca tu catálogo. Importa este SQL en tu Cursalia y ya tienes 200 cursos para mostrar a tus primeros visitantes.</p>',
                'palette'  => ['#FBBF24', '#FB7185'],
            ],
            [
                'cat'      => 'packs',
                'title'    => 'Cursalia Library Bundle',
                'headline' => 'Todo: PRO + Paletas + Components + Contenido a mitad de precio',
                'price'    => 199.00,
                'discount' => 99.00,
                'is_free'  => false,
                'featured' => true,
                'version'  => '2026.06',
                'tech'     => ['Laravel 13', 'Tailwind 4', 'Alpine.js', 'Stripe', 'PayPal'],
                'features' => [
                    'Cursalia PRO completo',
                    'Pack 10 Paletas',
                    'Header + Footer Pack',
                    '200 Títulos + 100 SEO descriptions',
                    'Actualizaciones de por vida',
                    'Soporte prioritario premium 12 meses',
                ],
                'desc'     => '<p>El bundle completo de Cursalia. Si vas a comprar más de un producto, este pack te ahorra ~50%.</p>',
                'palette'  => ['#3E6CF6', '#10B981'],
            ],
        ];

        foreach ($templates as $i => $t) {
            $slug = Str::slug($t['title']);
            $thumbPath = 'templates/'.$slug.'.svg';
            Storage::disk('public')->put($thumbPath, $this->buildSvg($t['title'], $t['headline'], $t['palette']));

            Template::updateOrCreate(
                ['slug' => $slug],
                [
                    'template_category_id' => $catMap[$t['cat']],
                    'title'        => $t['title'],
                    'headline'     => $t['headline'],
                    'description'  => $t['desc'],
                    'thumbnail'    => $thumbPath,
                    'price'        => $t['price'],
                    'discount'     => $t['discount'] ?? null,
                    'is_free'      => $t['is_free'],
                    'demo_url'     => $t['demo']     ?? null,
                    'download_url' => $t['download'] ?? null,
                    'version'      => $t['version'],
                    'tech_stack'   => $t['tech'],
                    'features'     => $t['features'],
                    'status'       => 'published',
                    'is_featured'  => $t['featured'],
                    'sort_order'   => $i + 1,
                ]
            );
        }

        $this->command->info('  ✓ '.TemplateCategory::count().' categorías de plantillas');
        $this->command->info('  ✓ '.Template::count().' plantillas publicadas');
    }

    private function buildSvg(string $title, string $subtitle, array $palette): string
    {
        [$c1, $c2] = $palette;
        $initials = (string) collect(preg_split('/\s+/', $title))
            ->take(2)
            ->map(fn ($w) => Str::upper(Str::substr($w, 0, 1)))
            ->implode('');
        $titleSafe = htmlspecialchars(Str::limit($title, 28), ENT_QUOTES, 'UTF-8');
        $subtitleSafe = htmlspecialchars(Str::limit($subtitle, 60), ENT_QUOTES, 'UTF-8');

        return <<<SVG
<?xml version="1.0" encoding="UTF-8"?>
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 800 500" preserveAspectRatio="xMidYMid slice">
  <defs>
    <linearGradient id="g" x1="0" y1="0" x2="1" y2="1">
      <stop offset="0%" stop-color="$c1"/>
      <stop offset="100%" stop-color="$c2"/>
    </linearGradient>
    <radialGradient id="r" cx="0.85" cy="0.15" r="0.8">
      <stop offset="0%" stop-color="#FFFFFF" stop-opacity="0.32"/>
      <stop offset="100%" stop-color="#FFFFFF" stop-opacity="0"/>
    </radialGradient>
    <style>
      .t { font-family: 'Poppins','Inter',sans-serif; fill: #ffffff; }
      .ini { font-family: 'Poppins',sans-serif; font-weight: 800; fill: rgba(255,255,255,0.12); }
      .badge { font-family: 'Inter',sans-serif; font-weight: 700; letter-spacing: 0.16em; text-transform: uppercase; fill: rgba(255,255,255,0.85); }
    </style>
  </defs>
  <rect width="800" height="500" fill="url(#g)"/>
  <rect width="800" height="500" fill="url(#r)"/>
  <text x="50%" y="86%" text-anchor="middle" class="ini" font-size="340">$initials</text>
  <g transform="translate(50,60)">
    <rect width="84" height="32" rx="16" fill="rgba(255,255,255,0.18)"/>
    <text x="42" y="22" text-anchor="middle" class="badge" font-size="11">PLANTILLA</text>
  </g>
  <text x="50" y="200" class="t" font-size="48" font-weight="800">$titleSafe</text>
  <text x="50" y="245" class="t" font-size="22" opacity="0.85">$subtitleSafe</text>
</svg>
SVG;
    }
}
