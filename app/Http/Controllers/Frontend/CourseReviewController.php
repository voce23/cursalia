<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Frontend\CourseReviewRequest;
use App\Models\Course;
use App\Models\CourseReview;
use App\Models\Enrollment;
use Illuminate\Http\RedirectResponse;

class CourseReviewController extends Controller
{
    public function store(CourseReviewRequest $request, Course $course): RedirectResponse
    {
        $enrolled = Enrollment::query()
            ->where('user_id', auth()->id())
            ->where('course_id', $course->id)
            ->where('have_access', true)
            ->exists();

        abort_unless($enrolled, 403);

        CourseReview::updateOrCreate(
            [
                'course_id' => $course->id,
                'user_id' => auth()->id(),
            ],
            $request->validated()
        );

        flash()->success('Tu reseña fue guardada correctamente.');

        return back();
    }
}
