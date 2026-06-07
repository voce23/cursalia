<?php

namespace Database\Seeders;

use App\Models\AboutSection;
use App\Models\Brand;
use App\Models\Counter;
use App\Models\Course;
use App\Models\FeaturedInstructor;
use App\Models\HeroSection;
use App\Models\Testimonial;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

/**
 * Modo lanzamiento · desactiva los datos DEMO fraudulentos del home antes
 * de subir a producción.
 *
 * Por qué: el sitio recién instalado trae datos de ejemplo (testimonios de
 * personas inventadas, logos de "empresas que contratan a nuestros egresados",
 * instructores ficticios). Mostrarlos en un sitio real es:
 *   - Publicidad engañosa (sancionable en España/UE).
 *   - Pérdida de confianza del visitante.
 *
 * Este seeder los DESACTIVA (is_active = false), no los borra. Cuando tengas
 * testimonios/instructores/marcas REALES, los reactivas desde el admin o
 * editas estos registros.
 *
 * Las secciones del home tienen guards @if(isNotEmpty) → al quedar sin
 * registros activos, desaparecen solas sin huecos rotos.
 *
 * Ejecutar:  php artisan db:seed --class=CursaliaLaunchCleanupSeeder
 */
class CursaliaLaunchCleanupSeeder extends Seeder
{
    public function run(): void
    {
        $t = Testimonial::query()->update(['is_active' => false]);
        $b = Brand::query()->update(['is_active' => false]);
        $i = FeaturedInstructor::query()->update(['is_active' => false]);

        $this->command->info("  ✓ Testimonios desactivados: {$t}");
        $this->command->info("  ✓ Marcas (empresas) desactivadas: {$b}");
        $this->command->info("  ✓ Instructores destacados desactivados: {$i}");

        // Hero: reemplazar el texto demo ("Plataforma #1 en Bolivia",
        // "Más de 500 cursos impartidos por expertos") por mensaje real
        // y honesto de Cursalia.
        $hero = HeroSection::query()->first();
        if ($hero) {
            $hero->badge_text     = 'Gratis y de código abierto';
            $hero->title          = 'Crea tu propia';
            $hero->highlight_text = 'academia online';
            $realSubtitle = 'Monta tu plataforma de cursos en tu propio dominio, sin pagar mensualidades ni saber programar. En español y con tu marca.';
            foreach (['subtitle', 'description', 'subtitle_text', 'sub_title'] as $col) {
                if (Schema::hasColumn('hero_sections', $col)) {
                    $hero->{$col} = $realSubtitle;
                }
            }
            $hero->save();
            $this->command->info('  ✓ Hero actualizado con mensaje real de Cursalia.');
        }

        // Cursos demo: tienen contenido de relleno (lorem) y precios que no
        // se pueden cobrar (pagos = Fase 2). Los pasamos a borrador para que
        // NO aparezcan en /courses ni en el home. Reversible: edita status a
        // 'active' desde el admin cuando tengas un curso real.
        $c = Course::query()->update(['status' => 'draft']);
        $this->command->info("  ✓ Cursos demo pasados a borrador: {$c}");

        // Counter del /about: tenía números inventados (500 cursos, 85
        // instructores, 12000 alumnos, 2000 horas). Como Cursalia recién
        // arranca y NO tiene métricas reales que presumir, eliminamos el
        // registro → la sección de contadores se oculta sola (@if($counter)).
        // Reversible: crea un Counter con números reales cuando los tengas.
        $deleted = Counter::query()->delete();
        if ($deleted) {
            $this->command->info('  ✓ Counter con números falsos eliminado (sección se oculta).');
        }

        // AboutSection: texto genérico demo → mensaje real de Cursalia.
        $about = AboutSection::query()->first();
        if ($about) {
            $about->title    = 'Sobre Cursalia';
            $about->subtitle = 'Tu academia online, sin pagar mensualidades';
            $about->content  = '<p>Cursalia es un sistema de gestión de aprendizaje (LMS) gratuito y de código abierto, pensado para que cualquier persona —sin saber programar— pueda montar su propia academia online en su propio dominio, con su marca y en español de verdad.</p>'
                .'<p>Nació de una idea simple: las plataformas como Hotmart o Thinkific te cobran comisión o mensualidad por algo que debería ser tuyo. Con Cursalia, tu academia es 100% tuya.</p>';
            $about->save();
            $this->command->info('  ✓ AboutSection actualizada con mensaje real.');
        }

        $this->command->warn('  → Las secciones del home se ocultarán automáticamente.');
        $this->command->warn('  → Reactiva cada una cuando tengas contenido REAL.');
    }
}
