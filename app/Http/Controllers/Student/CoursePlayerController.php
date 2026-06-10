<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\LessonCompletion;
use App\Models\LessonQuestion;
use App\Models\Quiz;
use App\Models\WatchHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CoursePlayerController extends Controller
{
    public function show(Course $course, Request $request): View
    {
        $user = Auth::user();

        $hasAccess = $user->enrollments()
            ->where('course_id', $course->id)
            ->where('have_access', true)
            ->exists();

        abort_unless($hasAccess, 403);

        $chapters = $course->chapters()
            ->orderBy('order')
            ->with(['lessons' => fn ($q) => $q->orderBy('order')])
            ->get();

        // Aplanar lecciones una sola vez y crear un mapa id→índice para búsqueda O(1)
        $allLessons = $chapters->flatMap(fn ($ch) => $ch->lessons)->values();
        $lessonIndex = $allLessons->pluck(null, 'id')->keys()->flip()->toArray();

        // -- Resolver leccion actual (sin queries extra: usar colección en memoria) --
        $lessonId = $request->query('lesson');
        $currentLesson = null;

        if ($lessonId) {
            $currentLesson = $allLessons->firstWhere('id', (int) $lessonId);
        }

        // Si no hay leccion en URL, retomar desde el historial
        if (! $currentLesson) {
            $history = WatchHistory::where('user_id', $user->id)
                ->where('course_id', $course->id)
                ->first();

            if ($history) {
                $currentLesson = $allLessons->firstWhere('id', $history->lesson_id);
            }
        }

        // Default: primera leccion del primer capitulo
        if (! $currentLesson && $allLessons->isNotEmpty()) {
            $currentLesson = $allLessons->first();
        }

        // -- Anterior / Siguiente (búsqueda O(1) con mapa de índices) ---------------
        $currentIndex = $currentLesson ? ($lessonIndex[$currentLesson->id] ?? false) : false;
        $prevLesson = ($currentIndex !== false && $currentIndex > 0)
            ? $allLessons[$currentIndex - 1] : null;
        $nextLesson = ($currentIndex !== false && $currentIndex < $allLessons->count() - 1)
            ? $allLessons[$currentIndex + 1] : null;

        // -- Registrar historial de reproduccion ---------------------
        if ($currentLesson) {
            WatchHistory::updateOrCreate(
                ['user_id' => $user->id, 'course_id' => $course->id],
                ['lesson_id' => $currentLesson->id]
            );
        }

        // -- IDs de lecciones completadas ----------------------------
        $completedLessonIds = LessonCompletion::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->pluck('lesson_id')
            ->toArray();

        $isCurrentLessonCompleted = $currentLesson
            ? in_array($currentLesson->id, $completedLessonIds)
            : false;

        // -- % de progreso --------------------------------------------
        $totalLessons = $allLessons->count();
        $progress = $totalLessons > 0
            ? round(count($completedLessonIds) / $totalLessons * 100)
            : 0;

        // -- Q&A de la lección actual -------------------------
        $questions = collect();
        if ($currentLesson && $course->qna) {
            $questions = LessonQuestion::where('lesson_id', $currentLesson->id)
                ->with(['user:id,name,image,role', 'answers'])
                ->latest()
                ->get();
        }

        // -- Quiz (autoevaluación) de la lección actual · FREE mínimo ----------
        //    Solo si la lección tiene un quiz activo. Cargamos preguntas+opciones
        //    y el último intento del alumno para decidir si mostrar el formulario
        //    o el resultado.
        $quiz = null;
        $lastAttempt = null;
        if ($currentLesson) {
            $quiz = Quiz::with(['questions' => fn ($q) => $q->orderBy('order'), 'questions.options' => fn ($q) => $q->orderBy('order')])
                ->where('lesson_id', $currentLesson->id)
                ->where('status', true)
                ->first();

            if ($quiz) {
                $lastAttempt = $quiz->attempts()
                    ->where('user_id', $user->id)
                    ->whereNotNull('completed_at')
                    ->with('answers')
                    ->latest('completed_at')
                    ->first();
            }
        }

        return view('student.player.show', compact(
            'course',
            'chapters',
            'currentLesson',
            'prevLesson',
            'nextLesson',
            'completedLessonIds',
            'isCurrentLessonCompleted',
            'progress',
            'totalLessons',
            'questions',
            'quiz',
            'lastAttempt',
        ));
    }
}
