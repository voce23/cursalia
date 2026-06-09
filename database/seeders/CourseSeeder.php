<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\CourseChapter;
use App\Models\CourseChapterLesson;
use App\Models\CourseLanguage;
use App\Models\CourseLevel;
use App\Models\CourseReview;
use App\Models\CertificateBuilder;
use App\Models\CertificateBuilderItem;
use App\Models\Enrollment;
use App\Models\LessonCompletion;
use App\Models\WatchHistory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class CourseSeeder extends Seeder
{
    private const CERTIFICATE_ITEMS = [
        'title' => ['x_position' => 170, 'y_position' => 130],
        'subtitle' => ['x_position' => 170, 'y_position' => 225],
        'description' => ['x_position' => 170, 'y_position' => 315],
        'signature' => ['x_position' => 840, 'y_position' => 560],
    ];

    public function run(): void
    {
        $categoryImage = 'course/crs_69eacd210aa62.webp';

        CertificateBuilder::query()->updateOrCreate(
            ['id' => 1],
            [
                'background' => 'certificates/demo-certificate-background.svg',
                'signature' => 'certificates/demo-signature.svg',
                'title' => 'Certificado de finalizacion',
                'sub_title' => 'Otorgado a [student_name]',
                'description' => 'Por completar satisfactoriamente el curso [course_name] en [platform_name] el dia [date].',
            ]
        );

        foreach (self::CERTIFICATE_ITEMS as $elementId => $position) {
            CertificateBuilderItem::query()->updateOrCreate(
                ['element_id' => $elementId],
                $position,
            );
        }

        DB::table('users')->updateOrInsert(
            ['email' => 'instructor@lmsl13.test'],
            [
                'name' => 'Instructor Demo',
                'password' => Hash::make('password'),
                'role' => 'instructor',
                'approve_status' => 'approved',
                'instructor_request' => true,
                'image' => 'instructors/instructor-demo.svg',
                'headline' => 'Especialista en formacion digital',
                'bio' => 'Instructor de muestra para poblar el catalogo publico del LMS.',
                'facebook' => 'https://facebook.com/instructor.demo',
                'x' => 'https://x.com/instructor_demo',
                'linkedin' => 'https://linkedin.com/in/instructor-demo',
                'email_verified_at' => now(),
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );

        $instructorId = DB::table('users')
            ->where('email', 'instructor@lmsl13.test')
            ->value('id');

        $students = [
            [
                'name' => 'Ana Martinez',
                'email' => 'ana.estudiante@lmsl13.test',
                'headline' => 'Frontend junior en crecimiento',
            ],
            [
                'name' => 'Carlos Rojas',
                'email' => 'carlos.estudiante@lmsl13.test',
                'headline' => 'Analista digital y autodidacta',
            ],
            [
                'name' => 'Lucia Perez',
                'email' => 'lucia.estudiante@lmsl13.test',
                'headline' => 'Diseñadora enfocada en producto',
            ],
        ];

        foreach ($students as $student) {
            DB::table('users')->updateOrInsert(
                ['email' => $student['email']],
                [
                    'name' => $student['name'],
                    'password' => Hash::make('password'),
                    'role' => 'student',
                    'approve_status' => 'approved',
                    'instructor_request' => false,
                    'headline' => $student['headline'],
                    'email_verified_at' => now(),
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }

        $studentIds = DB::table('users')
            ->whereIn('email', collect($students)->pluck('email'))
            ->pluck('id', 'email');

        $development = CourseCategory::query()->updateOrCreate(
            ['slug' => 'desarrollo-web'],
            ['name' => 'Desarrollo Web', 'image' => $categoryImage, 'status' => true]
        );

        $design = CourseCategory::query()->updateOrCreate(
            ['slug' => 'diseno-digital'],
            ['name' => 'Diseno Digital', 'image' => $categoryImage, 'status' => true]
        );

        $marketing = CourseCategory::query()->updateOrCreate(
            ['slug' => 'marketing-digital'],
            ['name' => 'Marketing Digital', 'image' => $categoryImage, 'status' => true]
        );

        $categories = [
            CourseCategory::query()->updateOrCreate(
                ['slug' => 'laravel'],
                ['name' => 'Laravel', 'parent_id' => $development->id, 'image' => $categoryImage, 'status' => true]
            ),
            CourseCategory::query()->updateOrCreate(
                ['slug' => 'javascript'],
                ['name' => 'JavaScript', 'parent_id' => $development->id, 'image' => $categoryImage, 'status' => true]
            ),
            CourseCategory::query()->updateOrCreate(
                ['slug' => 'ux-ui'],
                ['name' => 'UX UI', 'parent_id' => $design->id, 'image' => $categoryImage, 'status' => true]
            ),
            CourseCategory::query()->updateOrCreate(
                ['slug' => 'branding'],
                ['name' => 'Branding', 'parent_id' => $design->id, 'image' => $categoryImage, 'status' => true]
            ),
            CourseCategory::query()->updateOrCreate(
                ['slug' => 'seo'],
                ['name' => 'SEO', 'parent_id' => $marketing->id, 'image' => $categoryImage, 'status' => true]
            ),
            CourseCategory::query()->updateOrCreate(
                ['slug' => 'redes-sociales'],
                ['name' => 'Redes Sociales', 'parent_id' => $marketing->id, 'image' => $categoryImage, 'status' => true]
            ),
        ];

        $levels = [
            CourseLevel::query()->updateOrCreate(['slug' => 'principiante'], ['name' => 'Principiante']),
            CourseLevel::query()->updateOrCreate(['slug' => 'intermedio'], ['name' => 'Intermedio']),
            CourseLevel::query()->updateOrCreate(['slug' => 'avanzado'], ['name' => 'Avanzado']),
        ];

        $languages = [
            CourseLanguage::query()->updateOrCreate(['slug' => 'espanol'], ['name' => 'Espanol']),
            CourseLanguage::query()->updateOrCreate(['slug' => 'ingles'], ['name' => 'Ingles']),
        ];

        $courses = [
            [
                'title' => 'Laravel 13 desde cero',
                'slug' => 'laravel-13-desde-cero',
                'seo_description' => 'Aprende Laravel 13 creando aplicaciones modernas desde cero.',
                'description' => 'Curso practico para construir proyectos con rutas, controladores, Blade y base de datos.',
                'price' => 49.90,
                'discount' => 29.90,
                'duration' => '12 horas',
                'thumbnail' => 'course/laravel-13-desde-cero.svg',
                'category' => $categories[0],
                'level' => $levels[0],
                'language' => $languages[0],
                'curriculum' => [
                    [
                        'title' => 'Base del proyecto Laravel',
                        'lessons' => [
                            ['title' => 'Instalacion y estructura del framework', 'duration' => '12:40', 'is_preview' => true],
                            ['title' => 'Rutas, controladores y vistas Blade', 'duration' => '19:30', 'is_preview' => false],
                            ['title' => 'Layouts, componentes y secciones', 'duration' => '17:10', 'is_preview' => false],
                        ],
                    ],
                    [
                        'title' => 'Persistencia y despliegue',
                        'lessons' => [
                            ['title' => 'Migraciones, modelos y relaciones', 'duration' => '23:10', 'is_preview' => true],
                            ['title' => 'Formularios, validaciones y CRUD', 'duration' => '26:35', 'is_preview' => false],
                            ['title' => 'Preparacion para produccion', 'duration' => '14:20', 'is_preview' => false],
                        ],
                    ],
                ],
                'reviews' => [
                    ['email' => 'ana.estudiante@lmsl13.test', 'rating' => 5, 'review' => 'Muy claro para arrancar con Laravel. El flujo de rutas y Blade queda bien explicado.'],
                    ['email' => 'carlos.estudiante@lmsl13.test', 'rating' => 4, 'review' => 'Buen curso para crear una base solida. Me gusto la parte de migraciones y CRUD.'],
                    ['email' => 'lucia.estudiante@lmsl13.test', 'rating' => 5, 'review' => 'Explica bien la arquitectura y ayuda a perder el miedo al framework.'],
                ],
                'progress' => [
                    'ana.estudiante@lmsl13.test' => 6,
                    'carlos.estudiante@lmsl13.test' => 4,
                    'lucia.estudiante@lmsl13.test' => 2,
                ],
            ],
            [
                'title' => 'JavaScript moderno para proyectos reales',
                'slug' => 'javascript-moderno-para-proyectos-reales',
                'seo_description' => 'Domina JavaScript moderno con ejercicios y componentes interactivos.',
                'description' => 'Variables, funciones, asincronia, consumo de APIs y patrones de trabajo actuales.',
                'price' => 39.90,
                'discount' => 24.90,
                'duration' => '10 horas',
                'thumbnail' => 'course/javascript-moderno-para-proyectos-reales.svg',
                'category' => $categories[1],
                'level' => $levels[1],
                'language' => $languages[0],
                'curriculum' => [
                    [
                        'title' => 'JavaScript del presente',
                        'lessons' => [
                            ['title' => 'Scope, let, const y template literals', 'duration' => '11:50', 'is_preview' => true],
                            ['title' => 'Arrow functions y metodos utiles de arrays', 'duration' => '16:20', 'is_preview' => false],
                            ['title' => 'Objetos, destructuring y modularidad', 'duration' => '15:45', 'is_preview' => false],
                        ],
                    ],
                    [
                        'title' => 'Aplicacion conectada a APIs',
                        'lessons' => [
                            ['title' => 'Promises, async await y manejo de errores', 'duration' => '18:05', 'is_preview' => true],
                            ['title' => 'Render dinamico y estados de carga', 'duration' => '20:40', 'is_preview' => false],
                            ['title' => 'Refactor y organizacion del proyecto', 'duration' => '13:10', 'is_preview' => false],
                        ],
                    ],
                ],
                'reviews' => [
                    ['email' => 'ana.estudiante@lmsl13.test', 'rating' => 4, 'review' => 'Buen equilibrio entre teoria y practica. La parte de async await me ayudo bastante.'],
                    ['email' => 'carlos.estudiante@lmsl13.test', 'rating' => 5, 'review' => 'Muy util para trabajar con APIs reales y ordenar mejor el codigo.'],
                ],
                'progress' => [
                    'ana.estudiante@lmsl13.test' => 5,
                    'carlos.estudiante@lmsl13.test' => 6,
                    'lucia.estudiante@lmsl13.test' => 1,
                ],
            ],
            [
                'title' => 'UX UI para plataformas educativas',
                'slug' => 'ux-ui-para-plataformas-educativas',
                'seo_description' => 'Disena experiencias de usuario claras para productos educativos.',
                'description' => 'Investigacion, wireframes, prototipos y buenas practicas para interfaces de aprendizaje.',
                'price' => 44.90,
                'discount' => 0,
                'duration' => '8 horas',
                'thumbnail' => 'course/ux-ui-para-plataformas-educativas.svg',
                'category' => $categories[2],
                'level' => $levels[0],
                'language' => $languages[0],
                'curriculum' => [
                    [
                        'title' => 'Investigacion y definicion de experiencia',
                        'lessons' => [
                            ['title' => 'Entender al estudiante y sus objetivos', 'duration' => '10:20', 'is_preview' => true],
                            ['title' => 'Journey map para productos de aprendizaje', 'duration' => '14:25', 'is_preview' => false],
                            ['title' => 'Priorizacion de problemas de usabilidad', 'duration' => '12:55', 'is_preview' => false],
                        ],
                    ],
                    [
                        'title' => 'De wireframe a prototipo',
                        'lessons' => [
                            ['title' => 'Estructura de pantallas y jerarquia visual', 'duration' => '16:35', 'is_preview' => true],
                            ['title' => 'Prototipo navegable y pruebas rapidas', 'duration' => '19:05', 'is_preview' => false],
                            ['title' => 'Documentacion de decisiones de diseño', 'duration' => '11:20', 'is_preview' => false],
                        ],
                    ],
                ],
                'reviews' => [
                    ['email' => 'lucia.estudiante@lmsl13.test', 'rating' => 5, 'review' => 'Excelente enfoque para plataformas educativas. Los ejercicios estan bien pensados.'],
                    ['email' => 'ana.estudiante@lmsl13.test', 'rating' => 4, 'review' => 'Me gusto como traduce investigacion en decisiones de interfaz concretas.'],
                ],
                'progress' => [
                    'ana.estudiante@lmsl13.test' => 3,
                    'carlos.estudiante@lmsl13.test' => 1,
                    'lucia.estudiante@lmsl13.test' => 6,
                ],
            ],
            [
                'title' => 'Branding visual para emprendedores',
                'slug' => 'branding-visual-para-emprendedores',
                'seo_description' => 'Construye una identidad visual consistente para tu marca.',
                'description' => 'Color, tipografia, tono visual y piezas base para una marca digital memorable.',
                'price' => 34.90,
                'discount' => 19.90,
                'duration' => '6 horas',
                'thumbnail' => 'course/branding-visual-para-emprendedores.svg',
                'category' => $categories[3],
                'level' => $levels[0],
                'language' => $languages[0],
                'curriculum' => [
                    [
                        'title' => 'Esencia de marca',
                        'lessons' => [
                            ['title' => 'Propuesta de valor y personalidad', 'duration' => '09:40', 'is_preview' => true],
                            ['title' => 'Moodboard y referencias visuales', 'duration' => '13:50', 'is_preview' => false],
                            ['title' => 'Paleta, tipografia y contraste', 'duration' => '15:00', 'is_preview' => false],
                        ],
                    ],
                    [
                        'title' => 'Sistema visual aplicable',
                        'lessons' => [
                            ['title' => 'Logo, variantes y usos correctos', 'duration' => '17:25', 'is_preview' => true],
                            ['title' => 'Plantillas para redes y presentaciones', 'duration' => '12:45', 'is_preview' => false],
                            ['title' => 'Mini manual de marca accionable', 'duration' => '10:15', 'is_preview' => false],
                        ],
                    ],
                ],
                'reviews' => [
                    ['email' => 'lucia.estudiante@lmsl13.test', 'rating' => 5, 'review' => 'Muy util para convertir ideas en una identidad coherente y presentable.'],
                    ['email' => 'carlos.estudiante@lmsl13.test', 'rating' => 4, 'review' => 'Buen curso corto y directo. Sirve para ordenar una marca sin complicarse.'],
                ],
                'progress' => [
                    'ana.estudiante@lmsl13.test' => 2,
                    'carlos.estudiante@lmsl13.test' => 4,
                    'lucia.estudiante@lmsl13.test' => 5,
                ],
            ],
            [
                'title' => 'SEO practico para vender mas',
                'slug' => 'seo-practico-para-vender-mas',
                'seo_description' => 'Mejora el posicionamiento organico de tu sitio con tecnicas accionables.',
                'description' => 'Keyword research, SEO on page, enlazado interno y medicion de resultados.',
                'price' => 54.90,
                'discount' => 34.90,
                'duration' => '9 horas',
                'thumbnail' => 'course/seo-practico-para-vender-mas.svg',
                'category' => $categories[4],
                'level' => $levels[1],
                'language' => $languages[1],
                'curriculum' => [
                    [
                        'title' => 'Bases del posicionamiento',
                        'lessons' => [
                            ['title' => 'Busqueda de keywords con intencion comercial', 'duration' => '15:25', 'is_preview' => true],
                            ['title' => 'Arquitectura web y paginas objetivo', 'duration' => '14:45', 'is_preview' => false],
                            ['title' => 'Titulos, metas y snippets mas atractivos', 'duration' => '12:35', 'is_preview' => false],
                            ['title' => 'Analisis de competencia y brechas', 'duration' => '13:20', 'is_preview' => false],
                        ],
                    ],
                    [
                        'title' => 'Optimización y medicion',
                        'lessons' => [
                            ['title' => 'Contenido optimizado para conversion', 'duration' => '18:20', 'is_preview' => true],
                            ['title' => 'Enlazado interno y autoridad topical', 'duration' => '16:10', 'is_preview' => false],
                            ['title' => 'KPIs, Search Console y mejora continua', 'duration' => '15:40', 'is_preview' => false],
                            ['title' => 'SEO tecnico basico para rendimiento', 'duration' => '14:15', 'is_preview' => false],
                        ],
                    ],
                    [
                        'title' => 'Escalado de resultados SEO',
                        'lessons' => [
                            ['title' => 'Roadmap de 90 dias por prioridad', 'duration' => '11:45', 'is_preview' => true],
                            ['title' => 'Reportes para cliente o negocio', 'duration' => '10:50', 'is_preview' => false],
                            ['title' => 'Automatizacion de seguimiento semanal', 'duration' => '09:40', 'is_preview' => false],
                            ['title' => 'Checklist final de auditoria SEO', 'duration' => '12:10', 'is_preview' => false],
                        ],
                    ],
                ],
                'reviews' => [
                    ['email' => 'carlos.estudiante@lmsl13.test', 'rating' => 5, 'review' => 'Bastante practico y orientado a resultados. Muy buena seccion de keywords.'],
                    ['email' => 'ana.estudiante@lmsl13.test', 'rating' => 4, 'review' => 'Me dio una estructura clara para optimizar contenido y medir avances.'],
                ],
                'progress' => [
                    'ana.estudiante@lmsl13.test' => 4,
                    'carlos.estudiante@lmsl13.test' => 6,
                    'lucia.estudiante@lmsl13.test' => 2,
                ],
            ],
            [
                'title' => 'Estrategia de redes sociales 2026',
                'slug' => 'estrategia-de-redes-sociales-2026',
                'seo_description' => 'Planifica contenidos, anuncios y crecimiento para redes sociales.',
                'description' => 'Crea una estrategia integral de contenido, campanas y analitica para redes.',
                'price' => 29.90,
                'discount' => 0,
                'duration' => '7 horas',
                'thumbnail' => 'course/estrategia-de-redes-sociales-2026.svg',
                'category' => $categories[5],
                'level' => $levels[2],
                'language' => $languages[0],
                'curriculum' => [
                    [
                        'title' => 'Diseño de la estrategia',
                        'lessons' => [
                            ['title' => 'Objetivos, audiencias y mensajes', 'duration' => '11:35', 'is_preview' => true],
                            ['title' => 'Pilares de contenido y frecuencia', 'duration' => '14:50', 'is_preview' => false],
                            ['title' => 'Calendario editorial reutilizable', 'duration' => '13:15', 'is_preview' => false],
                            ['title' => 'Framework de ganchos y formatos', 'duration' => '12:25', 'is_preview' => false],
                        ],
                    ],
                    [
                        'title' => 'Campañas y analitica',
                        'lessons' => [
                            ['title' => 'Creatividades y anuncios que convierten', 'duration' => '16:40', 'is_preview' => true],
                            ['title' => 'Interpretacion de metricas clave', 'duration' => '12:30', 'is_preview' => false],
                            ['title' => 'Escalado y mejora mensual', 'duration' => '11:55', 'is_preview' => false],
                            ['title' => 'Distribucion de presupuesto por objetivo', 'duration' => '10:35', 'is_preview' => false],
                        ],
                    ],
                    [
                        'title' => 'Operacion del equipo social',
                        'lessons' => [
                            ['title' => 'Flujo de aprobacion y roles', 'duration' => '09:55', 'is_preview' => true],
                            ['title' => 'Banco de contenidos reutilizables', 'duration' => '08:40', 'is_preview' => false],
                            ['title' => 'Manejo de crisis y respuesta publica', 'duration' => '10:20', 'is_preview' => false],
                            ['title' => 'Tablero ejecutivo mensual', 'duration' => '09:10', 'is_preview' => false],
                        ],
                    ],
                ],
                'reviews' => [
                    ['email' => 'ana.estudiante@lmsl13.test', 'rating' => 5, 'review' => 'Muy aterrizado para ejecutar una estrategia semanal sin perder el foco.'],
                    ['email' => 'lucia.estudiante@lmsl13.test', 'rating' => 4, 'review' => 'Buen enfoque en contenido y medicion. Se entiende rapido y aporta plantillas mentales.'],
                ],
                'progress' => [
                    'ana.estudiante@lmsl13.test' => 6,
                    'carlos.estudiante@lmsl13.test' => 3,
                    'lucia.estudiante@lmsl13.test' => 5,
                ],
            ],
        ];

        foreach ($courses as $courseData) {
            $course = Course::query()->updateOrCreate(
                ['slug' => $courseData['slug']],
                [
                    'instructor_id' => $instructorId,
                    'category_id' => $courseData['category']->id,
                    'course_level_id' => $courseData['level']->id,
                    'course_language_id' => $courseData['language']->id,
                    'title' => $courseData['title'],
                    'seo_description' => $courseData['seo_description'],
                    'thumbnail' => $courseData['thumbnail'],
                    'description' => $courseData['description'],
                    // LMS GRATIS: todos los cursos demo se siembran sin coste (no hay pasarela de pago en FREE).
                    'price' => 0,
                    'discount' => 0,
                    'duration' => $courseData['duration'],
                    'certificate' => true,
                    'qna' => true,
                    'is_approved' => 'approved',
                    'status' => 'active',
                ]
            );

            CourseChapter::query()->where('course_id', $course->id)->delete();

            foreach ($courseData['curriculum'] as $chapterIndex => $chapterData) {
                $chapter = CourseChapter::query()->create([
                    'course_id' => $course->id,
                    'instructor_id' => $instructorId,
                    'title' => $chapterData['title'],
                    'order' => $chapterIndex + 1,
                    'status' => true,
                ]);

                foreach ($chapterData['lessons'] as $lessonIndex => $lessonData) {
                    CourseChapterLesson::query()->create([
                        'title' => $lessonData['title'],
                        'slug' => Str::slug($course->title . ' ' . $chapterData['title'] . ' ' . $lessonData['title']),
                        'description' => 'Leccion de muestra para el curso ' . $course->title . '.',
                        'instructor_id' => $instructorId,
                        'course_id' => $course->id,
                        'chapter_id' => $chapter->id,
                        'file_path' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                        'storage' => 'youtube',
                        'duration' => $lessonData['duration'],
                        'file_type' => 'video',
                        'downloadable' => false,
                        'order' => $lessonIndex + 1,
                        'is_preview' => $lessonData['is_preview'],
                        'status' => true,
                    ]);
                }
            }

            foreach ($courseData['reviews'] as $reviewData) {
                $studentId = $studentIds[$reviewData['email']] ?? null;

                if (! $studentId) {
                    continue;
                }

                Enrollment::query()->updateOrCreate(
                    [
                        'user_id' => $studentId,
                        'course_id' => $course->id,
                    ],
                    [
                        'instructor_id' => $instructorId,
                        'have_access' => true,
                    ]
                );

                CourseReview::query()->updateOrCreate(
                    [
                        'course_id' => $course->id,
                        'user_id' => $studentId,
                    ],
                    [
                        'rating' => $reviewData['rating'],
                        'review' => $reviewData['review'],
                    ]
                );
            }

            $lessonIds = CourseChapterLesson::query()
                ->where('course_id', $course->id)
                ->orderBy('order')
                ->pluck('id')
                ->values();

            foreach ($courseData['progress'] as $email => $completedLessonsCount) {
                $studentId = $studentIds[$email] ?? null;

                if (! $studentId) {
                    continue;
                }

                Enrollment::query()->updateOrCreate(
                    [
                        'user_id' => $studentId,
                        'course_id' => $course->id,
                    ],
                    [
                        'instructor_id' => $instructorId,
                        'have_access' => true,
                    ]
                );

                LessonCompletion::query()
                    ->where('user_id', $studentId)
                    ->where('course_id', $course->id)
                    ->delete();

                WatchHistory::query()
                    ->where('user_id', $studentId)
                    ->where('course_id', $course->id)
                    ->delete();

                $watchCount = min($lessonIds->count(), max($completedLessonsCount, min($lessonIds->count(), 2)));
                $completedLessonIds = $lessonIds->take(min($completedLessonsCount, $lessonIds->count()));

                $lastWatchedLessonId = $lessonIds->take($watchCount)->last();

                if ($lastWatchedLessonId) {
                    WatchHistory::query()->updateOrCreate(
                        [
                            'user_id' => $studentId,
                            'course_id' => $course->id,
                        ],
                        [
                            'lesson_id' => $lastWatchedLessonId,
                        ]
                    );
                }

                foreach ($completedLessonIds as $lessonId) {
                    LessonCompletion::query()->create([
                        'user_id' => $studentId,
                        'course_id' => $course->id,
                        'lesson_id' => $lessonId,
                    ]);
                }
            }
        }
    }
}