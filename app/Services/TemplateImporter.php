<?php

namespace App\Services;

use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\CourseChapter;
use App\Models\CourseChapterLesson;
use App\Models\CourseLanguage;
use App\Models\CourseLevel;
use App\Models\CourseReview;
use App\Models\Enrollment;
use App\Models\Quiz;
use App\Models\QuizQuestion;
use App\Models\QuizQuestionOption;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

/**
 * Importa una PLANTILLA de cursos (archivo .json) al LMS: crea categoría,
 * cursos, módulos y lecciones. Pensado para el botón "Importar plantilla"
 * del panel (Opción 2). Idempotente por slug. Seguro: la miniatura la
 * GENERA este servicio (el archivo NO trae SVG/markup arbitrario).
 */
class TemplateImporter
{
    /** Paletas de color para las miniaturas generadas (cíclicas por curso). */
    private const PALETTE = [
        ['#34d399', '#059669'], ['#fbbf24', '#d97706'], ['#f472b6', '#be185d'],
        ['#60a5fa', '#1d4ed8'], ['#818cf8', '#4338ca'], ['#2dd4bf', '#0f766e'],
        ['#c084fc', '#7e22ce'], ['#f87171', '#b91c1c'], ['#fb923c', '#c2410c'],
        ['#22d3ee', '#0e7490'],
    ];

    /**
     * Importa los datos ya decodificados de una plantilla.
     * Devuelve un resumen: ['name', 'courses', 'lessons'].
     *
     * @param array<string,mixed> $data
     */
    public function import(array $data, bool $replaceDemo = false): array
    {
        $this->assertValid($data);

        return DB::transaction(function () use ($data, $replaceDemo) {
            // Si se pidió, borrar los cursos y blog de EJEMPLO del LMS gratis
            // para que quede solo el contenido de esta plantilla.
            if ($replaceDemo) {
                $this->wipeExistingContent();
            }

            $instructorId = $this->instructorId();
            $this->applyInstructor($instructorId, $data['instructor'] ?? null);

            $cat = $data['category'] ?? [];
            $category = CourseCategory::query()->updateOrCreate(
                ['slug' => $cat['slug'] ?? Str::slug($data['name'] ?? 'plantilla')],
                [
                    'name' => $cat['name'] ?? ($data['name'] ?? 'Plantilla'),
                    'image' => $this->thumbnail('cat-'.($cat['slug'] ?? 'plantilla'), $cat['name'] ?? 'Plantilla', 0),
                    'status' => true,
                ]
            );

            $level = CourseLevel::query()->firstOrCreate(
                ['slug' => Str::slug($data['level'] ?? 'Principiante')],
                ['name' => $data['level'] ?? 'Principiante']
            );
            $language = CourseLanguage::query()->firstOrCreate(
                ['slug' => Str::slug($data['language'] ?? 'Español')],
                ['name' => $data['language'] ?? 'Español']
            );

            $courseCount = 0;
            $lessonCount = 0;
            $reviewCount = 0;
            $quizCount = 0;

            foreach (array_values($data['courses']) as $i => $c) {
                $slug = $c['slug'] ?? Str::slug($c['title']);

                $course = Course::query()->updateOrCreate(
                    ['slug' => $slug],
                    [
                        'instructor_id' => $instructorId,
                        'category_id' => $category->id,
                        'course_level_id' => $level->id,
                        'course_language_id' => $language->id,
                        'title' => $c['title'],
                        'seo_description' => $c['seo_description'] ?? Str::limit(strip_tags((string) ($c['description'] ?? $c['title'])), 150),
                        'thumbnail' => $this->thumbnail($slug, $c['title'], $i),
                        'demo_video_storage' => ! empty($c['intro_video']) ? $this->videoMeta($c['intro_video'])[0] : null,
                        'demo_video_source' => $c['intro_video'] ?? null,
                        'description' => $c['description'] ?? '',
                        'price' => 0,
                        'discount' => 0,
                        'duration' => $c['duration'] ?? 'A tu ritmo',
                        'certificate' => true,
                        'qna' => true,
                        'is_approved' => 'approved',
                        'status' => 'active',
                    ]
                );

                CourseChapter::query()->where('course_id', $course->id)->delete();

                foreach (array_values($c['modules'] ?? []) as $mi => $module) {
                    $chapter = CourseChapter::query()->create([
                        'course_id' => $course->id,
                        'instructor_id' => $instructorId,
                        'title' => $module['title'],
                        'order' => $mi + 1,
                        'status' => true,
                    ]);

                    foreach (array_values($module['lessons'] ?? []) as $li => $lesson) {
                        $url = trim((string) ($lesson['video_url'] ?? ''));
                        $fileType = $lesson['file_type'] ?? 'video';
                        if ($url === '') {
                            $storage = 'external_link';
                        } elseif (str_starts_with($url, '/') || str_contains($url, '/storage/')) {
                            $storage = 'upload';
                        } else {
                            $storage = $this->videoMeta($url)[0];
                        }

                        $lessonModel = CourseChapterLesson::query()->create([
                            'title' => $lesson['title'],
                            'slug' => Str::slug($course->title.' '.$module['title'].' '.$lesson['title']).'-'.Str::random(4),
                            'description' => $lesson['description'] ?? null,
                            'instructor_id' => $instructorId,
                            'course_id' => $course->id,
                            'chapter_id' => $chapter->id,
                            'file_path' => $url ?: null,
                            'storage' => $storage,
                            'file_type' => $fileType,
                            'duration' => $lesson['duration'] ?? null,
                            'downloadable' => (bool) ($lesson['downloadable'] ?? false),
                            'order' => $li + 1,
                            'is_preview' => (bool) ($lesson['is_preview'] ?? false),
                            'status' => true,
                        ]);
                        $lessonCount++;

                        if (! empty($lesson['quiz']) && is_array($lesson['quiz'])) {
                            $this->createQuiz($lessonModel->id, $lesson['quiz']);
                            $quizCount++;
                        }
                    }
                }

                // Reseñas de ejemplo (prueba social): crea alumno + reseña.
                foreach (array_values($c['reviews'] ?? []) as $rev) {
                    $studentId = $this->studentId($rev['student_name'] ?? 'Alumno');
                    Enrollment::query()->updateOrCreate(
                        ['user_id' => $studentId, 'course_id' => $course->id],
                        ['instructor_id' => $instructorId, 'have_access' => true]
                    );
                    CourseReview::query()->updateOrCreate(
                        ['course_id' => $course->id, 'user_id' => $studentId],
                        ['rating' => (int) ($rev['rating'] ?? 5), 'review' => $rev['review'] ?? '']
                    );
                    $reviewCount++;
                }

                $courseCount++;
            }

            return [
                'name' => $data['name'] ?? 'Plantilla',
                'courses' => $courseCount,
                'lessons' => $lessonCount,
                'reviews' => $reviewCount,
                'quizzes' => $quizCount,
            ];
        });
    }

    /** Valida que el archivo sea una plantilla de Cursalia con la forma esperada. */
    private function assertValid(array $data): void
    {
        if (($data['cursalia_template'] ?? false) !== true) {
            throw new RuntimeException('El archivo no es una plantilla de Cursalia válida.');
        }
        if (! isset($data['courses']) || ! is_array($data['courses']) || $data['courses'] === []) {
            throw new RuntimeException('La plantilla no contiene cursos.');
        }
        foreach ($data['courses'] as $c) {
            if (! is_array($c) || empty($c['title'])) {
                throw new RuntimeException('Hay un curso sin título en la plantilla.');
            }
        }
    }

    /**
     * Borra TODO el contenido de cursos y blog existente (el de ejemplo del
     * LMS gratis) para dejar el sitio limpio antes de importar la plantilla.
     * Desactiva las claves foráneas para no chocar con datos relacionados.
     */
    private function wipeExistingContent(): void
    {
        $tables = [
            // Relacionados con lecciones/cursos
            'lesson_comments', 'lesson_question_answers', 'lesson_questions',
            'quiz_attempt_answers', 'quiz_attempts', 'quiz_question_options',
            'quiz_questions', 'quiz_options', 'quizzes',
            'lesson_completions', 'watch_histories', 'watch_history',
            'course_reviews', 'enrollments', 'orders', 'order_items',
            'course_chapter_lessons', 'course_chapters', 'courses',
            // Blog
            'blog_comments', 'blogs',
        ];

        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        foreach ($tables as $t) {
            if (Schema::hasTable($t)) {
                DB::table($t)->delete();
            }
        }
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    /** Reusa un instructor existente o crea uno genérico (la importación debe ser autosuficiente). */
    private function instructorId(): int
    {
        $id = DB::table('users')->where('role', 'instructor')->value('id');
        if ($id) {
            return (int) $id;
        }

        return (int) DB::table('users')->insertGetId([
            'name' => 'Equipo Docente',
            'email' => 'docente.plantillas@'.(parse_url((string) config('app.url'), PHP_URL_HOST) ?: 'cursalia.test'),
            'password' => Hash::make(Str::random(24)),
            'role' => 'instructor',
            'approve_status' => 'approved',
            'instructor_request' => true,
            'email_verified_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /** Actualiza el perfil del instructor (nombre/titular/bio) desde la plantilla. */
    private function applyInstructor(int $instructorId, ?array $info): void
    {
        if (! $info) {
            return;
        }
        $fields = array_filter([
            'name' => $info['name'] ?? null,
            'headline' => $info['headline'] ?? null,
            'bio' => $info['bio'] ?? null,
        ], fn ($v) => $v !== null && $v !== '');
        if ($fields) {
            $fields['updated_at'] = now();
            DB::table('users')->where('id', $instructorId)->update($fields);
        }
    }

    /** Reusa o crea un alumno de ejemplo por nombre (para las reseñas). */
    private function studentId(string $name): int
    {
        $host = parse_url((string) config('app.url'), PHP_URL_HOST) ?: 'cursalia.test';
        $email = Str::slug($name).'.alumno@'.$host;
        $id = DB::table('users')->where('email', $email)->value('id');
        if ($id) {
            return (int) $id;
        }

        return (int) DB::table('users')->insertGetId([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make(Str::random(24)),
            'role' => 'student',
            'approve_status' => 'approved',
            'email_verified_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /** Crea una autoevaluación (quiz) en una lección desde la plantilla. */
    private function createQuiz(int $lessonId, array $q): void
    {
        $quiz = Quiz::query()->create([
            'lesson_id' => $lessonId,
            'title' => $q['title'] ?? 'Autoevaluación',
            'description' => $q['description'] ?? null,
            'passing_score' => (int) ($q['passing_score'] ?? 60),
            'shuffle_questions' => false,
            'show_results_immediately' => true,
            'allow_retakes' => true,
            'max_attempts' => 3,
            'time_limit' => null,
            'status' => true,
        ]);

        foreach (array_values($q['questions'] ?? []) as $qi => $qq) {
            $question = QuizQuestion::query()->create([
                'quiz_id' => $quiz->id,
                'question' => $qq['question'],
                'question_type' => 'multiple_choice',
                'order' => $qi + 1,
                'required' => true,
                'points' => 1,
                'explanation' => $qq['explanation'] ?? null,
            ]);

            foreach (array_values($qq['options'] ?? []) as $oi => $opt) {
                QuizQuestionOption::query()->create([
                    'question_id' => $question->id,
                    'option_text' => $opt['text'] ?? '',
                    'is_correct' => (bool) ($opt['correct'] ?? false),
                    'order' => $oi + 1,
                ]);
            }
        }
    }

    /** storage + file_type según la URL de video. Sin video aún = modo "enlace externo" (Bunny.net), NUNCA subir archivo. */
    private function videoMeta(?string $url): array
    {
        $url = trim((string) $url);
        if ($url === '') {
            // Las plantillas NO suben videos al hosting: se enlazan (Bunny.net / YouTube).
            // Dejamos la lección lista en modo "enlace externo" para pegar la URL.
            return ['external_link', 'video'];
        }
        if (Str::contains($url, ['youtube.com', 'youtu.be'])) {
            return ['youtube', 'video'];
        }
        if (Str::contains($url, 'vimeo.com')) {
            return ['vimeo', 'video'];
        }

        // Bunny.net (b-cdn.net / mediadelivery.net), MP4 directo, etc.
        return ['external_link', 'video'];
    }

    /** Genera una miniatura SVG 16:9 (gradiente + ícono + título) y devuelve su ruta. SEGURO: markup propio. */
    private function thumbnail(string $slug, string $title, int $index): string
    {
        [$c1, $c2] = self::PALETTE[$index % count(self::PALETTE)];
        $safeTitle = htmlspecialchars(Str::limit($title, 42), ENT_QUOTES, 'UTF-8');

        $svg = <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 800 450">
  <defs><linearGradient id="g" x1="0" y1="0" x2="1" y2="1">
    <stop offset="0" stop-color="{$c1}"/><stop offset="1" stop-color="{$c2}"/>
  </linearGradient></defs>
  <rect width="800" height="450" fill="url(#g)"/>
  <g fill="#ffffff" opacity="0.16"><circle cx="660" cy="90" r="120"/><circle cx="120" cy="380" r="90"/></g>
  <text x="400" y="240" text-anchor="middle" font-family="Segoe UI, Arial, sans-serif" font-size="40" font-weight="800" fill="#ffffff">{$safeTitle}</text>
  <text x="400" y="290" text-anchor="middle" font-family="Segoe UI, Arial, sans-serif" font-size="20" font-weight="500" fill="#ffffff" opacity="0.85">Plantilla Cursalia</text>
</svg>
SVG;

        $path = 'course/'.$slug.'.svg';
        Storage::disk('public')->put($path, $svg);

        return $path;
    }
}
