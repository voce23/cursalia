<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Template;
use App\Models\TemplateCategory;
use App\Models\TemplateWaitlist;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

/**
 * CRUD admin del marketplace de plantillas.
 */
class TemplateController extends Controller
{
    public function index(): View
    {
        $templates = Template::query()
            ->with('category:id,name,color')
            ->withCount('waitlist')
            ->orderByDesc('is_featured')
            ->orderBy('sort_order')
            ->paginate(15);

        return view('admin.templates.index', compact('templates'));
    }

    public function create(): View
    {
        return view('admin.templates.form', [
            'template' => new Template(['is_free' => false, 'status' => 'draft', 'version' => '1.0.0']),
            'categories' => TemplateCategory::orderBy('sort_order')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateRequest($request);
        $data['slug'] = Str::slug($data['title']);
        $data['tech_stack'] = $this->splitMultiline($request->input('tech_stack_raw'));
        $data['features'] = $this->splitMultiline($request->input('features_raw'));

        if ($request->hasFile('thumbnail')) {
            $data['thumbnail'] = $this->saveImage($request->file('thumbnail'));
        }

        Template::create($data);

        return to_route('admin.templates.index')->with('success', 'Plantilla creada.');
    }

    public function edit(Template $template): View
    {
        return view('admin.templates.form', [
            'template' => $template,
            'categories' => TemplateCategory::orderBy('sort_order')->get(),
        ]);
    }

    public function update(Request $request, Template $template): RedirectResponse
    {
        $data = $this->validateRequest($request);
        if ($data['title'] !== $template->title) {
            $data['slug'] = Str::slug($data['title']);
        }
        $data['tech_stack'] = $this->splitMultiline($request->input('tech_stack_raw'));
        $data['features'] = $this->splitMultiline($request->input('features_raw'));

        if ($request->hasFile('thumbnail')) {
            if ($template->thumbnail) {
                Storage::disk('public')->delete($template->thumbnail);
            }
            $data['thumbnail'] = $this->saveImage($request->file('thumbnail'));
        }

        $template->update($data);

        return to_route('admin.templates.index')->with('success', 'Plantilla actualizada.');
    }

    public function destroy(Template $template): JsonResponse
    {
        if ($template->thumbnail) {
            Storage::disk('public')->delete($template->thumbnail);
        }
        $template->delete();

        return response()->json(['message' => 'Plantilla eliminada.']);
    }

    /** Vista de la lista de espera (waitlist). */
    public function waitlist(): View
    {
        $entries = TemplateWaitlist::query()
            ->with('template:id,title,slug')
            ->latest()
            ->paginate(30);

        $countByTemplate = Template::query()
            ->where('is_free', false)
            ->withCount('waitlist')
            ->orderByDesc('waitlist_count')
            ->get(['id', 'title', 'slug']);

        return view('admin.templates.waitlist', compact('entries', 'countByTemplate'));
    }

    private function validateRequest(Request $request): array
    {
        return $request->validate([
            'template_category_id' => ['nullable', 'exists:template_categories,id'],
            'title' => ['required', 'string', 'max:120'],
            'headline' => ['nullable', 'string', 'max:200'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'discount' => ['nullable', 'numeric', 'min:0'],
            'is_free' => ['nullable', 'boolean'],
            'demo_url' => ['nullable', 'url', 'max:255'],
            'download_url' => ['nullable', 'url', 'max:255'],
            'version' => ['required', 'string', 'max:20'],
            'status' => ['required', 'in:draft,published'],
            'is_featured' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'thumbnail' => ['nullable', 'image', 'max:4096'],
        ]) + [
            'is_free' => $request->boolean('is_free'),
            'is_featured' => $request->boolean('is_featured'),
        ];
    }

    private function splitMultiline(?string $raw): array
    {
        if (! $raw) {
            return [];
        }

        return collect(preg_split("/\r?\n/", trim($raw)))
            ->map(fn ($l) => trim($l))
            ->filter()
            ->values()
            ->all();
    }

    private function saveImage(UploadedFile $file): string
    {
        Storage::disk('public')->makeDirectory('templates');
        $name = 'templates/'.Str::random(10).'.'.$file->getClientOriginalExtension();
        Storage::disk('public')->put($name, file_get_contents($file->getRealPath()));

        return $name;
    }
}
