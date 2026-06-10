<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\CourseChapterLesson;
use App\Models\Enrollment;
use App\Models\LessonCompletion;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

/**
 * Smoke test del progreso del estudiante + quiz mínimo (Cursalia FREE).
 * Verifica que un alumno inscrito puede: ver sus cursos, entrar al player,
 * marcar una lección como completada (AJAX), y enviar la autoevaluación.
 */
class QuizAndProgressTest extends TestCase
{
    use DatabaseTransactions;

    private function enrolledStudent(): array
    {
        // Tomar un curso con lecciones y un alumno; inscribirlo si hace falta.
        $lesson = CourseChapterLesson::query()->whereNotNull('course_id')->orderBy('id')->firstOrFail();
        $course = Course::findOrFail($lesson->course_id);

        $student = User::where('role', 'student')->first() ?? User::factory()->create(['role' => 'student']);

        Enrollment::firstOrCreate(
            ['user_id' => $student->id, 'course_id' => $course->id],
            ['instructor_id' => $course->instructor_id, 'have_access' => true]
        );

        return [$student, $course, $lesson];
    }

    public function test_enrolled_courses_page_responds(): void
    {
        [$student] = $this->enrolledStudent();

        $this->actingAs($student)
            ->get(route('student.enrolled-courses.index'))
            ->assertOk()
            ->assertSee('Mis cursos', false);
    }

    public function test_player_shows_lesson_and_progress(): void
    {
        [$student, $course] = $this->enrolledStudent();

        $this->actingAs($student)
            ->get(route('student.player.show', $course))
            ->assertOk()
            ->assertSee('Contenido del curso', false)
            ->assertSee('Marcar como completada', false);
    }

    public function test_toggle_lesson_completion_updates_progress(): void
    {
        [$student, $course, $lesson] = $this->enrolledStudent();

        // Asegurar estado limpio
        LessonCompletion::where('user_id', $student->id)->where('lesson_id', $lesson->id)->delete();

        $res = $this->actingAs($student)
            ->post(route('student.player.lesson.toggle-complete', ['course' => $course->slug, 'lesson' => $lesson->id]));

        $res->assertOk()->assertJson(['completed' => true]);

        $this->assertDatabaseHas('lesson_completions', [
            'user_id' => $student->id,
            'lesson_id' => $lesson->id,
        ]);
    }

    public function test_quiz_submission_grades_automatically(): void
    {
        [$student, $course, $lesson] = $this->enrolledStudent();

        $quiz = Quiz::with('questions.options')->where('lesson_id', $lesson->id)->first();

        if (! $quiz) {
            $this->markTestSkipped('La lección no tiene quiz demo sembrado.');
        }

        // Construir respuestas CORRECTAS (la opción is_correct de cada pregunta).
        $answers = [];
        foreach ($quiz->questions as $q) {
            $correct = $q->options->firstWhere('is_correct', true);
            $answers[$q->id] = $correct?->id;
        }

        $this->actingAs($student)
            ->post(route('student.quiz.submit', $quiz), ['answers' => $answers])
            ->assertRedirect();

        $attempt = QuizAttempt::where('quiz_id', $quiz->id)->where('user_id', $student->id)->latest()->first();
        $this->assertNotNull($attempt);
        $this->assertEquals(100.0, (float) $attempt->percentage, 'Respuestas correctas deben dar 100%');
        $this->assertTrue((bool) $attempt->passed);
    }
}
