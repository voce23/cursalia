<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Instructor\ChapterStoreRequest;
use App\Http\Requests\Instructor\LessonStoreRequest;
use App\Models\Course;
use App\Models\CourseChapter;
use App\Models\CourseChapterLesson;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CourseContentController extends Controller
{
    public function index(Course $course): View
    {
        $course->load('chapters.lessons');

        return view('admin.course.content', compact('course'));
    }

    public function storeChapter(ChapterStoreRequest $request, Course $course): JsonResponse
    {
        $order = $course->chapters()->count() + 1;

        $chapter = CourseChapter::create([
            'course_id'     => $course->id,
            'instructor_id' => $course->instructor_id,
            'title'         => $request->title,
            'order'         => $order,
        ]);

        return response()->json([
            'message' => 'Capítulo creado correctamente.',
            'chapter' => $chapter,
        ]);
    }

    public function updateChapter(ChapterStoreRequest $request, CourseChapter $chapter): JsonResponse
    {
        $chapter->update(['title' => $request->title]);

        return response()->json([
            'message' => 'Capítulo actualizado correctamente.',
            'chapter' => $chapter,
        ]);
    }

    public function destroyChapter(CourseChapter $chapter): JsonResponse
    {
        $chapter->delete();

        return response()->json(['message' => 'Capítulo eliminado correctamente.']);
    }

    /* ─── Lecciones ─── */

    public function storeLesson(LessonStoreRequest $request, CourseChapter $chapter): JsonResponse
    {
        $order = $chapter->lessons()->count() + 1;

        $lesson = CourseChapterLesson::create([
            'title'         => $request->title,
            'slug'          => Str::slug($request->title) . '-' . Str::random(5),
            'description'   => $request->description,
            'instructor_id' => $chapter->instructor_id,
            'course_id'     => $chapter->course_id,
            'chapter_id'    => $chapter->id,
            'file_path'     => $request->file_path,
            'storage'       => $request->storage,
            'duration'      => $request->duration,
            'file_type'     => $request->file_type,
            'downloadable'  => $request->boolean('downloadable'),
            'is_preview'    => $request->boolean('is_preview'),
            'order'         => $order,
        ]);

        return response()->json([
            'message' => 'Lección creada correctamente.',
            'lesson'  => $lesson,
        ]);
    }

    public function updateLesson(LessonStoreRequest $request, CourseChapterLesson $lesson): JsonResponse
    {
        $lesson->update([
            'title'        => $request->title,
            'slug'         => Str::slug($request->title) . '-' . Str::random(5),
            'description'  => $request->description,
            'file_path'    => $request->file_path,
            'storage'      => $request->storage,
            'duration'     => $request->duration,
            'file_type'    => $request->file_type,
            'downloadable' => $request->boolean('downloadable'),
            'is_preview'   => $request->boolean('is_preview'),
        ]);

        return response()->json([
            'message' => 'Lección actualizada correctamente.',
            'lesson'  => $lesson,
        ]);
    }

    public function destroyLesson(CourseChapterLesson $lesson): JsonResponse
    {
        $lesson->delete();

        return response()->json(['message' => 'Lección eliminada correctamente.']);
    }

    /* ─── Ordenamiento ─── */

    public function sortLessons(Request $request, CourseChapter $chapter): JsonResponse
    {
        $request->validate([
            'ids'   => ['required', 'array'],
            'ids.*' => ['integer'],
        ]);

        foreach ($request->ids as $index => $id) {
            CourseChapterLesson::where('id', $id)
                ->where('chapter_id', $chapter->id)
                ->update(['order' => $index + 1]);
        }

        return response()->json(['message' => 'Orden actualizado.']);
    }

    public function sortChapters(Request $request, Course $course): JsonResponse
    {
        $request->validate([
            'ids'   => ['required', 'array'],
            'ids.*' => ['integer'],
        ]);

        foreach ($request->ids as $index => $id) {
            CourseChapter::where('id', $id)
                ->where('course_id', $course->id)
                ->update(['order' => $index + 1]);
        }

        return response()->json(['message' => 'Orden de capítulos actualizado.']);
    }
}
