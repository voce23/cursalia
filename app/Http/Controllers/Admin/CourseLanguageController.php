<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CourseLanguageRequest;
use App\Models\CourseLanguage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CourseLanguageController extends Controller
{
    public function index(): View
    {
        $languages = CourseLanguage::latest()->paginate(15);

        return view('admin.course.course-language.index', compact('languages'));
    }

    public function create(): View
    {
        return view('admin.course.course-language.create');
    }

    public function store(CourseLanguageRequest $request): RedirectResponse
    {
        CourseLanguage::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
        ]);

        flash()->success('Idioma creado exitosamente.');

        return to_route('admin.course-languages.index');
    }

    public function edit(CourseLanguage $courseLanguage): View
    {
        return view('admin.course.course-language.edit', compact('courseLanguage'));
    }

    public function update(CourseLanguageRequest $request, CourseLanguage $courseLanguage): RedirectResponse
    {
        $courseLanguage->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
        ]);

        flash()->success('Idioma actualizado exitosamente.');

        return to_route('admin.course-languages.index');
    }

    public function destroy(CourseLanguage $courseLanguage): JsonResponse
    {
        if ($courseLanguage->courses()->exists()) {
            return response()->json(
                ['message' => 'No se puede eliminar: hay cursos usando este idioma.'],
                409
            );
        }

        $courseLanguage->delete();

        flash()->success('Idioma eliminado exitosamente.');

        return response()->json(['message' => 'Eliminado exitosamente.']);
    }
}
