<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\BlogCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class BlogController extends Controller
{
    public function index(Request $request): View
    {
        $categorySlug = $request->string('category')->toString();

        $blogs = Blog::query()
            ->where('status', 'published')
            ->whereNotNull('published_at')
            ->whereHas('category', fn ($query) => $query->where('status', true))
            ->with(['category', 'author'])
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->string('search')->toString();

                $query->where(function ($query) use ($search) {
                    $query->where('title', 'like', '%'.$search.'%')
                        ->orWhere('summary', 'like', '%'.$search.'%')
                        ->orWhere('content', 'like', '%'.$search.'%');
                });
            })
            ->when($categorySlug, function ($query) use ($categorySlug) {
                $query->whereHas('category', function ($query) use ($categorySlug) {
                    $query->where('slug', $categorySlug)
                        ->where('status', true);
                });
            })
            ->latest('published_at')
            ->paginate(9)
            ->withQueryString();

        $categories = Cache::remember('blog.categories_with_count', 3600, fn () => BlogCategory::query()
            ->where('status', true)
            ->whereHas('blogs', fn ($query) => $query
                ->where('status', 'published')
                ->whereNotNull('published_at')
            )
            ->orderBy('name')
            ->withCount(['blogs' => fn ($q) => $q->where('status', 'published')->whereNotNull('published_at')])
            ->get(['id', 'name', 'slug', 'color'])
            ->map(fn ($c) => [
                'name' => $c->name,
                'slug' => $c->slug,
                'color' => $c->color,
                'blogs_count' => $c->blogs_count,
            ])
            ->all()
        );

        return view('frontend.pages.blog.index', compact('blogs', 'categories'));
    }

    public function show(string $slug): View
    {
        $blog = Blog::query()
            ->where('slug', $slug)
            ->where('status', 'published')
            ->whereNotNull('published_at')
            ->whereHas('category', fn ($query) => $query->where('status', true))
            ->with(['category', 'author'])
            ->firstOrFail();

        $approvedComments = $blog->comments()
            ->where('is_approved', true)
            ->latest('approved_at')
            ->paginate(15)
            ->withQueryString();

        $recentBlogs = Blog::query()
            ->where('status', 'published')
            ->whereNotNull('published_at')
            ->whereHas('category', fn ($query) => $query->where('status', true))
            ->where('id', '!=', $blog->id)
            ->with(['category', 'author'])
            ->latest('published_at')
            ->take(4)
            ->get();

        $categories = Cache::remember('blog.categories_with_count', 3600, fn () => BlogCategory::query()
            ->where('status', true)
            ->whereHas('blogs', fn ($query) => $query
                ->where('status', 'published')
                ->whereNotNull('published_at')
            )
            ->orderBy('name')
            ->withCount(['blogs' => fn ($q) => $q->where('status', 'published')->whereNotNull('published_at')])
            ->get(['id', 'name', 'slug', 'color'])
            ->map(fn ($c) => [
                'name' => $c->name,
                'slug' => $c->slug,
                'color' => $c->color,
                'blogs_count' => $c->blogs_count,
            ])
            ->all()
        );

        return view('frontend.pages.blog.show', compact('blog', 'recentBlogs', 'categories', 'approvedComments'));
    }
}
