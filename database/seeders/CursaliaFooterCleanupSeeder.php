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
        // Idempotente: borrar SOLO los items rotos/duplicados conocidos del seed
        // anterior. NO toca items que el admin haya creado/editado manualmente.
        $deprecatedTitles = [
            'Centro de Ayuda',         // URL era "#" (rota)
            'Términos y Condiciones',  // duplicada con columna Legal
            'Política de Privacidad',  // duplicada con columna Legal
            'Sobre nosotros',          // duplicada con columna Explorar
            'Ser instructor',          // duplicada con columna Explorar
        ];
        FooterColumnTwo::whereIn('title', $deprecatedTitles)->delete();

        // Items útiles. Usamos updateOrCreate para que sea idempotente:
        // si ya existen (por correr el seeder dos veces), solo actualizan.
        $items = [
            ['title' => 'Centro de ayuda',      'url' => '/contact',  'sort_order' => 1, 'is_active' => true],
            ['title' => 'Contacto',             'url' => '/contact',  'sort_order' => 2, 'is_active' => true],
            ['title' => 'Servicios y asesoría', 'url' => '/services', 'sort_order' => 3, 'is_active' => true],
        ];

        foreach ($items as $item) {
            FooterColumnTwo::updateOrCreate(
                ['title' => $item['title']],   // identidad por título
                $item
            );
        }

        // Invalidar cache del composer para que el footer se regenere.
        \App\View\Composers\BrandingComposer::flushCache();

        $this->command->info('  ✓ Footer column 2 limpiada (3 items, sin duplicados ni URLs rotas).');
    }
}
