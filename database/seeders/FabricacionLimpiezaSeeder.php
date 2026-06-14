<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\CourseChapter;
use App\Models\CourseChapterLesson;
use App\Models\CourseLanguage;
use App\Models\CourseLevel;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * ============================================================================
 *  PLANTILLA DE NICHO · Fabricación de productos de limpieza
 * ============================================================================
 *  Crea UN curso por producto (jabón, jaboncillo, champú, detergentes, etc.),
 *  cada uno con su estructura pedagógica completa (módulos + lecciones con
 *  descripción). Las lecciones quedan SIN video a propósito: el dueño del LMS
 *  sube SUS propios videos (es su know-how). Precio 0 (LMS gratis).
 *
 *  NO se engancha a DatabaseSeeder (es una plantilla opcional por nicho).
 *  Se ejecuta a mano:   php artisan db:seed --class=FabricacionLimpiezaSeeder
 *  Es idempotente: re-ejecutarlo actualiza el curso y reconstruye sus módulos.
 * ============================================================================
 */
class FabricacionLimpiezaSeeder extends Seeder
{
    public function run(): void
    {
        $instructorId = $this->instructorId();

        $category = CourseCategory::query()->updateOrCreate(
            ['slug' => 'fabricacion-productos-limpieza'],
            [
                'name' => 'Fabricación de productos de limpieza',
                'image' => $this->thumbnail('cat-fabricacion-limpieza', 'Productos de limpieza', ['#0ea5e9', '#0369a1']),
                'status' => true,
            ]
        );

        $level = CourseLevel::query()->firstOrCreate(['slug' => 'principiante'], ['name' => 'Principiante']);
        $language = CourseLanguage::query()->firstOrCreate(['slug' => 'espanol'], ['name' => 'Español']);

        // Un curso por producto. 'noun' = cómo se nombra el producto dentro de las frases.
        $products = [
            ['title' => 'Jabón de tocador artesanal',        'slug' => 'jabon-tocador-artesanal',        'noun' => 'jabón de tocador',        'colors' => ['#34d399', '#059669']],
            ['title' => 'Jaboncillo lavador de ropa',         'slug' => 'jaboncillo-lavador-ropa',         'noun' => 'jaboncillo lavador',      'colors' => ['#fbbf24', '#d97706']],
            ['title' => 'Champú casero',                      'slug' => 'champu-casero',                   'noun' => 'champú',                  'colors' => ['#f472b6', '#be185d']],
            ['title' => 'Detergente líquido para ropa',       'slug' => 'detergente-liquido-ropa',         'noun' => 'detergente líquido',      'colors' => ['#60a5fa', '#1d4ed8']],
            ['title' => 'Detergente en polvo',                'slug' => 'detergente-en-polvo',             'noun' => 'detergente en polvo',     'colors' => ['#818cf8', '#4338ca']],
            ['title' => 'Lavavajillas líquido',               'slug' => 'lavavajillas-liquido',            'noun' => 'lavavajillas líquido',    'colors' => ['#2dd4bf', '#0f766e']],
            ['title' => 'Suavizante de ropa',                 'slug' => 'suavizante-ropa',                 'noun' => 'suavizante',              'colors' => ['#c084fc', '#7e22ce']],
            ['title' => 'Limpiador desinfectante multiusos',  'slug' => 'limpiador-desinfectante-multiusos', 'noun' => 'limpiador desinfectante', 'colors' => ['#f87171', '#b91c1c']],
        ];

        foreach ($products as $p) {
            $course = Course::query()->updateOrCreate(
                ['slug' => $p['slug']],
                [
                    'instructor_id' => $instructorId,
                    'category_id' => $category->id,
                    'course_level_id' => $level->id,
                    'course_language_id' => $language->id,
                    'title' => $p['title'],
                    'seo_description' => "Aprende a fabricar {$p['noun']} paso a paso: insumos, proceso, envasado y venta.",
                    'thumbnail' => $this->thumbnail($p['slug'], $p['title'], $p['colors']),
                    'description' => "Curso práctico para fabricar {$p['noun']} de calidad desde casa o tu taller. "
                        ."Cubre los insumos, el proceso paso a paso, el control de calidad, el envasado y cómo venderlo. "
                        ."Ideal para emprender con productos de limpieza.",
                    'price' => 0,
                    'discount' => 0,
                    'duration' => 'A tu ritmo',
                    'certificate' => true,
                    'qna' => true,
                    'is_approved' => 'approved',
                    'status' => 'active',
                ]
            );

            // Reconstruir módulos/lecciones (idempotente).
            CourseChapter::query()->where('course_id', $course->id)->delete();

            foreach ($this->curriculum($p['noun']) as $chapterIndex => $chapterData) {
                $chapter = CourseChapter::query()->create([
                    'course_id' => $course->id,
                    'instructor_id' => $instructorId,
                    'title' => $chapterData['title'],
                    'order' => $chapterIndex + 1,
                    'status' => true,
                ]);

                foreach ($chapterData['lessons'] as $lessonIndex => $lesson) {
                    CourseChapterLesson::query()->create([
                        'title' => $lesson['title'],
                        'slug' => Str::slug($p['title'].' '.$chapterData['title'].' '.$lesson['title']),
                        'description' => $lesson['desc'],
                        'instructor_id' => $instructorId,
                        'course_id' => $course->id,
                        'chapter_id' => $chapter->id,
                        'file_path' => null,     // SIN video: el dueño lo sube luego
                        'storage' => 'upload',
                        'file_type' => 'video',
                        'duration' => null,
                        'downloadable' => false,
                        'order' => $lessonIndex + 1,
                        'is_preview' => $lesson['preview'] ?? false,
                        'status' => true,
                    ]);
                }
            }

            $this->command?->info("  ✔ Curso creado: {$p['title']}");
        }

        $this->command?->info('Plantilla "Fabricación de productos de limpieza" lista: '.count($products).' cursos.');
    }

    /**
     * Estructura pedagógica común a todos los productos (parametrizada por $noun).
     * 5 módulos · 16 lecciones. Sólo títulos + descripciones (el video lo sube el dueño).
     */
    private function curriculum(string $noun): array
    {
        return [
            [
                'title' => 'Módulo 1 · Fundamentos y seguridad',
                'lessons' => [
                    ['title' => 'Bienvenida: qué vas a fabricar y para quién', 'preview' => true,
                        'desc' => "Presentación del curso de {$noun}: qué aprenderás, qué producto final obtendrás y a qué clientes puedes venderlo."],
                    ['title' => 'Equipo, utensilios y área de trabajo',
                        'desc' => "Lista del equipo básico (recipientes, balanza, agitador, moldes, envases) y cómo montar un área de trabajo limpia y ordenada para fabricar {$noun}."],
                    ['title' => 'Seguridad, higiene y primeros auxilios',
                        'desc' => "Manejo seguro de los insumos, equipo de protección (guantes, lentes, mascarilla), ventilación y qué hacer ante salpicaduras. Buenas prácticas de higiene."],
                ],
            ],
            [
                'title' => 'Módulo 2 · Insumos e ingredientes',
                'lessons' => [
                    ['title' => 'Qué insumos necesitas y dónde comprarlos',
                        'desc' => "Lista completa de insumos para fabricar {$noun}, calidades recomendadas y dónde conseguirlos a buen precio."],
                    ['title' => 'La función de cada ingrediente',
                        'desc' => "Para qué sirve cada componente de la fórmula del {$noun} y cómo afecta al resultado final (limpieza, espuma, aroma, textura)."],
                    ['title' => 'Proporciones, medidas y rendimiento',
                        'desc' => "Cómo medir correctamente cada insumo y calcular el rendimiento: cuánto producto obtienes por lote y cómo escalar la receta."],
                ],
            ],
            [
                'title' => 'Módulo 3 · Elaboración paso a paso',
                'lessons' => [
                    ['title' => 'Preparación previa y pesado',
                        'desc' => "Organización de la mesa de trabajo y pesado/medición exacta de cada insumo antes de empezar a fabricar {$noun}."],
                    ['title' => "Proceso de elaboración del {$noun}", 'preview' => true,
                        'desc' => "El paso a paso de la elaboración del {$noun}: orden de mezcla, tiempos, temperatura y agitación."],
                    ['title' => 'El punto correcto y control de calidad',
                        'desc' => "Cómo reconocer cuándo el {$noun} está en su punto: textura, color, densidad, espuma y aroma. Pruebas sencillas de control de calidad."],
                    ['title' => 'Errores comunes y cómo corregirlos',
                        'desc' => "Los fallos más frecuentes al fabricar {$noun} (separación, grumos, poca espuma, mal aroma) y cómo solucionarlos sin perder el lote."],
                ],
            ],
            [
                'title' => 'Módulo 4 · Variantes, envasado y etiquetado',
                'lessons' => [
                    ['title' => 'Variantes: aroma, color y concentración',
                        'desc' => "Cómo personalizar tu {$noun} con distintos aromas, colores y concentraciones para diferenciarte y ofrecer variedad."],
                    ['title' => 'Envasado y dosificación',
                        'desc' => "Elección de envases, llenado, sellado y dosificación adecuada del {$noun} para que llegue bien al cliente."],
                    ['title' => 'Etiquetado y conservación',
                        'desc' => "Diseño de una etiqueta clara, datos que debe llevar, vida útil y cómo conservar el {$noun} para que no se eche a perder."],
                ],
            ],
            [
                'title' => 'Módulo 5 · Costos y venta',
                'lessons' => [
                    ['title' => 'Calcula tu costo y precio de venta',
                        'desc' => "Cómo calcular el costo real por unidad del {$noun} y fijar un precio que sea rentable y competitivo."],
                    ['title' => 'Marca, presentación y fotos',
                        'desc' => "Crea una imagen de marca atractiva y aprende a fotografiar tu {$noun} para que se venda mejor en redes y tienda."],
                    ['title' => 'Dónde y cómo vender tu producto',
                        'desc' => "Canales de venta (tienda, redes, mayoreo, ferias), cómo conseguir tus primeros clientes y fidelizarlos."],
                ],
            ],
        ];
    }

    /** Reusa un instructor existente o crea uno genérico (la plantilla debe ser autosuficiente). */
    private function instructorId(): int
    {
        $id = DB::table('users')->where('role', 'instructor')->value('id');
        if ($id) {
            return (int) $id;
        }

        return (int) DB::table('users')->insertGetId([
            'name' => 'Equipo Docente',
            'email' => 'docente.limpieza@'.(parse_url((string) config('app.url'), PHP_URL_HOST) ?: 'cursalia.test'),
            'password' => Hash::make(Str::random(24)),
            'role' => 'instructor',
            'approve_status' => 'approved',
            'instructor_request' => true,
            'email_verified_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /** Genera una miniatura SVG 16:9 (gradiente + ícono de botella + título) y devuelve su ruta. */
    private function thumbnail(string $slug, string $title, array $colors): string
    {
        [$c1, $c2] = $colors;
        $safeTitle = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');

        $svg = <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 800 450">
  <defs>
    <linearGradient id="g" x1="0" y1="0" x2="1" y2="1">
      <stop offset="0" stop-color="{$c1}"/><stop offset="1" stop-color="{$c2}"/>
    </linearGradient>
  </defs>
  <rect width="800" height="450" fill="url(#g)"/>
  <g fill="#ffffff" opacity="0.16">
    <circle cx="660" cy="90" r="120"/>
    <circle cx="120" cy="380" r="90"/>
  </g>
  <g transform="translate(360,150)" fill="none" stroke="#ffffff" stroke-width="6" stroke-linejoin="round" stroke-linecap="round" opacity="0.95">
    <path d="M34 0 h12 v14 l10 10 v56 a8 8 0 0 1 -8 8 h-26 a8 8 0 0 1 -8 -8 v-56 l10 -10 z"/>
    <line x1="22" y1="48" x2="58" y2="48"/>
  </g>
  <text x="400" y="330" text-anchor="middle" font-family="Segoe UI, Arial, sans-serif" font-size="38" font-weight="800" fill="#ffffff">{$safeTitle}</text>
  <text x="400" y="372" text-anchor="middle" font-family="Segoe UI, Arial, sans-serif" font-size="20" font-weight="500" fill="#ffffff" opacity="0.85">Fabricación artesanal · paso a paso</text>
</svg>
SVG;

        $path = "course/{$slug}.svg";
        Storage::disk('public')->put($path, $svg);

        return $path;
    }
}
