<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CourseLevelRequest;
use App\Models\CourseLevel;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CourseLevelController extends Controller
{
    public function index(): View
    {
        $levels = CourseLevel::latest()->paginate(15);

        return view('admin.course.course-level.index', compact('levels'));
    }

    public function create(): View
    {
        return view('admin.course.course-level.create');
    }

    public function store(CourseLevelRequest $request): RedirectResponse
    {
        CourseLevel::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
        ]);

        flash()->success('Nivel creado exitosamente.');

        return to_route('admin.course-levels.index');
    }

    public function edit(CourseLevel $courseLevel): View
    {
        return view('admin.course.course-level.edit', compact('courseLevel'));
    }

    public function update(CourseLevelRequest $request, CourseLevel $courseLevel): RedirectResponse
    {
        $courseLevel->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
        ]);

        flash()->success('Nivel actualizado exitosamente.');

        return to_route('admin.course-levels.index');
    }

    public function destroy(CourseLevel $courseLevel): JsonResponse
    {
        if ($courseLevel->courses()->exists()) {
            return response()->json(
                ['message' => 'No se puede eliminar: hay cursos usando este nivel.'],
                409
            );
        }

        $courseLevel->delete();

        flash()->success('Nivel eliminado exitosamente.');

        return response()->json(['message' => 'Eliminado exitosamente.']);
    }
}
