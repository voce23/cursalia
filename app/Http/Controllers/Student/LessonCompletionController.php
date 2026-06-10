<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseChapterLesson;
use App\Models\LessonCompletion;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LessonCompletionController extends Controller
{
    public function toggle(Request $request, Course $course, CourseChapterLesson $lesson): JsonResponse
    {
        $user = Auth::user();

        // Verificar acceso al curso
        $hasAccess = $user->enrollments()
            ->where('course_id', $course->id)
            ->where('have_access', true)
            ->exists();

        abort_unless($hasAccess, 403);

        // Verificar que la leccion pertenece al curso
        abort_unless($lesson->course_id === $course->id, 403);

        $existing = LessonCompletion::where('user_id', $user->id)
            ->where('lesson_id', $lesson->id)
            ->first();

        if ($existing) {
            $existing->delete();
            $completed = false;
        } else {
            LessonCompletion::create([
                'user_id' => $user->id,
                'course_id' => $course->id,
                'lesson_id' => $lesson->id,
            ]);
            $completed = true;
        }

        $totalLessons = CourseChapterLesson::where('course_id', $course->id)->count();
        $completedCount = LessonCompletion::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->count();
        $progress = $totalLessons > 0 ? round($completedCount / $totalLessons * 100) : 0;

        return response()->json([
            'completed' => $completed,
            'progress' => $progress,
            'completedCount' => $completedCount,
            'totalLessons' => $totalLessons,
        ]);
    }
}
