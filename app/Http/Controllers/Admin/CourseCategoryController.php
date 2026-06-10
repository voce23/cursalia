<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CourseCategoryStoreRequest;
use App\Http\Requests\Admin\CourseCategoryUpdateRequest;
use App\Models\CourseCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Intervention\Image\Laravel\Facades\Image;

class CourseCategoryController extends Controller
{
    public function index(): View
    {
        $categories = CourseCategory::whereNull('parent_id')
            ->withCount('subcategories')
            ->latest()
            ->paginate(15);

        return view('admin.course.course-category.index', compact('categories'));
    }

    public function create(): View
    {
        return view('admin.course.course-category.create');
    }

    public function store(CourseCategoryStoreRequest $request): RedirectResponse
    {
        $filename = null;
        if ($request->hasFile('image')) {
            Storage::disk('public')->makeDirectory('category');
            $filename = 'category/'.uniqid('cat_').'.webp';
            Image::decode($request->file('image'))
                ->cover(600, 400)
                ->save(Storage::disk('public')->path($filename), 90);
        }

        CourseCategory::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'image' => $filename,
        ]);

        return to_route('admin.course-categories.index')
            ->with('success', 'Categoría "'.$request->name.'" creada correctamente.');
    }

    public function edit(CourseCategory $courseCategory): View
    {
        return view('admin.course.course-category.edit', compact('courseCategory'));
    }

    public function update(CourseCategoryUpdateRequest $request, CourseCategory $courseCategory): RedirectResponse
    {
        $data = [
            'name' => $request->name,
            'slug' => Str::slug($request->name),
        ];

        if ($request->hasFile('image')) {
            // Delete old image
            if ($courseCategory->image) {
                Storage::disk('public')->delete($courseCategory->image);
            }

            Storage::disk('public')->makeDirectory('category');
            $filename = 'category/'.uniqid('cat_').'.webp';

            Image::decode($request->file('image'))
                ->cover(600, 400)
                ->save(Storage::disk('public')->path($filename), 90);

            $data['image'] = $filename;
        }

        $courseCategory->update($data);

        return to_route('admin.course-categories.index')
            ->with('success', 'Categoría actualizada correctamente.');
    }

    public function destroy(CourseCategory $courseCategory): JsonResponse
    {
        if ($courseCategory->subcategories()->exists()) {
            return response()->json(
                ['message' => 'No se puede eliminar: esta categoría tiene subcategorías.'],
                409
            );
        }

        if ($courseCategory->courses()->exists()) {
            return response()->json(
                ['message' => 'No se puede eliminar: hay cursos en esta categoría.'],
                409
            );
        }

        // Delete image from disk
        if ($courseCategory->image) {
            Storage::disk('public')->delete($courseCategory->image);
        }

        $courseCategory->delete();

        return response()->json(['message' => 'Categoría eliminada correctamente.']);
    }
}
