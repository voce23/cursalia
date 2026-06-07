<?php

namespace Database\Seeders;

use App\Models\CourseChapterLesson;
use App\Models\Quiz;
use Illuminate\Database\Seeder;

/**
 * Crea una autoevaluación DEMO (Cursalia FREE) en la primera lección
 * disponible, para mostrar cómo funciona el quiz mínimo. Idempotente:
 * si ya existe un quiz en esa lección, no duplica.
 *
 * Ejecutar:  php artisan db:seed --class=CursaliaQuizDemoSeeder
 */
class CursaliaQuizDemoSeeder extends Seeder
{
    public function run(): void
    {
        // Primera lección de cualquier curso (la que verá el alumno al entrar).
        $lesson = CourseChapterLesson::query()->orderBy('course_id')->orderBy('order')->first();

        if (! $lesson) {
            $this->command->warn('  → No hay lecciones; no se creó quiz demo.');
            return;
        }

        if (Quiz::where('lesson_id', $lesson->id)->exists()) {
            $this->command->info('  ✓ La lección ya tiene quiz; no se duplica.');
            return;
        }

        $quiz = Quiz::create([
            'lesson_id'      => $lesson->id,
            'title'          => 'Autoevaluación rápida',
            'description'    => 'Comprueba lo que aprendiste en esta lección. No cuenta para ningún certificado.',
            'passing_score'  => 70,
            'allow_retakes'  => true,
            'max_attempts'   => 0, // ilimitado
            'status'         => true,
        ]);

        $questions = [
            [
                'q' => '¿Qué es un LMS?',
                'type' => 'multiple_choice',
                'explanation' => 'LMS = Learning Management System: una plataforma para crear, gestionar y entregar cursos.',
                'options' => [
                    ['Una plataforma para gestionar y entregar cursos online', true],
                    ['Un lenguaje de programación', false],
                    ['Un tipo de servidor web', false],
                    ['Una red social', false],
                ],
            ],
            [
                'q' => 'Cursalia te permite montar tu academia en tu propio dominio sin pagar mensualidades.',
                'type' => 'true_false',
                'explanation' => 'Correcto: Cursalia FREE es gratuito y se aloja en tu propio dominio.',
                'options' => [
                    ['Verdadero', true],
                    ['Falso', false],
                ],
            ],
            [
                'q' => '¿Para qué sirve marcar una lección como completada?',
                'type' => 'multiple_choice',
                'explanation' => 'Marcar lecciones actualiza tu barra de progreso del curso.',
                'options' => [
                    ['Para llevar el control de tu progreso en el curso', true],
                    ['Para borrar la lección', false],
                    ['Para descargar un certificado al instante', false],
                ],
            ],
        ];

        foreach ($questions as $qi => $q) {
            $question = $quiz->questions()->create([
                'question'      => $q['q'],
                'question_type' => $q['type'],
                'order'         => $qi,
                'required'      => true,
                'points'        => 1,
                'explanation'   => $q['explanation'],
            ]);

            foreach ($q['options'] as $oi => [$text, $correct]) {
                $question->options()->create([
                    'option_text' => $text,
                    'is_correct'  => $correct,
                    'order'       => $oi,
                ]);
            }
        }

        $this->command->info("  ✓ Quiz demo creado en lección #{$lesson->id} ({$lesson->title}) con 3 preguntas.");
    }
}
