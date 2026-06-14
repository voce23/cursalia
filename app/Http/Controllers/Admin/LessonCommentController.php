<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LessonComment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LessonCommentController extends Controller
{
    public function index(Request $request): View
    {
        $tab = $request->string('tab')->toString() ?: 'pending';

        $items = LessonComment::query()
            ->with('lesson:id,title,course_id')
            ->when($tab === 'pending', fn ($q) => $q->where('is_approved', false))
            ->when($tab === 'approved', fn ($q) => $q->where('is_approved', true))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $counts = [
            'pending' => LessonComment::where('is_approved', false)->count(),
            'approved' => LessonComment::where('is_approved', true)->count(),
        ];

        return view('admin.lesson-comments.index', compact('items', 'counts', 'tab'));
    }

    public function approve(LessonComment $lessonComment): RedirectResponse
    {
        $lessonComment->update(['is_approved' => true, 'approved_at' => now()]);

        return back()->with('success', 'Comentario aprobado.');
    }

    public function destroy(LessonComment $lessonComment): RedirectResponse
    {
        $lessonComment->delete();

        return back()->with('success', 'Comentario eliminado.');
    }
}
