<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            // Base
            AdminSeeder::class,
            InitialConfigurationSeeder::class,
            CursaliaBrandingSeeder::class,      // marca Cursalia + paleta verde
            CursaliaMenuOrderSeeder::class,
            CursaliaFooterCleanupSeeder::class,

            // Contenido: blog
            BlogSeeder::class,
            CursaliaHotmartArticleSeeder::class,
            CursaliaLmsComparativaArticleSeeder::class,
            CursaliaThinkificMigrationArticleSeeder::class,

            // Contenido: cursos + lecciones
            CourseSeeder::class,
            CursaliaLesson00Seeder::class,
            CursaliaLesson01Seeder::class,
            CursaliaLessonsFaqSeeder::class,
            CursaliaQuizDemoSeeder::class,

            // Páginas / CMS
            CursaliaHomeExtraSeeder::class,
            CursaliaServicesSeeder::class,
            CursaliaMarketplaceSeeder::class,

            // Ajustes finales (avatares, imágenes de categorías, limpieza de rutas)
            CursaliaAuditFixesSeeder::class,

            // OJO: CursaliaLaunchCleanupSeeder NO se incluye a propósito — ese seeder
            // VACÍA el demo (cursos a borrador, testimonios/marcas/instructores off) y
            // está pensado para ejecutarse a mano cuando el dueño pasa a producción con
            // su contenido REAL. Una instalación nueva debe verse poblada y atractiva.

            // SVGs de imágenes: SIEMPRE el último (necesita todo el contenido ya creado)
            CursaliaVisualFixesSeeder::class,
        ]);
    }
}
