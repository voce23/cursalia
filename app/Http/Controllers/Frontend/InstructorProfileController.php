<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\User;

class InstructorProfileController extends Controller
{
    public function show(string $username)
    {
        $instructor = User::where('name', $username)
            ->where('role', 'instructor')
            ->where('approve_status', 'approved')
            ->firstOrFail();

        $courses = $instructor->courses()
            ->where('is_approved', 'approved')
            ->where('status', 'active')
            ->with('category')
            ->withCount('lessons')
            ->withAvg('reviews', 'rating')
            ->latest()
            ->get();

        $totalStudents = $instructor->courses()
            ->where('is_approved', 'approved')
            ->withCount('enrollments')
            ->get()
            ->sum('enrollments_count');

        $avgRating = $courses->whereNotNull('reviews_avg_rating')->avg('reviews_avg_rating');

        return view('frontend.instructors.show', compact(
            'instructor',
            'courses',
            'totalStudents',
            'avgRating',
        ));
    }
}
