<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogComment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BlogCommentController extends Controller
{
    public function index(Request $request): View
    {
        $tab = $request->string('tab')->toString() ?: 'pending';

        $items = BlogComment::query()
            ->with('blog:id,title,slug')
            ->when($tab === 'pending',  fn ($q) => $q->where('is_approved', false))
            ->when($tab === 'approved', fn ($q) => $q->where('is_approved', true))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $counts = [
            'pending'  => BlogComment::where('is_approved', false)->count(),
            'approved' => BlogComment::where('is_approved', true)->count(),
        ];

        return view('admin.blog-comments.index', compact('items', 'counts', 'tab'));
    }

    public function approve(BlogComment $blogComment): RedirectResponse
    {
        $blogComment->update(['is_approved' => true, 'approved_at' => now()]);
        return back()->with('success', 'Comentario aprobado.');
    }

    public function destroy(BlogComment $blogComment): JsonResponse
    {
        $blogComment->delete();
        return response()->json(['message' => 'Comentario eliminado.']);
    }
}
