<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Models\LessonCompletion;
use App\Models\WatchHistory;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class EnrolledCourseController extends Controller
{
    public function index(): View
    {
        $userId = Auth::id();

        $enrolledCourses = Enrollment::query()
            ->where('user_id', $userId)
            ->where('have_access', true)
            ->with([
                'course' => function ($query) {
                    $query->select('id', 'title', 'slug', 'thumbnail', 'instructor_id', 'price', 'discount')
                        ->withCount('lessons')
                        ->with('instructor:id,name');
                },
            ])
            ->latest()
            ->paginate(9);

        // Historial de reproduccion (ultima leccion vista por curso)
        $courseIds   = $enrolledCourses->pluck('course_id')->filter()->toArray();
        $watchHistories = WatchHistory::where('user_id', $userId)
            ->whereIn('course_id', $courseIds)
            ->get()
            ->keyBy('course_id');

        // Progreso por curso (lecciones completadas)
        $completions = LessonCompletion::where('user_id', $userId)
            ->whereIn('course_id', $courseIds)
            ->selectRaw('course_id, COUNT(*) as completed_count')
            ->groupBy('course_id')
            ->get()
            ->keyBy('course_id');

        return view('student.enrolled-courses.index', compact(
            'enrolledCourses',
            'watchHistories',
            'completions',
        ));
    }
}
