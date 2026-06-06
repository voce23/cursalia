<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\CourseCategory;
use App\Models\Testimonial;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Auditoría exhaustiva: genera avatares SVG para users sin foto, SVGs para
 * categorías sin imagen, limpia rutas huérfanas en todas las tablas con
 * campos image/logo/avatar/thumbnail/document, y enriquece los textos
 * genéricos del seeder original (headlines, bios, "Instructor Demo").
 */
class CursaliaAuditFixesSeeder extends Seeder
{
    /** 8 gradients Cursalia para los avatares circulares */
    private array $gradients = [
        ['#10B981', '#059669'], // brand
        ['#FB7185', '#E11D48'], // coral
        ['#FBBF24', '#F59E0B'], // sun
        ['#10B981', '#3E6CF6'], // brand → azure
        ['#FB7185', '#FBBF24'], // coral → sun
        ['#059669', '#0F766E'], // teal profundo
        ['#34D399', '#FB7185'], // brand pálido → coral
        ['#A78BFA', '#FB7185'], // violeta → coral
    ];

    /** Iconos por slug de categoría (mismo set que VisualFixes) */
    private array $categoryIcons = [
        'desarrollo-web'    => 'code',
        'diseno-digital'    => 'palette',
        'marketing-digital' => 'megaphone',
        'laravel'           => 'code',
        'javascript'        => 'code',
        'ux-ui'             => 'palette',
        'branding'          => 'palette',
        'seo'               => 'magnifier',
        'redes-sociales'    => 'megaphone',
    ];

    public function run(): void
    {
        Storage::disk('public')->makeDirectory('avatars');
        Storage::disk('public')->makeDirectory('category');

        $this->fixUserAvatars();
        $this->fixAdminAvatars();
        $this->fixCategoryImages();
        $this->fixTestimonialAvatars();
        $this->enrichInstructorHeadlines();
        $this->cleanOrphanImageColumns();

        $this->command->info('  ✓ Avatares de usuarios');
        $this->command->info('  ✓ Avatares de admins');
        $this->command->info('  ✓ Imágenes de categorías ('.CourseCategory::whereNotNull('image')->count().')');
        $this->command->info('  ✓ Headlines/bios enriquecidos');
        $this->command->info('  ✓ Rutas huérfanas limpiadas');
    }

    // ──────────────────────────────────────────────────────────────────────
    // Avatares
    // ──────────────────────────────────────────────────────────────────────

    private function fixUserAvatars(): void
    {
        User::orderBy('id')->get()->each(function (User $u, int $i) {
            // Si tiene imagen pero el archivo no existe → la regeneramos.
            // Si no tiene imagen → también la generamos.
            $needsAvatar = ! $u->image || ! file_exists(storage_path('app/public/'.$u->image));
            if (! $needsAvatar) {
                return;
            }
            $gradient = $this->gradients[$i % count($this->gradients)];
            $svg = $this->buildAvatarSvg($u->name, $gradient);
            $path = 'avatars/u_'.$u->id.'.svg';
            Storage::disk('public')->put($path, $svg);
            $u->image = $path;
            $u->save();
        });
    }

    private function fixAdminAvatars(): void
    {
        if (! Schema::hasColumn('admins', 'image')) {
            return;
        }
        Admin::orderBy('id')->get()->each(function (Admin $a, int $i) {
            $needs = ! $a->image || ! file_exists(storage_path('app/public/'.$a->image));
            if (! $needs) {
                return;
            }
            $gradient = $this->gradients[($i + 3) % count($this->gradients)];
            $svg = $this->buildAvatarSvg($a->name, $gradient);
            $path = 'avatars/a_'.$a->id.'.svg';
            Storage::disk('public')->put($path, $svg);
            $a->image = $path;
            $a->save();
        });
    }

    private function fixTestimonialAvatars(): void
    {
        Testimonial::orderBy('id')->get()->each(function (Testimonial $t, int $i) {
            $needs = ! $t->avatar || ! file_exists(storage_path('app/public/'.$t->avatar));
            if (! $needs) {
                return;
            }
            $gradient = $this->gradients[($i + 5) % count($this->gradients)];
            $svg = $this->buildAvatarSvg($t->name, $gradient);
            $path = 'avatars/t_'.$t->id.'.svg';
            Storage::disk('public')->put($path, $svg);
            $t->avatar = $path;
            $t->save();
        });
    }

    private function buildAvatarSvg(string $name, array $gradient): string
    {
        [$c1, $c2] = $gradient;
        $initials = $this->initials($name);

        return <<<SVG
<?xml version="1.0" encoding="UTF-8"?>
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 200 200">
  <defs>
    <linearGradient id="g" x1="0" y1="0" x2="1" y2="1">
      <stop offset="0%" stop-color="$c1"/>
      <stop offset="100%" stop-color="$c2"/>
    </linearGradient>
    <style>.t { font-family: 'Poppins', 'Inter', sans-serif; font-weight: 800; fill: #ffffff; }</style>
  </defs>
  <circle cx="100" cy="100" r="100" fill="url(#g)"/>
  <text x="100" y="118" text-anchor="middle" class="t" font-size="78">$initials</text>
</svg>
SVG;
    }

    private function initials(string $name): string
    {
        $parts = preg_split('/\s+/', trim($name));
        $first = Str::upper(Str::substr($parts[0] ?? 'C', 0, 1));
        $second = Str::upper(Str::substr($parts[1] ?? '', 0, 1));
        return $first.$second ?: 'C';
    }

    // ──────────────────────────────────────────────────────────────────────
    // Categorías
    // ──────────────────────────────────────────────────────────────────────

    private function fixCategoryImages(): void
    {
        // Las categorías cuyo archivo no exista → generar SVG limpio con icono.
        CourseCategory::orderBy('id')->get()->each(function (CourseCategory $cat, int $i) {
            if ($cat->image && file_exists(storage_path('app/public/'.$cat->image))) {
                return;
            }
            $svg = $this->buildCategorySvg($cat->name, $this->categoryIcons[$cat->slug] ?? 'book', $i);
            $path = 'category/'.$cat->slug.'.svg';
            Storage::disk('public')->put($path, $svg);
            $cat->image = $path;
            $cat->save();
        });
    }

    private function buildCategorySvg(string $name, string $icon, int $i): string
    {
        $palettes = [
            ['#ECFDF5', '#10B981'], // cream verde
            ['#FFF1F2', '#FB7185'], // crema coral
            ['#FEF3C7', '#F59E0B'], // crema sol
            ['#EEF6FF', '#3E6CF6'], // crema azul
        ];
        [$bg, $fg] = $palettes[$i % count($palettes)];
        $iconSvg = $this->iconSvg($icon);
        $safe = htmlspecialchars(Str::limit($name, 18), ENT_QUOTES, 'UTF-8');

        return <<<SVG
<?xml version="1.0" encoding="UTF-8"?>
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 200">
  <rect width="320" height="200" rx="16" fill="$bg"/>
  <g transform="translate(132,48) scale(2)" fill="none" stroke="$fg" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">$iconSvg</g>
  <text x="50%" y="155" text-anchor="middle" font-family="'Poppins', sans-serif" font-weight="700" font-size="20" fill="#1F2933">$safe</text>
</svg>
SVG;
    }

    private function iconSvg(string $name): string
    {
        return match ($name) {
            'code'      => '<polyline points="14,4 22,16 14,28"/><polyline points="10,4 2,16 10,28"/>',
            'palette'   => '<circle cx="16" cy="16" r="14"/><circle cx="11" cy="11" r="1.6" fill="currentColor" stroke="none"/><circle cx="20" cy="9" r="1.6" fill="currentColor" stroke="none"/><circle cx="24" cy="16" r="1.6" fill="currentColor" stroke="none"/>',
            'megaphone' => '<polygon points="4,12 22,4 22,28 4,20"/><line x1="22" y1="10" x2="28" y2="10"/><line x1="22" y1="18" x2="28" y2="18"/>',
            'magnifier' => '<circle cx="14" cy="14" r="10"/><line x1="22" y1="22" x2="30" y2="30"/>',
            default     => '<rect x="4" y="4" width="24" height="24" rx="3"/><line x1="4" y1="10" x2="28" y2="10"/>',
        };
    }

    // ──────────────────────────────────────────────────────────────────────
    // Texto: headlines y bios
    // ──────────────────────────────────────────────────────────────────────

    private function enrichInstructorHeadlines(): void
    {
        $headlines = [
            'Instructor Demo'  => ['headline' => 'Especialista en formación digital', 'name' => 'Mario López'],
            'Lucía Martín'     => ['headline' => 'Diseñadora de Producto'],
            'Diego Romero'     => ['headline' => 'Ingeniero de Software'],
            'Carmen Ortega'    => ['headline' => 'Estratega de Marketing'],
            'Andrés Vega'      => ['headline' => 'Fotógrafo Profesional'],
        ];
        foreach ($headlines as $name => $fields) {
            $u = User::where('name', $name)->first();
            if (! $u) {
                continue;
            }
            $u->headline = $fields['headline'];
            if (isset($fields['name'])) {
                $u->name = $fields['name'];
            }
            if (empty($u->bio)) {
                $u->bio = 'Profesional en activo apasionado por compartir lo que sabe. Cree que la mejor forma de aprender es construyendo cosas reales y rodeándose de una buena comunidad.';
            }
            $u->save();
        }
    }

    // ──────────────────────────────────────────────────────────────────────
    // Limpieza de rutas huérfanas
    // ──────────────────────────────────────────────────────────────────────

    private function cleanOrphanImageColumns(): void
    {
        $columns = [
            ['hero_sections',     'hero_image'],
            ['about_sections',    'image'],
            ['general_settings',  'logo'],
            ['general_settings',  'favicon'],
            ['header_settings',   'logo'],
            ['users',             'document'],
        ];
        foreach ($columns as [$t, $col]) {
            if (! Schema::hasTable($t) || ! Schema::hasColumn($t, $col)) {
                continue;
            }
            DB::table($t)->whereNotNull($col)->where($col, '!=', '')->get(['id', $col])
                ->each(function ($row) use ($t, $col) {
                    if (! file_exists(storage_path('app/public/'.$row->$col))) {
                        DB::table($t)->where('id', $row->id)->update([$col => null]);
                    }
                });
        }
    }
}
