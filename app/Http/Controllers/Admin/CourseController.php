<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CourseStoreRequest;
use App\Http\Requests\Admin\CourseUpdateRequest;
use App\Mail\CourseApprovedMail;
use App\Mail\CourseRejectedMail;
use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\CourseLanguage;
use App\Models\CourseLevel;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Intervention\Image\Laravel\Facades\Image;

class CourseController extends Controller
{
    public function index(Request $request): View
    {
        $query = Course::with(['instructor', 'category', 'level', 'language']);

        // Filtro por estado de aprobación
        if ($request->filled('approval') && in_array($request->approval, ['pending', 'approved', 'rejected'])) {
            $query->where('is_approved', $request->approval);
        }

        // Búsqueda por título o instructor
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhereHas('instructor', fn ($iq) => $iq->where('name', 'like', "%{$search}%"));
            });
        }

        $courses = $query->latest()->paginate(15)->withQueryString();

        return view('admin.course.index', compact('courses'));
    }

    public function show(Course $course): View
    {
        $course->load(['instructor', 'category', 'level', 'language', 'chapters.lessons']);

        return view('admin.course.show', compact('course'));
    }

    public function updateApproval(Request $request, Course $course): JsonResponse
    {
        $request->validate([
            'is_approved' => ['required', 'in:pending,approved,rejected'],
        ]);

        $previousStatus = $course->is_approved;

        $course->update([
            'is_approved' => $request->is_approved,
        ]);

        // Enviar email al instructor si el estado cambió
        if ($previousStatus !== $request->is_approved && $course->instructor) {
            match ($request->is_approved) {
                'approved' => Mail::to($course->instructor->email)->queue(new CourseApprovedMail($course)),
                'rejected' => Mail::to($course->instructor->email)->queue(new CourseRejectedMail($course)),
                default    => null,
            };
        }

        $labels = ['pending' => 'Pendiente', 'approved' => 'Aprobado', 'rejected' => 'Rechazado'];

        return response()->json([
            'message' => "Curso marcado como {$labels[$request->is_approved]}.",
        ]);
    }

    public function create(): View
    {
        $instructors = User::where('role', 'instructor')
            ->where('approve_status', 'approved')
            ->orderBy('name')
            ->get(['id', 'name', 'email']);

        $categories = CourseCategory::whereNull('parent_id')
            ->with('subcategories')
            ->get();
        $levels    = CourseLevel::all();
        $languages = CourseLanguage::all();

        return view('admin.course.create', compact('instructors', 'categories', 'levels', 'languages'));
    }

    public function store(CourseStoreRequest $request): RedirectResponse
    {
        $thumbnailPath = null;

        if ($request->hasFile('thumbnail')) {
            Storage::disk('public')->makeDirectory('course');
            $filename = 'course/' . uniqid('crs_') . '.webp';
            Image::decode($request->file('thumbnail'))
                ->cover(600, 400)
                ->save(Storage::disk('public')->path($filename), 90);
            $thumbnailPath = $filename;
        }

        $slug = $this->uniqueSlug($request->title);

        $course = Course::create([
            'instructor_id'      => $request->instructor_id,
            'category_id'        => $request->category_id,
            'course_level_id'    => $request->course_level_id,
            'course_language_id' => $request->course_language_id,
            'title'              => $request->title,
            'slug'               => $slug,
            'seo_description'    => $request->seo_description,
            'thumbnail'          => $thumbnailPath,
            'demo_video_storage' => $request->demo_video_storage,
            'demo_video_source'  => $request->demo_video_source,
            'description'        => $request->description,
            'price'              => $request->price,
            'discount'           => $request->discount,
            'duration'           => $request->duration,
            'certificate'        => $request->boolean('certificate'),
            'qna'                => $request->boolean('qna'),
            'is_approved'        => 'approved',
            'status'             => $request->input('status', 'active'),
        ]);

        flash()->success('Curso creado. Ahora añade sus capítulos y lecciones.');

        return to_route('admin.courses.content', $course);
    }

    public function edit(Course $course): View
    {
        $course->load(['instructor', 'category', 'level', 'language']);

        $instructors = User::where('role', 'instructor')
            ->where('approve_status', 'approved')
            ->orderBy('name')
            ->get(['id', 'name', 'email']);

        $categories = CourseCategory::whereNull('parent_id')
            ->with('subcategories')
            ->get();
        $levels    = CourseLevel::all();
        $languages = CourseLanguage::all();

        return view('admin.course.edit', compact('course', 'instructors', 'categories', 'levels', 'languages'));
    }

    public function update(CourseUpdateRequest $request, Course $course): RedirectResponse
    {
        $data = [
            'instructor_id'      => $request->instructor_id,
            'category_id'        => $request->category_id,
            'course_level_id'    => $request->course_level_id,
            'course_language_id' => $request->course_language_id,
            'title'              => $request->title,
            'slug'               => $this->uniqueSlug($request->title, $course->id),
            'seo_description'    => $request->seo_description,
            'demo_video_storage' => $request->demo_video_storage,
            'demo_video_source'  => $request->demo_video_source,
            'description'        => $request->description,
            'price'              => $request->price,
            'discount'           => $request->discount,
            'duration'           => $request->duration,
            'certificate'        => $request->boolean('certificate'),
            'qna'                => $request->boolean('qna'),
            'status'             => $request->input('status', $course->status ?? 'active'),
        ];

        if ($request->hasFile('thumbnail')) {
            if ($course->thumbnail) {
                Storage::disk('public')->delete($course->thumbnail);
            }
            Storage::disk('public')->makeDirectory('course');
            $filename = 'course/' . uniqid('crs_') . '.webp';
            Image::decode($request->file('thumbnail'))
                ->cover(600, 400)
                ->save(Storage::disk('public')->path($filename), 90);
            $data['thumbnail'] = $filename;
        }

        $course->update($data);

        flash()->success('Curso actualizado correctamente.');

        return to_route('admin.courses.index');
    }

    public function destroy(Course $course): RedirectResponse
    {
        if ($course->thumbnail) {
            Storage::disk('public')->delete($course->thumbnail);
        }

        $course->delete();

        flash()->success('Curso eliminado correctamente.');

        return to_route('admin.courses.index');
    }

    private function uniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $slug     = Str::slug($title);
        $original = $slug;
        $counter  = 1;

        while (
            Course::where('slug', $slug)
                ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = $original . '-' . $counter++;
        }

        return $slug;
    }
}
