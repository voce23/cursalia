<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Frontend\BlogCommentStoreRequest;
use App\Models\Blog;
use Illuminate\Http\RedirectResponse;

class BlogCommentController extends Controller
{
    public function store(BlogCommentStoreRequest $request, Blog $blog): RedirectResponse
    {
        $blog->comments()->create([
            'name' => $request->name,
            'email' => $request->email,
            'comment' => $request->comment,
            'is_approved' => false,
            'approved_at' => null,
        ]);

        return back()->with('success', 'Tu comentario fue enviado y quedará pendiente de aprobación.');
    }
}
