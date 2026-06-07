<?php

namespace Database\Seeders;

use App\Models\HeaderNavigationLink;
use Illuminate\Database\Seeder;

/**
 * Corrige el orden del menú primario (estaba invertido: empezaba por
 * 'Plantillas' y terminaba en 'Inicio').
 *
 * Orden correcto de izquierda a derecha:
 *   Inicio · Cursos · Blog · Nosotros · Contacto · Ser Instructor · Plantillas
 *
 * Idempotente: hace match por URL y reasigna sort_order.
 */
class CursaliaMenuOrderSeeder extends Seeder
{
    public function run(): void
    {
        // url => sort_order (1 = primero/izquierda)
        $order = [
            '/'                          => 1, // Inicio
            '/courses'                   => 2, // Cursos
            '/blog'                      => 3, // Blog
            '/about'                     => 4, // Nosotros
            '/contact'                   => 5, // Contacto
            '/templates'                 => 6, // Plantillas
            '/student/become-instructor' => 7, // Ser Instructor
        ];

        $updated = 0;
        foreach ($order as $url => $sort) {
            $updated += HeaderNavigationLink::where('url', $url)->update(['sort_order' => $sort]);
        }

        \App\View\Composers\BrandingComposer::flushCache();

        $this->command->info("  ✓ Orden del menú primario corregido ({$updated} enlaces).");
        $this->command->info('  → Inicio · Cursos · Blog · Nosotros · Contacto · Plantillas · Ser Instructor');
    }
}
