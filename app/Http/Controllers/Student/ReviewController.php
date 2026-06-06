<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Http\Requests\Frontend\CourseReviewRequest;
use App\Models\CourseReview;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ReviewController extends Controller
{
    public function index(): View
    {
        $reviews = CourseReview::query()
            ->where('user_id', auth()->id())
            ->with('course:id,title,slug,thumbnail')
            ->latest()
            ->paginate(10);

        return view('student.reviews.index', compact('reviews'));
    }

    public function edit(CourseReview $review): View
    {
        abort_unless($review->user_id === auth()->id(), 403);

        $review->load('course:id,title,slug,thumbnail');

        return view('student.reviews.edit', compact('review'));
    }

    public function update(CourseReviewRequest $request, CourseReview $review): RedirectResponse
    {
        abort_unless($review->user_id === auth()->id(), 403);

        $review->update($request->validated());

        flash()->success('Reseña actualizada correctamente.');

        return redirect()->route('student.reviews.index');
    }
}
