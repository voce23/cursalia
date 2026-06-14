<?php

namespace App\Console\Commands;

use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\CourseChapter;
use App\Models\CourseChapterLesson;
use App\Models\CourseLanguage;
use App\Models\CourseLevel;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

/**
 * Exporta los cursos de una categoría a un archivo de PLANTILLA (.json)
 * compatible con el importador del panel (admin → Importar plantilla).
 *
 * Así fabricas plantillas a partir de cursos reales que armes en tu LMS,
 * sin escribir el JSON a mano.
 *
 *   php artisan templates:export fabricacion-productos-limpieza
 *   php artisan templates:export {categoria-slug} --name="Mi plantilla" --out=ruta.json
 */
class ExportTemplateCommand extends Command
{
    protected $signature = 'templates:export
        {category : Slug de la categoría a exportar}
        {--name= : Nombre visible de la plantilla}
        {--out= : Ruta del archivo .json de salida}';

    protected $description = 'Exporta los cursos de una categoría a una plantilla .json importable.';

    public function handle(): int
    {
        $category = CourseCategory::query()->where('slug', $this->argument('category'))->first();
        if (! $category) {
            $this->error("No existe la categoría con slug «{$this->argument('category')}».");

            return self::FAILURE;
        }

        $courses = Course::query()->where('category_id', $category->id)->orderBy('id')->get();
        if ($courses->isEmpty()) {
            $this->error('Esa categoría no tiene cursos.');

            return self::FAILURE;
        }

        $first = $courses->first();
        $level = CourseLevel::query()->find($first->course_level_id);
        $language = CourseLanguage::query()->find($first->course_language_id);

        $template = [
            'cursalia_template' => true,
            'format_version' => '1.0',
            'name' => $this->option('name') ?: $category->name,
            'category' => ['name' => $category->name, 'slug' => $category->slug],
            'level' => $level->name ?? 'Principiante',
            'language' => $language->name ?? 'Español',
            'courses' => [],
        ];

        foreach ($courses as $course) {
            $modules = [];

            $chapters = CourseChapter::query()->where('course_id', $course->id)->orderBy('order')->get();
            foreach ($chapters as $chapter) {
                $lessons = CourseChapterLesson::query()
                    ->where('chapter_id', $chapter->id)
                    ->orderBy('order')
                    ->get();

                $modules[] = [
                    'title' => $chapter->title,
                    'lessons' => $lessons->map(fn ($l) => [
                        'title' => $l->title,
                        'description' => $l->description,
                        'is_preview' => (bool) $l->is_preview,
                        // Solo conservamos el enlace si NO es un archivo subido (las plantillas no llevan videos pesados).
                        'video_url' => ($l->file_path && $l->storage !== 'upload') ? $l->file_path : '',
                        'duration' => $l->duration,
                    ])->all(),
                ];
            }

            $template['courses'][] = [
                'title' => $course->title,
                'slug' => $course->slug,
                'seo_description' => $course->seo_description,
                'description' => $course->description,
                'duration' => $course->duration,
                'modules' => $modules,
            ];
        }

        $out = $this->option('out') ?: storage_path('app/templates/'.$category->slug.'.json');
        File::ensureDirectoryExists(dirname($out));
        File::put($out, json_encode($template, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

        $this->info('Plantilla exportada: '.count($template['courses']).' cursos.');
        $this->line('  → '.$out);

        return self::SUCCESS;
    }
}
