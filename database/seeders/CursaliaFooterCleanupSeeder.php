<?php

namespace Database\Seeders;

use App\Models\FooterColumnTwo;
use Illuminate\Database\Seeder;

/**
 * Limpia los duplicados de la columna 2 del footer ("Empresa") que se solapan
 * con "Explorar" (sobre nosotros, ser instructor) y con "Compañía/Legal"
 * (términos, privacidad). El footer queda con 3 columnas funcionales claras:
 *
 *   EXPLORAR   → footer_column_ones      (catálogo y descubrimiento)
 *   SOPORTE    → footer_column_twos      (ayuda al usuario, sin legales)
 *   LEGAL      → custom_pages slug=legal/* (privacidad, términos, etc.)
 *
 * También deja solo URLs reales (no `#`).
 */
class CursaliaFooterCleanupSeeder extends Seeder
{
    public function run(): void
    {
        // Borrar absolutamente todo lo de footer_column_twos para empezar limpio.
        FooterColumnTwo::query()->delete();

        // Repoblar con los 3 items útiles, todos con URL real.
        $items = [
            ['title' => 'Centro de ayuda',      'url' => '/contact',  'sort_order' => 1, 'is_active' => true],
            ['title' => 'Contacto',             'url' => '/contact',  'sort_order' => 2, 'is_active' => true],
            ['title' => 'Servicios y asesoría', 'url' => '/services', 'sort_order' => 3, 'is_active' => true],
        ];

        foreach ($items as $item) {
            FooterColumnTwo::create($item);
        }

        // Invalidar cache del composer para que el footer se regenere.
        \App\View\Composers\BrandingComposer::flushCache();

        $this->command->info('  ✓ Footer column 2 limpiada (3 items, sin duplicados ni URLs rotas).');
    }
}
