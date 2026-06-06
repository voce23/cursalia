<?php

namespace Database\Seeders;

use App\Models\Blog;
use Illuminate\Database\Seeder;

/**
 * Inyecta las 5 FAQ pre-redactadas en cada una de las 2 lecciones publicadas.
 *
 * Es lo que el admin haría manualmente desde /admin/blogs/{id}/edit pero
 * más rápido y reproducible. Activa el rich snippet de Preguntas en SERP.
 */
class CursaliaLessonsFaqSeeder extends Seeder
{
    public function run(): void
    {
        // ─── Lección 0 ───────────────────────────────────────────────────
        $lec0 = Blog::where('slug', 'lec-00-construye-tu-propia-academia-online')->first();
        if ($lec0) {
            $lec0->faq = [
                [
                    'q' => '¿Necesito saber programar para hacer este curso de Cursalia?',
                    'a' => 'No. Las 13 primeras lecciones del curso están diseñadas para que avances sin escribir código. Verás algunos comandos que tendrás que copiar y pegar, pero no entender. La idea es que Cursalia te dé el trabajo hecho.',
                ],
                [
                    'q' => '¿Cuánto tiempo necesito para terminar el curso completo?',
                    'a' => 'Depende del ritmo: con 3 horas a la semana lo terminas en 4 meses, con 1 hora al día en 6 semanas, y con 4 horas al día en 2 semanas. Cada lección es autocontenida, así que puedes parar y retomar cuando quieras.',
                ],
                [
                    'q' => '¿Qué diferencia hay entre Cursalia y otras plataformas como Hotmart o Thinkific?',
                    'a' => 'Cursalia es un sistema que descargas y pones en tu servidor: no pagas mensualidades, tu academia es 100% tuya y tu marca no se diluye con la de la plataforma. Hotmart o Thinkific son servicios alquilados: te dan infraestructura plug-and-play, pero te cobran comisión o cuota mensual y tu lista de alumnos no es del todo tuya.',
                ],
                [
                    'q' => '¿Cursalia es realmente gratis o tiene letra pequeña?',
                    'a' => 'Cursalia FREE es gratis y sin "Powered by Cursalia" obligatorio. Existe una versión Cursalia PRO de pago con cobros automáticos, marketplace multi-instructor y certificados, pero la FREE funciona sin limitaciones absurdas y puedes empezar a usarla hoy mismo.',
                ],
                [
                    'q' => '¿Qué necesito antes de empezar la Lección 1?',
                    'a' => 'Solo cuatro cosas: una computadora (Windows, Mac o Linux), conexión a internet decente, 2-3 horas libres para la primera lección y cero conocimientos de programación. Si tienes esas cuatro, tienes lo que hace falta.',
                ],
            ];
            $lec0->save();
            $this->command->info('  ✓ Lección 0 → '.count($lec0->faq).' preguntas guardadas');
        } else {
            $this->command->warn('  ✗ Lección 0 no encontrada');
        }

        // ─── Lección 1 ───────────────────────────────────────────────────
        $lec1 = Blog::where('slug', 'lec-01-herramientas-laravel-windows')->first();
        if ($lec1) {
            $lec1->faq = [
                [
                    'q' => '¿Qué es Laragon y por qué lo usamos en lugar de XAMPP o WAMP?',
                    'a' => 'Laragon es un entorno de desarrollo local para Windows muy ligero que viene con PHP, MySQL, Apache/Nginx y Composer preinstalados. Comparado con XAMPP es más rápido, ocupa menos disco y no rompe al actualizar Windows. Por eso es la elección recomendada en 2026.',
                ],
                [
                    'q' => '¿Puedo seguir el curso si uso Mac o Linux en lugar de Windows?',
                    'a' => 'Sí. Las herramientas que instalamos (Node.js, Git, VS Code, Composer) existen para los tres sistemas. La única diferencia es que en Mac usarás Valet o Herd en lugar de Laragon, y en Linux puedes usar Docker o instalación nativa. El resto del curso es idéntico.',
                ],
                [
                    'q' => '¿Cuánto espacio en disco necesito para todas estas herramientas?',
                    'a' => 'Aproximadamente 4-5 GB en total: Laragon ocupa unos 600 MB, Node.js 100 MB, Git 250 MB, VS Code 350 MB y el proyecto Cursalia con sus dependencias unos 500 MB-1 GB. Si tu disco tiene menos de 10 GB libres, libera espacio antes de empezar.',
                ],
                [
                    'q' => '¿Tengo que pagar por alguna de estas herramientas?',
                    'a' => 'No. Las cinco herramientas (Laragon, Node.js, Git, VS Code, Composer) son 100% gratuitas y open source. No hay versión "Pro" oculta ni periodo de prueba. Las usas para siempre sin coste.',
                ],
                [
                    'q' => '¿Qué hago si una instalación falla a mitad del proceso?',
                    'a' => 'Lo más común: instalador bloqueado por antivirus, falta de permisos de administrador, o conexión a internet lenta. En el 90% de casos basta con desactivar temporalmente el antivirus, ejecutar el instalador como administrador (clic derecho → "Ejecutar como administrador") y reintentar. Si persiste, escríbeme y vemos tu caso.',
                ],
            ];
            $lec1->save();
            $this->command->info('  ✓ Lección 1 → '.count($lec1->faq).' preguntas guardadas');
        } else {
            $this->command->warn('  ✗ Lección 1 no encontrada');
        }
    }
}
