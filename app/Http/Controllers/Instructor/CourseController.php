<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Instructor\CourseStoreRequest;
use App\Http\Requests\Instructor\CourseUpdateRequest;
use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\CourseLanguage;
use App\Models\CourseLevel;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Intervention\Image\Laravel\Facades\Image;

class CourseController extends Controller
{
    public function index(): View
    {
        $courses = Course::where('instructor_id', Auth::id())
            ->latest()
            ->paginate(10);

        return view('instructor.course.index', compact('courses'));
    }

    public function create(): View
    {
        $categories = CourseCategory::whereNull('parent_id')
            ->with('subcategories')
            ->get();
        $levels = CourseLevel::all();
        $languages = CourseLanguage::all();

        return view('instructor.course.create', compact('categories', 'levels', 'languages'));
    }

    public function store(CourseStoreRequest $request): RedirectResponse
    {
        $thumbnailPath = null;

        if ($request->hasFile('thumbnail')) {
            Storage::disk('public')->makeDirectory('course');
            $filename = 'course/'.uniqid('crs_').'.webp';
            Image::decode($request->file('thumbnail'))
                ->cover(600, 400)
                ->save(Storage::disk('public')->path($filename), 90);
            $thumbnailPath = $filename;
        }

        $slug = $this->uniqueSlug($request->title);

        Course::create([
            'instructor_id' => Auth::id(),
            'category_id' => $request->category_id,
            'course_level_id' => $request->course_level_id,
            'course_language_id' => $request->course_language_id,
            'title' => $request->title,
            'slug' => $slug,
            'seo_description' => $request->seo_description,
            'thumbnail' => $thumbnailPath,
            'demo_video_storage' => $request->demo_video_storage,
            'demo_video_source' => $request->demo_video_source,
            'description' => $request->description,
            'price' => $request->price,
            'discount' => $request->discount,
            'duration' => $request->duration,
            'certificate' => $request->boolean('certificate'),
            'qna' => $request->boolean('qna'),
        ]);

        flash()->success('Curso creado correctamente.');

        return to_route('instructor.courses.index');
    }

    public function edit(Course $course): View
    {
        $this->authorize('update', $course);

        $categories = CourseCategory::whereNull('parent_id')
            ->with('subcategories')
            ->get();
        $levels = CourseLevel::all();
        $languages = CourseLanguage::all();

        return view('instructor.course.edit', compact('course', 'categories', 'levels', 'languages'));
    }

    public function update(CourseUpdateRequest $request, Course $course): RedirectResponse
    {
        $this->authorize('update', $course);

        $data = [
            'category_id' => $request->category_id,
            'course_level_id' => $request->course_level_id,
            'course_language_id' => $request->course_language_id,
            'title' => $request->title,
            'slug' => $this->uniqueSlug($request->title, $course->id),
            'seo_description' => $request->seo_description,
            'demo_video_storage' => $request->demo_video_storage,
            'demo_video_source' => $request->demo_video_source,
            'description' => $request->description,
            'price' => $request->price,
            'discount' => $request->discount,
            'duration' => $request->duration,
            'certificate' => $request->boolean('certificate'),
            'qna' => $request->boolean('qna'),
        ];

        if ($request->hasFile('thumbnail')) {
            if ($course->thumbnail) {
                Storage::disk('public')->delete($course->thumbnail);
            }
            Storage::disk('public')->makeDirectory('course');
            $filename = 'course/'.uniqid('crs_').'.webp';
            Image::decode($request->file('thumbnail'))
                ->cover(600, 400)
                ->save(Storage::disk('public')->path($filename), 90);
            $data['thumbnail'] = $filename;
        }

        $course->update($data);

        flash()->success('Curso actualizado correctamente.');

        return to_route('instructor.courses.index');
    }

    private function uniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $slug = Str::slug($title);
        $original = $slug;
        $counter = 1;

        while (
            Course::where('slug', $slug)
                ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = $original.'-'.$counter++;
        }

        return $slug;
    }
}
