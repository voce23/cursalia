<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CourseSubCategoryStoreRequest;
use App\Http\Requests\Admin\CourseSubCategoryUpdateRequest;
use App\Models\CourseCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CourseSubCategoryController extends Controller
{
    public function index(CourseCategory $courseCategory): View
    {
        $subcategories = CourseCategory::where('parent_id', $courseCategory->id)
            ->latest()
            ->paginate(15);

        return view('admin.course.course-sub-category.index', compact('courseCategory', 'subcategories'));
    }

    public function create(CourseCategory $courseCategory): View
    {
        return view('admin.course.course-sub-category.create', compact('courseCategory'));
    }

    public function store(CourseSubCategoryStoreRequest $request, CourseCategory $courseCategory): RedirectResponse
    {
        CourseCategory::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'parent_id' => $courseCategory->id,
        ]);

        flash()->success('Subcategoría creada correctamente.');

        return to_route('admin.course-categories.subcategories.index', $courseCategory);
    }

    public function edit(CourseCategory $courseCategory, CourseCategory $subcategory): View
    {
        return view('admin.course.course-sub-category.edit', compact('courseCategory', 'subcategory'));
    }

    public function update(
        CourseSubCategoryUpdateRequest $request,
        CourseCategory $courseCategory,
        CourseCategory $subcategory,
    ): RedirectResponse {
        $subcategory->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
        ]);

        flash()->success('Subcategoría actualizada correctamente.');

        return to_route('admin.course-categories.subcategories.index', $courseCategory);
    }

    public function destroy(CourseCategory $courseCategory, CourseCategory $subcategory): JsonResponse
    {
        if ($subcategory->courses()->exists()) {
            return response()->json([
                'message' => 'No se puede eliminar: esta subcategoría tiene cursos asociados.',
            ], 409);
        }

        $subcategory->delete();

        return response()->json(['message' => 'Subcategoría eliminada correctamente.']);
    }
}
