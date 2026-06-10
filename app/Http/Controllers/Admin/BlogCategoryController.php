<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class BlogCategoryController extends Controller
{
    public function index(): View
    {
        $items = BlogCategory::query()
            ->withCount('blogs')
            ->orderBy('name')
            ->paginate(20);

        return view('admin.blog-categories.index', compact('items'));
    }

    public function create(): View
    {
        return view('admin.blog-categories.form', [
            'category' => new BlogCategory(['color' => '#10B981', 'status' => true]),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateRequest($request);
        $data['slug'] = $this->uniqueSlug($data['name']);
        $data['status'] = $request->boolean('status');
        BlogCategory::create($data);

        return to_route('admin.blog-categories.index')->with('success', 'Categoría creada.');
    }

    public function edit(BlogCategory $blogCategory): View
    {
        return view('admin.blog-categories.form', ['category' => $blogCategory]);
    }

    public function update(Request $request, BlogCategory $blogCategory): RedirectResponse
    {
        $data = $this->validateRequest($request);
        if ($data['name'] !== $blogCategory->name) {
            $data['slug'] = $this->uniqueSlug($data['name'], $blogCategory->id);
        }
        $data['status'] = $request->boolean('status');
        $blogCategory->update($data);

        return to_route('admin.blog-categories.index')->with('success', 'Categoría actualizada.');
    }

    public function destroy(BlogCategory $blogCategory): JsonResponse
    {
        if ($blogCategory->blogs()->exists()) {
            return response()->json(['message' => 'No se puede eliminar: hay artículos en esta categoría.'], 409);
        }
        $blogCategory->delete();

        return response()->json(['message' => 'Categoría eliminada.']);
    }

    private function validateRequest(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'color' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'status' => ['nullable', 'boolean'],
        ]);
    }

    private function uniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name);
        $slug = $base;
        $i = 1;
        while (BlogCategory::query()->where('slug', $slug)->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))->exists()) {
            $slug = $base.'-'.$i++;
        }

        return $slug;
    }
}
