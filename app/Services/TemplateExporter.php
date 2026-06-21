<?php

namespace App\Services;

use App\Models\Course;
use App\Models\Quiz;

/**
 * Exporta el contenido de cursos del LMS a una PLANTILLA .json con el MISMO
 * formato que entiende TemplateImporter (estilo Divi: exportar / importar).
 * Pensado para que el dueño arme un sitio por nicho y lo convierta en una
 * plantilla vendible/reutilizable.
 *
 * Nota: las plantillas enlazan videos (YouTube/Bunny), no suben archivos; si
 * una lección tiene video subido al hosting, su URL no existirá en el destino.
 */
class TemplateExporter
{
    /** @return array<string,mixed> */
    public function export(?string $name = null): array
    {
        $courses = Course::query()
            ->where('status', 'active')
            ->with(['chapters.lessons', 'reviews.user', 'category', 'level', 'language'])
            ->orderBy('id')
            ->get();

        $first = $courses->first();
        $cat = $first?->category;

        $coursesData = $courses->map(fn (Course $c) => [
            'slug' => $c->slug,
            'title' => $c->title,
            'seo_description' => $c->seo_description,
            'description' => $c->description,
            'intro_video' => $c->demo_video_source,
            'duration' => $c->duration,
            'modules' => $c->chapters->sortBy('order')->values()->map(fn ($ch) => [
                'title' => $ch->title,
                'lessons' => $ch->lessons->sortBy('order')->values()->map(fn ($l) => $this->lessonData($l))->all(),
            ])->all(),
            'reviews' => $c->reviews->map(fn ($r) => [
                'student_name' => $r->user?->name ?? 'Alumno',
                'rating' => (int) $r->rating,
                'review' => (string) $r->review,
            ])->values()->all(),
        ])->all();

        return [
            'cursalia_template' => true,
            'exported_at' => now()->toDateTimeString(),
            'name' => $name ?: (config('app.name').' — plantilla'),
            'category' => [
                'slug' => $cat?->slug ?? 'plantilla',
                'name' => $cat?->name ?? 'Plantilla',
            ],
            'level' => $first?->level?->name ?? 'Principiante',
            'language' => $first?->language?->name ?? 'Español',
            'courses' => $coursesData,
        ];
    }

    /** Cuántos cursos/lecciones hay disponibles para exportar (para la UI). */
    public function counts(): array
    {
        $courses = Course::query()->where('status', 'active')->withCount('lessons')->get();

        return [
            'courses' => $courses->count(),
            'lessons' => $courses->sum('lessons_count'),
        ];
    }

    /** @return array<string,mixed> */
    private function lessonData($l): array
    {
        $lesson = [
            'title' => $l->title,
            'description' => $l->description,
            'video_url' => $l->file_path,
            'file_type' => $l->file_type,
            'duration' => $l->duration,
            'downloadable' => (bool) $l->downloadable,
            'is_preview' => (bool) $l->is_preview,
        ];

        $quiz = Quiz::query()->with('questions.options')->where('lesson_id', $l->id)->first();
        if ($quiz) {
            $lesson['quiz'] = [
                'title' => $quiz->title,
                'description' => $quiz->description,
                'passing_score' => (int) $quiz->passing_score,
                'questions' => $quiz->questions->sortBy('order')->values()->map(fn ($q) => [
                    'question' => $q->question,
                    'explanation' => $q->explanation,
                    'options' => $q->options->sortBy('order')->values()->map(fn ($o) => [
                        'text' => $o->option_text,
                        'correct' => (bool) $o->is_correct,
                    ])->all(),
                ])->all(),
            ];
        }

        return $lesson;
    }
}
