<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Frontend\LessonCommentStoreRequest;
use App\Models\CourseChapterLesson;
use Illuminate\Http\RedirectResponse;

class LessonCommentController extends Controller
{
    public function store(LessonCommentStoreRequest $request, CourseChapterLesson $lesson): RedirectResponse
    {
        $lesson->comments()->create([
            'name' => $request->name,
            'email' => $request->email,
            'comment' => $request->comment,
            'is_approved' => false,
            'approved_at' => null,
        ]);

        return back()->with('success', 'Tu comentario fue enviado y quedará pendiente de aprobación.');
    }
}
