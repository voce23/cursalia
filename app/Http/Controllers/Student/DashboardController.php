<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\CourseReview;
use App\Models\Enrollment;
use App\Models\LessonCompletion;
use App\Models\Order;
use App\Notifications\NewInstructorRequestNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $uid = Auth::id();

        $enrolledCourses = Cache::remember("student.{$uid}.enrolled_count", 300, fn () =>
            Enrollment::query()->where('user_id', $uid)->where('have_access', true)->count()
        );
        $totalReviews = Cache::remember("student.{$uid}.reviews_count", 300, fn () =>
            CourseReview::query()->where('user_id', $uid)->count()
        );
        $totalOrders = Cache::remember("student.{$uid}.orders_count", 300, fn () =>
            Order::query()->where('buyer_id', $uid)->count()
        );
        $recentOrders = Order::query()
            ->where('buyer_id', $uid)
            ->latest()
            ->take(5)
            ->get(['id', 'invoice_id', 'paid_amount', 'currency', 'status', 'created_at']);

        $recentCourses = Enrollment::query()
            ->where('user_id', Auth::id())
            ->where('have_access', true)
            ->with([
                'course' => function ($query) {
                    $query->select('id', 'title', 'slug', 'thumbnail', 'instructor_id', 'certificate')
                        ->withCount('lessons')
                        ->with('instructor:id,name');
                },
            ])
            ->latest()
            ->take(3)
            ->get();

        $courseIds = $recentCourses->pluck('course_id')->filter()->all();

        $completedByCourse = LessonCompletion::query()
            ->where('user_id', Auth::id())
            ->whereIn('course_id', $courseIds)
            ->selectRaw('course_id, COUNT(*) as completed_count')
            ->groupBy('course_id')
            ->get()
            ->keyBy('course_id');

        $allEnrolledCourseIds = Enrollment::query()
            ->where('user_id', Auth::id())
            ->where('have_access', true)
            ->pluck('course_id');

        $totalLessons = $allEnrolledCourseIds->isEmpty()
            ? 0
            : \App\Models\Course::query()->whereIn('id', $allEnrolledCourseIds)->withCount('lessons')->get()->sum('lessons_count');

        $completedLessons = $allEnrolledCourseIds->isEmpty()
            ? 0
            : LessonCompletion::query()
                ->where('user_id', Auth::id())
                ->whereIn('course_id', $allEnrolledCourseIds)
                ->count();

        $averageProgress = $totalLessons > 0
            ? (int) round(($completedLessons / $totalLessons) * 100)
            : 0;

        return view('student.dashboard', compact(
            'enrolledCourses',
            'totalReviews',
            'totalOrders',
            'recentOrders',
            'recentCourses',
            'completedByCourse',
            'averageProgress',
        ));
    }

    public function becomeInstructor(): View
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($user->instructor_request) {
            abort(403);
        }

        return view('student.become-instructor');
    }

    public function becomeInstructorStore(Request $request): RedirectResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $request->validate([
            'document' => ['required', 'file', 'mimes:pdf,doc,docx,jpg,png', 'max:12288'],
        ]);

        $path = $request->file('document')->store('documents', 'public');

        // Campos de privilegio: asignación explícita (fuera de $fillable)
        $user->forceFill([
            'instructor_request' => true,
            'approve_status'     => 'pending',
            'document'           => $path,
            'role'               => 'instructor',
        ])->save();

        // Notificar a todos los admins
        Admin::all()->each(fn ($admin) => $admin->notify(new NewInstructorRequestNotification($user)));

        return redirect()->route('student.dashboard')
            ->with('success', 'Tu solicitud fue enviada correctamente. Te notificaremos por email.');
    }
}
