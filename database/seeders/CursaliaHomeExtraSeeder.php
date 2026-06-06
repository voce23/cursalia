<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\FeaturedInstructor;
use App\Models\Testimonial;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Datos extra que el CourseSeeder del LMSL13 no puebla:
 * marcas, instructores destacados y testimonios. Necesarios para que el
 * home Cursalia se vea completo.
 */
class CursaliaHomeExtraSeeder extends Seeder
{
    public function run(): void
    {
        // ── Marcas (logos como texto, sin necesidad de archivos) ──────────────
        $brands = [
            'Nimbus', 'Vertex', 'Lumio', 'Quanta', 'Northpeak', 'Helix',
        ];
        foreach ($brands as $i => $name) {
            Brand::updateOrCreate(
                ['name' => $name],
                ['url' => '#', 'sort_order' => $i + 1, 'is_active' => true]
            );
        }

        // ── Crear instructores adicionales si faltan ──────────────────────────
        $extra = [
            ['name' => 'Lucía Martín',  'email' => 'lucia.martin@cursalia.test',  'headline' => 'Diseñadora de Producto'],
            ['name' => 'Diego Romero',  'email' => 'diego.romero@cursalia.test',  'headline' => 'Ingeniero de Software'],
            ['name' => 'Carmen Ortega', 'email' => 'carmen.ortega@cursalia.test', 'headline' => 'Estratega de Marketing'],
            ['name' => 'Andrés Vega',   'email' => 'andres.vega@cursalia.test',   'headline' => 'Fotógrafo Profesional'],
        ];
        foreach ($extra as $data) {
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name'              => $data['name'],
                    'password'          => bcrypt('password'),
                    'email_verified_at' => now(),
                ]
            );
            $user->forceFill([
                'role'           => 'instructor',
                'approve_status' => 'approved',
                'headline'     => $data['headline'],
            ])->save();
        }

        // ── Instructores destacados ───────────────────────────────────────────
        $instructors = User::where('role', 'instructor')
            ->where('approve_status', 'approved')
            ->take(4)
            ->get();
        foreach ($instructors as $i => $user) {
            FeaturedInstructor::updateOrCreate(
                ['user_id' => $user->id],
                ['sort_order' => $i + 1, 'is_active' => true]
            );
        }

        // ── Testimonios ───────────────────────────────────────────────────────
        $testimonials = [
            [
                'name'        => 'Sofía Reyes',
                'designation' => 'Diseñadora UI',
                'message'     => 'Pasé de no saber nada de diseño a tener un portafolio real en 4 meses. Los proyectos del curso fueron justo lo que necesitaba para conseguir mi primer trabajo.',
                'rating'      => 5,
            ],
            [
                'name'        => 'Mateo Torres',
                'designation' => 'Desarrollador Frontend',
                'message'     => 'La mejor de las plataformas. Las explicaciones son claras, los mentores responden de verdad y todo está actualizado al 2026.',
                'rating'      => 5,
            ],
            [
                'name'        => 'Valentina Cruz',
                'designation' => 'Emprendedora',
                'message'     => 'Cursalia me ayudó a lanzar mi propio emprendimiento. El curso de marketing fue práctico desde el primer día y todo lo apliqué en mi negocio.',
                'rating'      => 5,
            ],
        ];
        foreach ($testimonials as $i => $t) {
            Testimonial::updateOrCreate(
                ['name' => $t['name']],
                array_merge($t, ['sort_order' => $i + 1, 'is_active' => true])
            );
        }

        $this->command->info('  ✓ '.Brand::count().' marcas');
        $this->command->info('  ✓ '.FeaturedInstructor::count().' instructores destacados');
        $this->command->info('  ✓ '.Testimonial::count().' testimonios');
    }
}
