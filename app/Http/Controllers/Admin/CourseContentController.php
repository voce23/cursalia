<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseChapter;
use App\Models\CourseChapterLesson;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CourseContentController extends Controller
{
    /** Constructor de contenido: capítulos + lecciones del curso. */
    public function index(Course $course): View
    {
        $course->load([
            'chapters' => fn ($q) => $q->orderBy('order')->orderBy('id'),
            'chapters.lessons' => fn ($q) => $q->orderBy('order')->orderBy('id'),
        ]);

        return view('admin.course.content', compact('course'));
    }

    // ── Capítulos ──────────────────────────────────────────────────────────

    public function storeChapter(Request $request, Course $course): RedirectResponse
    {
        $data = $request->validate(['title' => ['required', 'string', 'max:255']]);

        $course->chapters()->create([
            'instructor_id' => $course->instructor_id,
            'title'         => $data['title'],
            'order'         => ($course->chapters()->max('order') ?? 0) + 1,
            'status'        => true,
        ]);

        return back()->with('success', 'Capítulo añadido.');
    }

    public function updateChapter(Request $request, CourseChapter $chapter): RedirectResponse
    {
        $data = $request->validate(['title' => ['required', 'string', 'max:255']]);
        $chapter->update($data);

        return back()->with('success', 'Capítulo actualizado.');
    }

    public function destroyChapter(CourseChapter $chapter): RedirectResponse
    {
        $chapter->lessons()->each(function ($lesson) {
            if ($lesson->storage === 'upload' && $lesson->file_path) {
                Storage::disk('public')->delete($lesson->file_path);
            }
            $lesson->delete();
        });
        $chapter->delete();

        return back()->with('success', 'Capítulo eliminado.');
    }

    public function moveChapter(CourseChapter $chapter, string $direction): RedirectResponse
    {
        $this->swapOrder(CourseChapter::where('course_id', $chapter->course_id), $chapter, $direction);

        return back();
    }

    // ── Lecciones ──────────────────────────────────────────────────────────

    public function storeLesson(Request $request, CourseChapter $chapter): RedirectResponse
    {
        $data = $this->validatedLesson($request);
        $data['file_path'] = $this->resolveFilePath($request, $data);

        $chapter->lessons()->create($data + [
            'course_id'     => $chapter->course_id,
            'instructor_id' => $chapter->instructor_id,
            'slug'          => Str::slug($data['title']).'-'.Str::random(5),
            'order'         => ($chapter->lessons()->max('order') ?? 0) + 1,
            'status'        => true,
        ]);

        return back()->with('success', 'Lección añadida.');
    }

    public function updateLesson(Request $request, CourseChapterLesson $lesson): RedirectResponse
    {
        $data = $this->validatedLesson($request);

        $newPath = $this->resolveFilePath($request, $data);
        if ($newPath !== null) {
            if ($lesson->storage === 'upload' && $lesson->file_path && $lesson->file_path !== $newPath) {
                Storage::disk('public')->delete($lesson->file_path);
            }
            $data['file_path'] = $newPath;
        } else {
            unset($data['file_path']);
        }

        $lesson->update($data);

        return back()->with('success', 'Lección actualizada.');
    }

    public function destroyLesson(CourseChapterLesson $lesson): RedirectResponse
    {
        if ($lesson->storage === 'upload' && $lesson->file_path) {
            Storage::disk('public')->delete($lesson->file_path);
        }
        $lesson->delete();

        return back()->with('success', 'Lección eliminada.');
    }

    public function moveLesson(CourseChapterLesson $lesson, string $direction): RedirectResponse
    {
        $this->swapOrder(CourseChapterLesson::where('chapter_id', $lesson->chapter_id), $lesson, $direction);

        return back();
    }

    // ──────────────────────────────────────────────────────────────────────

    private function validatedLesson(Request $request): array
    {
        $data = $request->validate([
            'title'        => ['required', 'string', 'max:255'],
            'storage'      => ['required', 'in:upload,youtube,vimeo,external_link'],
            'file_type'    => ['required', 'in:video,audio,doc,pdf,file'],
            'file_path'    => ['nullable', 'string', 'max:2000'],
            'file'         => ['nullable', 'file', 'max:204800'],   // 200 MB
            'duration'     => ['nullable', 'string', 'max:20'],
            'description'  => ['nullable', 'string'],
        ]);

        $data['is_preview']   = $request->boolean('is_preview');
        $data['downloadable'] = $request->boolean('downloadable');
        unset($data['file']);

        return $data;
    }

    /** file_path: sube el archivo si storage=upload (y hay archivo), o usa la URL. */
    private function resolveFilePath(Request $request, array $data): ?string
    {
        if ($data['storage'] === 'upload') {
            return $request->hasFile('file')
                ? $request->file('file')->store('lessons', 'public')
                : null;
        }

        return $request->input('file_path');
    }

    private function swapOrder($query, $model, string $direction): void
    {
        $neighbor = $direction === 'up'
            ? (clone $query)->where('order', '<', $model->order)->orderByDesc('order')->first()
            : (clone $query)->where('order', '>', $model->order)->orderBy('order')->first();

        if ($neighbor) {
            $tmp = $model->order;
            $model->update(['order' => $neighbor->order]);
            $neighbor->update(['order' => $tmp]);
        }
    }
}
