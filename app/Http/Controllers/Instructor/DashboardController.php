<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\OrderItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $uid = Auth::id();

        $pendingCourses = Cache::remember("instructor.{$uid}.courses.pending", 300, fn () =>
            Course::query()->where('instructor_id', $uid)->where('is_approved', 'pending')->count()
        );

        $approvedCourses = Cache::remember("instructor.{$uid}.courses.approved", 300, fn () =>
            Course::query()->where('instructor_id', $uid)->where('is_approved', 'approved')->count()
        );

        $totalEarnings = (float) Cache::remember("instructor.{$uid}.earnings", 300, fn () =>
            OrderItem::query()
                ->whereHas('course', fn ($q) => $q->where('instructor_id', $uid))
                ->sum('instructor_earning')
        );

        $totalStudents = Cache::remember("instructor.{$uid}.students", 300, fn () =>
            Enrollment::query()
                ->where('instructor_id', $uid)
                ->where('have_access', true)
                ->distinct('user_id')
                ->count('user_id')
        );

        $recentSales = OrderItem::query()
            ->whereHas('course', fn ($q) => $q->where('instructor_id', $uid))
            ->with(['course:id,title,instructor_id', 'order.customer:id,name,email'])
            ->latest()
            ->take(5)
            ->get();

        return view('instructor.dashboard', compact(
            'pendingCourses',
            'approvedCourses',
            'totalEarnings',
            'recentSales',
            'totalStudents',
        ));
    }
}
