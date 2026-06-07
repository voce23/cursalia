<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * Quiz · autoevaluación FREE (mínimo).
 *
 * Solo procesa preguntas autocorregibles (opción múltiple y verdadero/falso).
 * NO emite certificados (eso es PRO). El objetivo del quiz en FREE es que el
 * alumno compruebe si entendió la lección, con feedback y explicaciones.
 */
class QuizController extends Controller
{
    public function submit(Request $request, Quiz $quiz): RedirectResponse
    {
        $user = Auth::user();

        // Cargar la lección y el curso del quiz para verificar acceso e inscripción.
        $quiz->loadMissing(['lesson.course', 'questions.options']);
        $lesson = $quiz->lesson;
        abort_if(! $lesson, 404);

        $course   = $lesson->course;
        $courseId = $lesson->course_id;
        abort_if(! $course, 404);

        $hasAccess = $user->enrollments()
            ->where('course_id', $courseId)
            ->where('have_access', true)
            ->exists();

        abort_unless($hasAccess, 403, 'No estás inscrito en este curso.');

        // Límite de intentos (si no se permiten reintentos o se alcanzó el máximo).
        $previousAttempts = QuizAttempt::where('quiz_id', $quiz->id)
            ->where('user_id', $user->id)
            ->whereNotNull('completed_at')
            ->count();

        if (! $quiz->allow_retakes && $previousAttempts >= 1) {
            return back()->with('error', 'Ya completaste esta autoevaluación.');
        }
        if ($quiz->allow_retakes && $quiz->max_attempts > 0 && $previousAttempts >= $quiz->max_attempts) {
            return back()->with('error', 'Has alcanzado el máximo de intentos para esta autoevaluación.');
        }

        $answers = $request->input('answers', []); // [question_id => optionId | 'true'/'false' | texto]

        $attempt = DB::transaction(function () use ($quiz, $user, $answers, $request) {
            $attempt = QuizAttempt::create([
                'user_id'      => $user->id,
                'quiz_id'      => $quiz->id,
                'started_at'   => now(),
                'completed_at' => now(),
                'ip_address'   => $request->ip(),
                'user_agent'   => substr((string) $request->userAgent(), 0, 500),
            ]);

            $totalPoints  = 0;
            $earnedPoints = 0;

            foreach ($quiz->questions as $question) {
                $userAnswer = $answers[$question->id] ?? null;
                $isCorrect  = null;
                $points     = 0;

                if (in_array($question->question_type, ['multiple_choice', 'true_false'], true)) {
                    $totalPoints += $question->points;

                    if ($question->question_type === 'multiple_choice') {
                        $correctOption = $question->options->firstWhere('is_correct', true);
                        $isCorrect = $correctOption && (int) $userAnswer === (int) $correctOption->id;
                    } else { // true_false: la opción correcta es la marcada is_correct
                        $correctOption = $question->options->firstWhere('is_correct', true);
                        $isCorrect = $correctOption && (int) $userAnswer === (int) $correctOption->id;
                    }

                    if ($isCorrect) {
                        $points = $question->points;
                        $earnedPoints += $points;
                    }
                }
                // essay / short_answer: is_correct = null (no autocorregible en FREE)

                $attempt->answers()->create([
                    'question_id'   => $question->id,
                    'answer'        => is_array($userAnswer) ? json_encode($userAnswer) : (string) $userAnswer,
                    'is_correct'    => $isCorrect,
                    'points_earned' => $points,
                ]);
            }

            $percentage = $totalPoints > 0 ? round($earnedPoints / $totalPoints * 100, 2) : 0;

            $attempt->update([
                'score'      => $earnedPoints,
                'percentage' => $percentage,
                'passed'     => $percentage >= $quiz->passing_score,
            ]);

            return $attempt;
        });

        return redirect()
            ->to(route('student.player.show', $course).'?lesson='.$lesson->id.'#quiz')
            ->with('quiz_submitted', $attempt->id);
    }
}
