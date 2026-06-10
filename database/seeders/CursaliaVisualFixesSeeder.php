<?php

namespace Database\Seeders;

use App\Models\Blog;
use App\Models\Brand;
use App\Models\Course;
use App\Models\CourseLanguage;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Seeder de "arreglos visuales": genera SVGs placeholder reales para los
 * thumbnails de cursos, posts del blog y logos de marcas; corrige los nombres
 * de idiomas (Español/Inglés) y reparte los cursos entre los instructores
 * para que no todos queden asignados al "Instructor Demo".
 */
class CursaliaVisualFixesSeeder extends Seeder
{
    /** Paletas Cursalia para los SVGs */
    private array $palettes = [
        ['#10B981', '#059669', '#fbbf24'], // brand → sun
        ['#FB7185', '#E11D48', '#fde68a'], // coral → sun
        ['#FBBF24', '#F59E0B', '#a7f3d0'], // sun → brand
        ['#10B981', '#3E6CF6', '#fb7185'], // brand → azure → coral
        ['#FB7185', '#10B981', '#fbbf24'], // coral → brand → sun
        ['#059669', '#0F766E', '#fde68a'], // brand-700 → teal
    ];

    public function run(): void
    {
        Storage::disk('public')->makeDirectory('course');
        Storage::disk('public')->makeDirectory('blog');
        Storage::disk('public')->makeDirectory('brand');

        // ── Cursos: SVG placeholder + reparto de instructores ──────────────────
        $instructors = User::where('role', 'instructor')
            ->where('approve_status', 'approved')
            ->orderBy('id')
            ->pluck('id')
            ->all();

        Course::orderBy('id')->get()->each(function (Course $course, int $i) use ($instructors) {
            $palette = $this->palettes[$i % count($this->palettes)];
            $svg = $this->buildSvg(
                title: $course->title,
                subtitle: optional($course->category)->name ?: 'Curso',
                palette: $palette,
                icon: $this->iconForCategory(optional($course->category)->slug ?? ''),
            );
            $path = 'course/'.$course->slug.'.svg';
            Storage::disk('public')->put($path, $svg);

            // Repartir instructores: si hay >=4, hacemos round-robin saltando el "Instructor Demo" (id=1).
            $assigned = $instructors[$i % count($instructors)] ?? $course->instructor_id;

            $course->thumbnail = $path;
            $course->instructor_id = $assigned;
            $course->save();
        });

        // ── Blogs: SVG placeholder ────────────────────────────────────────────
        Blog::orderBy('id')->get()->each(function (Blog $blog, int $i) {
            $palette = $this->palettes[($i + 2) % count($this->palettes)];
            $svg = $this->buildSvg(
                title: $blog->title,
                subtitle: optional($blog->category)->name ?: 'Artículo',
                palette: $palette,
                icon: 'pen',
            );
            $path = 'blog/'.$blog->slug.'.svg';
            Storage::disk('public')->put($path, $svg);
            $blog->thumbnail = $path;
            $blog->save();
        });

        // ── Brands: SVG con tipografía bonita ──────────────────────────────────
        Brand::orderBy('id')->get()->each(function (Brand $brand) {
            $svg = $this->buildBrandSvg($brand->name);
            $path = 'brand/'.Str::slug($brand->name).'.svg';
            Storage::disk('public')->put($path, $svg);
            $brand->logo = $path;
            $brand->save();
        });

        // ── Idiomas: corregir tildes (Espanol → Español, Ingles → Inglés) ────
        CourseLanguage::where('name', 'Espanol')->update(['name' => 'Español', 'slug' => 'espanol']);
        CourseLanguage::where('name', 'Ingles')->update(['name' => 'Inglés',  'slug' => 'ingles']);

        $this->command->info('  ✓ '.Course::count().' SVGs de cursos');
        $this->command->info('  ✓ '.Blog::count().' SVGs de blog');
        $this->command->info('  ✓ '.Brand::count().' SVGs de brands');
        $this->command->info('  ✓ Idiomas con tildes corregidas');
        $this->command->info('  ✓ Cursos repartidos entre '.count($instructors).' instructores');
    }

    /** SVG placeholder bonito con gradient + iniciales + icono + categoría */
    private function buildSvg(string $title, string $subtitle, array $palette, string $icon): string
    {
        [$c1, $c2, $accent] = $palette;
        $initials = $this->initials($title);
        $iconSvg = $this->iconSvg($icon);

        // Coordenadas seguras para texto
        $titleSafe = htmlspecialchars(Str::limit($title, 32), ENT_QUOTES, 'UTF-8');
        $subtitleSafe = htmlspecialchars($subtitle, ENT_QUOTES, 'UTF-8');

        return <<<SVG
<?xml version="1.0" encoding="UTF-8"?>
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 600 400" preserveAspectRatio="xMidYMid slice">
  <defs>
    <linearGradient id="g" x1="0" y1="0" x2="1" y2="1">
      <stop offset="0%" stop-color="$c1"/>
      <stop offset="100%" stop-color="$c2"/>
    </linearGradient>
    <radialGradient id="r" cx="0.8" cy="0.2" r="0.7">
      <stop offset="0%" stop-color="$accent" stop-opacity="0.55"/>
      <stop offset="100%" stop-color="$accent" stop-opacity="0"/>
    </radialGradient>
    <radialGradient id="r2" cx="0.1" cy="0.9" r="0.6">
      <stop offset="0%" stop-color="#ffffff" stop-opacity="0.25"/>
      <stop offset="100%" stop-color="#ffffff" stop-opacity="0"/>
    </radialGradient>
    <style>
      .t { font-family: 'Poppins', 'Inter', system-ui, sans-serif; fill: #ffffff; }
      .l { font-family: 'Inter', sans-serif; fill: rgba(255,255,255,0.85); font-weight: 700; letter-spacing: 0.18em; text-transform: uppercase; }
      .ini { font-family: 'Poppins', sans-serif; font-weight: 800; fill: rgba(255,255,255,0.12); }
    </style>
  </defs>
  <rect width="600" height="400" fill="url(#g)"/>
  <rect width="600" height="400" fill="url(#r)"/>
  <rect width="600" height="400" fill="url(#r2)"/>

  <!-- Iniciales gigantes de fondo -->
  <text x="50%" y="78%" text-anchor="middle" class="ini" font-size="240">$initials</text>

  <!-- Icono encima -->
  <g transform="translate(40,40)" fill="none" stroke="#ffffff" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" opacity="0.95">
    $iconSvg
  </g>

  <!-- Etiqueta categoría -->
  <text x="40" y="120" class="l" font-size="13">$subtitleSafe</text>

  <!-- Título del curso -->
  <text x="40" y="160" class="t" font-size="32" font-weight="800">$titleSafe</text>
</svg>
SVG;
    }

    private function buildBrandSvg(string $name): string
    {
        $safe = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');

        return <<<SVG
<?xml version="1.0" encoding="UTF-8"?>
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 240 64">
  <style>
    .b { font-family: 'Poppins', 'Inter', sans-serif; font-weight: 800; fill: #5b5b66; letter-spacing: -0.01em; }
  </style>
  <text x="50%" y="55%" text-anchor="middle" dominant-baseline="middle" class="b" font-size="32">$safe</text>
</svg>
SVG;
    }

    private function initials(string $text): string
    {
        $words = preg_split('/\s+/', trim($text));
        $first = $words[0] ?? '';
        $second = $words[1] ?? '';
        $letters = Str::upper(Str::substr($first, 0, 1).Str::substr($second, 0, 1));

        return $letters ?: 'C';
    }

    private function iconForCategory(string $slug): string
    {
        return match (true) {
            str_contains($slug, 'desarrollo'),
            str_contains($slug, 'web'),
            str_contains($slug, 'programa') => 'code',
            str_contains($slug, 'diseno'),
            str_contains($slug, 'ux'),
            str_contains($slug, 'ui') => 'palette',
            str_contains($slug, 'marketing'),
            str_contains($slug, 'redes') => 'megaphone',
            str_contains($slug, 'fotografia'),
            str_contains($slug, 'foto') => 'camera',
            str_contains($slug, 'negocios'),
            str_contains($slug, 'business') => 'briefcase',
            default => 'book',
        };
    }

    private function iconSvg(string $name): string
    {
        return match ($name) {
            'code' => '<polyline points="14,4 22,16 14,28"/><polyline points="10,4 2,16 10,28"/>',
            'palette' => '<circle cx="16" cy="16" r="14"/><circle cx="11" cy="11" r="1.6" fill="#fff" stroke="none"/><circle cx="20" cy="9" r="1.6" fill="#fff" stroke="none"/><circle cx="24" cy="16" r="1.6" fill="#fff" stroke="none"/>',
            'megaphone' => '<polygon points="4,12 22,4 22,28 4,20"/><line x1="22" y1="10" x2="28" y2="10"/><line x1="22" y1="18" x2="28" y2="18"/>',
            'camera' => '<rect x="2" y="8" width="28" height="20" rx="3"/><circle cx="16" cy="18" r="6"/><rect x="11" y="4" width="10" height="6" rx="1"/>',
            'briefcase' => '<rect x="2" y="9" width="28" height="20" rx="2"/><path d="M11 9V5h10v4"/><line x1="2" y1="17" x2="30" y2="17"/>',
            'pen' => '<path d="M4 28l6-2 18-18-4-4-18 18-2 6z"/><line x1="18" y1="8" x2="24" y2="14"/>',
            default => '<path d="M4 6h18a4 4 0 014 4v20H8a4 4 0 01-4-4V6z"/><path d="M4 6a2 2 0 012-2h16a2 2 0 012 2"/>',
        };
    }
}
